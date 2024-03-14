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
        $start = microtime(true);
        $response = Http::get($this->monitoring['url']);
        $end = microtime(true);
        $response_time = round($end - $start, 2); // waktu response
        $status = $response->status(); // status kode
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
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
        $save = Result::create($monitoring);
        // $save_monitoring_status_code = Monitoring::find($monitoring_id);
        // $save_monitoring_status_code->status_code = $status;
        // $save_monitoring_status_code->save();
        if ($save) {
            Log::info("Response Time: {$response_time} seconds, Status: {$status}");
        } else {
            Log::info("Website Down!");
        }
    }
}
