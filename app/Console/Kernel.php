<?php

namespace MoviesOwl\Console;

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
        \MoviesOwl\Console\Commands\Inspire::class,
        \MoviesOwl\Console\Commands\ClearAllMoviesCommand::class,
        \MoviesOwl\Console\Commands\ClearMovieDetailsCommand::class,
        \MoviesOwl\Console\Commands\AddMissingImdbIdsCommand::class,
        \MoviesOwl\Console\Commands\UpdateDetailsCommand::class,
        'MoviesOwl\Console\Commands\LoadMoviesCommand'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();

        $schedule->command('movies:load')->dailyAt('00:30');
    }
}
