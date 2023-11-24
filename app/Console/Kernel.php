<?php

namespace App\Console;

use App\Http\Controllers\MaWaAPIController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /*$schedule->command('adldap:import', [
            '--no-interaction',
            '--restore',
            '--delete',
            '--filter' => '(objectclass=user)',
        ])->everyMinute();*/

        $schedule->call(function () {
            $mawa = new MaWaAPIController();
            $mawa->upsertMawaPersons($mawa->getAllMaWaVisitors());
        })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
