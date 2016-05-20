<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinema21\Cinema21Updater;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\Fandango\FandangoUpdater;
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
    private $fandangoUpdater;
    private $cinema21Updater;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:load {day=tomorrow}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get showings from sources';

    /**
     * Create a new command instance.
     *
     * @param EventCinemaUpdater $eventCinemaUpdater
     */
    public function __construct(EventCinemasUpdater $eventCinemasUpdater,
                                GoogleMoviesUpdater $googleMoviesUpdater,
                                MovieDetailsUpdater $movieDetailsUpdater,
                                FandangoUpdater $fandangoUpdater,
                                Cinema21Updater $cinema21Updater)
    {
        parent::__construct();
        $this->eventCinemasUpdater = $eventCinemasUpdater;
        $this->googleMoviesUpdater = $googleMoviesUpdater;
        $this->movieDetailsUpdater = $movieDetailsUpdater;
        $this->fandangoUpdater = $fandangoUpdater;
        $this->cinema21Updater = $cinema21Updater;
    }

    // https://tickets.fandango.com/transaction/ticketing/seatpicker/Default.aspx?tid=AACWX&t=18:30&best_availability=1&mid=185792&quantity=1&action=availability&sdate=05/10/2016&_=1462927932673

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles('php://stdout');
        $this->info('Running Cinema Update');

        $day = $this->argument('day');

        $this->info('Deleting sessions that are 2 weeks old');
        $before = Carbon::today()->subDays(9);
        Showing::where('start_time', ' <= ', $before->toDateTimeString())
            ->delete();

        $this->info('Syncing sessions');
        // Load sessions
        $this->eventCinemasUpdater->update($day);
        $this->googleMoviesUpdater->update($day);
        $this->fandangoUpdater->update($day);
        $this->cinema21Updater->update($day);
        $this->movieDetailsUpdater->updateAll($day);
    }
}
