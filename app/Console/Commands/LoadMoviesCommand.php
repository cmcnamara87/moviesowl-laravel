<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\GoogleMovies\GoogleMoviesUpdater;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\Movies\MovieDetailsUpdater;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
use MoviesOwl\Showings\Showing;
use Yangqi\Htmldom\Htmldom;

/**
 * Class LoadMoviesCommand
 * @package MoviesOwl\Console\Commands
 */
class LoadMoviesCommand extends Command
{
    private $eventCinemasUpdater;
    private $googleMoviesUpdater;
    private $movieDetailsUpdater;

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
    public function __construct(EventCinemasUpdater $eventCinemasUpdater,
                                GoogleMoviesUpdater $googleMoviesUpdater,
                                MovieDetailsUpdater $movieDetailsUpdater)
    {
        parent::__construct();
        $this->eventCinemasUpdater = $eventCinemasUpdater;
        $this->googleMoviesUpdater = $googleMoviesUpdater;
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
        $this->info('Running EventCinemas Update');
        $this->eventCinemasUpdater->update();
        $this->googleMoviesUpdater->update();
        $this->movieDetailsUpdater->updateAll();
    }
}
