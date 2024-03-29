<?php

namespace App\Jobs;

use App\Models\Result;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

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
        // cek status code di result monitoring
        try {
            $start = microtime(true); // waktu mulai akses website
            $response = Http::get($this->monitoring->url);
            $end = microtime(true); // waktu mulai akses website
            $response_time = round($end - $start, 2); // waktu response
            $status = $response->status();
            $monitoring_id = $this->monitoring->id;

            // tampung data monitoring
            $monitoringData = [
                'response_time' => $response_time,
                'status_code' => $status,
                'monitoring_id' => $monitoring_id,
                'user_id' => $this->monitoring->user_id,
            ];

            // cek apakah kolom pause di tabel monitoring 1 (true) atau 0 (false)
            if ($this->monitoring->status == 'pause' and $this->monitoring->id == $monitoring_id) {
                return;
            }

            if ($response->successful()) {
                Result::create($monitoringData);
                // menghitung rata rata response_time per id dan simpan rata rata response time ke tabel monitoring
                $avg_response = Result::where('monitoring_id', $monitoring_id)->avg('response_time');
                $this->monitoring->update(['avg_response_time' => $avg_response]);
                return;
            }
            if ($this->try < $this->monitoring->tries) {
                $this->try++;
                dispatch(new CheckMonitoringJob($this->monitoring, $this->try));
                $this->monitoring::where('id', $monitoring_id)->update(['status' => 'not active']);
                Log::info('Percobaan ke ' . $this->try);
                return;
            }
            Result::create($monitoringData);
            // jalankan job kirim email
            dispatch(new SendMailJob($this->monitoring->email));
            Log::info('Berhasil mengirim email pemberitahuan');
            Log::info('Response time ' . $response_time . ' Status ' . $status);
        } catch (\Exception $e) {
            Log::error('Failed to find Result: ' . $e->getMessage());
        }
    }
}
