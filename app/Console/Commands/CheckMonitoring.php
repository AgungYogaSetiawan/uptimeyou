<?php

namespace App\Console\Commands;

use App\Jobs\CheckMonitoringJob;
use App\Models\Monitoring;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CheckMonitoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:monit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Mengambil data ke Database
        $monitoring = Monitoring::all();
        // Lakukan HTTP request ke website yang ingin dimonitor
        $response = Http::get($monitoring['url']);

        try {
            foreach ($response as $resp) {
                $response_time = round(microtime(true) - LARAVEL_START, 2);
                $status = $response->status();
                Log::info("Respon $response_time Second dan Status $status");
                // Masukkan pekerjaan ke dalam antrian dengan data yang diperlukan
                dispatch(new CheckMonitoringJob($response_time, $status));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
