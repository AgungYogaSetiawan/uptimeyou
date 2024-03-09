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
    public $response_time;
    public $status;

    /**
     * Create a new job instance.
     */
    public function __construct($response_time, $status)
    {
        $this->response_time = $response_time;
        $this->status = $status;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Gunakan data yang diterima dari command di sini
        $data = [
            'response_time' => $this->response_time,
            'avg_response_time' => $this->response_time,
            'status_code' => $this->status,
            'monitoring_id' => 6,
            'user_id' => 1
        ];
        $save = Result::insert($data);
        if ($save) {
            Log::info("Response Time: {$this->response_time} seconds, Status: {$this->status}");
        } else {
            Log::info("Website Down!");
        }
    }
}
