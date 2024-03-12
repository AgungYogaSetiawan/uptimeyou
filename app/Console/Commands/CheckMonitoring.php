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
        $monitoring = Monitoring::all()->first();
        $result_monitoring = Result::all()->last();
        // ambil waktu terakhir data result disimpan kemudian kurangi dengan waktu yang di inginkan dari monitoring data schedule
        $timestamp_result_monitoring = Carbon::parse($result_monitoring['created_at']);
        $timestamp_result_monitoring->subSeconds($monitoring['schedule']);
        if ($timestamp_result_monitoring > $result_monitoring['created_at']) {
            dispatch(new CheckMonitoringJob($monitoring));
        } else {
            Log::info('Continue');
        }
    }
}
