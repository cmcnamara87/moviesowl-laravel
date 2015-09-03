<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use MoviesOwl\EventCinemas\EventCinemasUpdater;

class LoadMoviesCommand extends Command
{
    private $eventCinemasUpdater;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:load';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @param EventCinemaUpdater $eventCinemaUpdater
     */
    public function __construct(EventCinemasUpdater $eventCinemasUpdater)
    {
        parent::__construct();
        $this->eventCinemasUpdater = $eventCinemasUpdater;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Running EventCinemas Update');
        $this->eventCinemasUpdater->update();
    }
}
