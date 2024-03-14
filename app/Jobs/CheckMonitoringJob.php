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

    /**
     * Create a new job instance.
     */
    public function __construct($monitoring)
    {
        $this->monitoring = $monitoring;
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
        $arr_success = range(200, 299);
        // Menyimpan hasil monitoring ke tabel result monitoring
        $monitoring = [
            'response_time' => $response_time,
            'status_code' => $status,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'monitoring_id' => $monitoring_id,
            'user_id' => $user_id,
        ];
        $save_result = Result::create($monitoring);

        // menghitung rata rata response_time per id
        $avg_response = Result::where('monitoring_id', $monitoring_id)->sum('response_time') / Result::where('monitoring_id', $monitoring_id)->count();
        // simpan rata rata response time ke tabel monitoring
        $monitor_id = Monitoring::findOrFail($monitoring_id);
        $monitor_id->avg_response_time = $avg_response;
        $monitor_id->save();

        // cek status code di result monitoring
        try {
            $getResult = Result::findOrFail($save_result->id);
            if (!in_array($getResult->status_code, $arr_success)) {
                // lakukan pengulangan sebanyak tries yang di input user
                for ($i = 0; $i < $monitor_id->tries; $i++) {
                    dispatch(new CheckMonitoringJob($this->monitoring));
                }
                if (!in_array($getResult->status_code, $arr_success)) {
                    // jika masih gagal status code nya maka kirim email job
                    Log::info('Pesan akan dikirim melalui email job');
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to find Result: ' . $e->getMessage());
        }
    }
}
