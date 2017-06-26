<?php

namespace IAServer\Console;

use IAServer\Http\Controllers\Redis\RedisView;
use IAServer\Http\Requests\Request;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \IAServer\Console\Commands\Inspire::class,
        \IAServer\Console\Commands\LowLevelCommand::class,
        \IAServer\Console\Commands\initRawMaterials::class,
        \IAServer\Console\Commands\AMRCicle::class,
        \IAServer\Console\Commands\HeartBeatCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')->daily();

        $schedule->call(function () {
            Log::info("Tarea cada 5 minutos");
        })->everyFiveMinutes();

        $schedule->call(function () {
            Log::info("Tarea cada 10 minutos");
        })->everyTenMinutes();

       /* $schedule->call(function () {
            $rv = new RedisView();
            $exec = $rv->index();
        })->everyMinute();*/
    }
}
