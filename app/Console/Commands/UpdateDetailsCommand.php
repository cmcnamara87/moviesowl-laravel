<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Movies\MovieDetailsUpdater;

class UpdateDetailsCommand extends Command
{
    protected $movieDetailsUpdater;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get details for active movies';

    /**
     * Create a new command instance.
     *
     * @param EventCinemaUpdater $eventCinemaUpdater
     */
    public function __construct(MovieDetailsUpdater $movieDetailsUpdater)
    {
        parent::__construct();
        $this->movieDetailsUpdater = $movieDetailsUpdater;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles('php://stdout');
        $this->info('Updating details');
        $this->movieDetailsUpdater->updateAll();
    }
}
