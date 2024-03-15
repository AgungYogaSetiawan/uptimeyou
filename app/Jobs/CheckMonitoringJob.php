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
        // Lakukan HTTP request ke website yang ingin dimonitoring
        $start = microtime(true); // waktu mulai akses website
        $response = Http::get($this->monitoring['url']);
        $end = microtime(true); // waktu selesai akses website
        $response_time = round($end - $start, 2); // waktu response
        $status = $response->status(); // status kode
        $created_at = now();
        $updated_at = now();
        $monitoring_id = $this->monitoring['id'];
        $user_id = $this->monitoring['user_id'];
        // Menyimpan hasil monitoring ke tabel result monitoring
        $monitoring = [
            'response_time' => $response_time,
            'status_code' => $status,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'monitoring_id' => $monitoring_id,
            'user_id' => $user_id,
        ];
        Result::create($monitoring);

        // menghitung rata rata response_time per id
        $avg_response = Result::where('monitoring_id', $monitoring_id)->sum('response_time') / Result::where('monitoring_id', $monitoring_id)->count();
        // simpan rata rata response time ke tabel monitoring
        $monitor_id = Monitoring::findOrFail($monitoring_id);
        $monitor_id->avg_response_time = $avg_response;
        $monitor_id->save();

        // cek status code di result monitoring
        try {
            // cek response
            if ($response->successful()) {
                // response berhasil
                Log::info('Response time ' . $response_time . ' Status ' . $status);
            } else { // jika response gagal
                // jika try kurang dari sama dengan tries di tabel monitoring
                if ($this->try < $monitor_id->tries) {
                    $this->try++;
                    dispatch(new CheckMonitoringJob($this->monitoring, $this->try));
                    Log::info('Percobaan ke ' . $this->try);
                } else { // max percobaan
                    Log::info('Percobaan sudah habis, pesan akan dikirim ke email');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to find Result: ' . $e->getMessage());
        }
    }
}
