<?php

namespace App\Jobs;

use App\Console\Commands\CheckMonitoring;
use App\Models\Monitoring;
use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckMonitoringJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $monitoring;
    public $try;

    /**
     * Create a new job instance.
     */
    public function __construct($monitoring, $try = 1)
    {
        $this->monitoring = $monitoring;
        $this->try = $try;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $monitoring_id = $this->monitoring->id;
        $user_id = $this->monitoring->user_id;

        // cek status code di result monitoring
        try {
            $start = microtime(true); // waktu mulai akses website
            $response = Http::get($this->monitoring->url);
            $end = microtime(true); // waktu mulai akses website
            $response_time = round($end - $start, 2); // waktu response
            $status = $response->status();

            // tampung data monitoring
            $monitoringData = [
                'response_time' => $response_time,
                'status_code' => $status,
                'monitoring_id' => $monitoring_id,
                'user_id' => $user_id,
            ];

            // cek response berhasil atau tidak
            if ($response->successful()) {
                Result::create($monitoringData); // simpan ke tabel result
                // menghitung rata rata response_time per id
                $avg_response = Result::where('monitoring_id', $monitoring_id)->sum('response_time') / Result::where('monitoring_id', $monitoring_id)->count();
                // simpan rata rata response time ke tabel monitoring
                $this->monitoring->avg_response_time = $avg_response;
                $this->monitoring->save();
                Log::info('Response time ' . $response_time . ' Status ' . $status);
            } else {
                if ($this->try < $this->monitoring->tries) { // jika try masih kurang dengan tries yang diminta maka ulangi lagi
                    $this->try++;
                    dispatch(new CheckMonitoringJob($this->monitoring, $this->try));
                    Log::info('Percobaan ke ' . $this->try);
                } else { // jika maksimal try terpenuhi maka jalankan blok ini
                    Result::create($monitoringData); // simpan ke tabel result
                    // menghitung rata rata response_time per id
                    $avg_response = Result::where('monitoring_id', $monitoring_id)->sum('response_time') / Result::where('monitoring_id', $monitoring_id)->count();
                    // simpan rata rata response time ke tabel monitoring
                    $this->monitoring->avg_response_time = $avg_response;
                    $this->monitoring->save();
                    // akan menjalankan job kirim email
                    Log::info('Percobaan sudah habis, pesan akan dikirim ke email');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to find Result: ' . $e->getMessage());
        }
    }
}
