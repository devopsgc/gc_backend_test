<?php

namespace App\Console;

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
        $schedule->command('deck:ppt')->hourly();
        $schedule->command('crawl:facebook')->everyMinute();
        $schedule->command('crawl:instagram')->everyMinute();
        $schedule->command('crawl:youtube')->everyMinute();
        $schedule->command('crawl:twitter')->everyMinute();
        $schedule->command('crawl:tiktok')->everyMinute();
        $schedule->command('crawl:weibo')->everyMinute();
        $schedule->command('crawl:xiaohongshu')->everyMinute();
        $schedule->command('notify:users-campaign-completed')->daily();
        $schedule->command('socialdata:generate-report')->weeklyOn(6, '16:00'); // sun 00:00 am
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
