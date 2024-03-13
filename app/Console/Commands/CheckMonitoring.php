<?php

namespace App\Console\Commands;

use App\Jobs\CheckMonitoringJob;
use App\Models\Monitoring;
use App\Models\Result;
use Carbon\Carbon;
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
        $monitorings = Monitoring::all(); // Ambil data monitoring pertama

        // Ambil waktu terakhir data result disimpan
        $current_time = now();

        foreach ($monitorings as $monitoring) {
            $result_monitoring = Result::where('monitoring_id', $monitoring->id)->latest()->first();
            if ($result_monitoring) {
                dispatch(new CheckMonitoringJob($monitoring));
                continue;
            }
            if ($current_time->diffInSeconds($result_monitoring->created_at) > $monitoring->schedule) {
                // Jika waktu terakhir hasil monitoring melebihi jadwal monitoring, kirim data monitoring ke pekerjaan
                dispatch(new CheckMonitoringJob($monitoring));
            }
        }
    }
}
