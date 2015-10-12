<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\OMDB\OMDBApi;
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
    private $rottenTomatoesApi;
    private $omdbApi;

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
                                RottenTomatoesApi $rottenTomatoesApi,
                                OMDBApi $omdbApi)
    {
        parent::__construct();
        $this->eventCinemasUpdater = $eventCinemasUpdater;
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->omdbApi = $omdbApi;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Running EventCinemas Update');
//        $this->eventCinemasUpdater->update();
        $this->updateFromGoogleMovies();
        $this->updateTodaysMovies();
    }

    public function updateFromGoogleMovies()
    {
        Log::info('Updating from Google Movies for Brisbane');

        // get from google movies for brisbane
        $url = "http://www.google.com/movies?near=brisbane+australia";
        $googleMovies = @file_get_contents($url);
        $html = new Htmldom($googleMovies);

        // Find all article blocks
        foreach ($html->find('.theater') as $cinemaElement) {
            $cinemaName = $cinemaElement->find('h2.name a', 0)->plaintext;

            $cinema = Cinema::firstOrCreate([
                'location' => $cinemaName
            ]);
            Log::info('Cinema: ' . $cinemaName);
            foreach ($cinemaElement->find('.movie') as $movieElement) {
                $movie = Movie::firstOrCreate([
                    'title' => $movieElement->find('.name a', 0)->plaintext
                ]);
                Log::info('Movie: ' . $movie->title);

                $isPm = false;
                foreach (array_reverse($movieElement->find('.times span[style^="color:"]')) as $showingElement) {
                    $timeString = $showingElement->plaintext;
                    $parts = explode(':', $timeString);

                    if (strpos($timeString, 'pm') != -1) {
                        $isPm = true;
                    }
                    if (strpos($timeString, 'am') != -1) {
                        $isPm = false;
                    }
                    $hours = intval(preg_replace("/[^0-9 ]/", '', $parts[0]));
                    if ($isPm && $hours != 12) {
                        $hours += 12;
                    }
                    $minutes = intval($parts[1]);
                    $timestamp = Carbon::today()->timestamp + ($hours * 60 * 60) + ($minutes * 60);
                    $startTime = Carbon::createFromTimestamp($timestamp);

                    Log::info('Session: ' . $startTime->toDateTimeString());
                    $showing = Showing::firstOrCreate([
                        "movie_id" => $movie->id,
                        "cinema_id" => $cinema->id,
                        "start_time" => $timestamp
                    ]);
                }
            }
        }
    }

    public function updateTodaysMovies() {
        Log::info('Updating Movie Details');
        $startOfDay = Carbon::today();
        $endOfDay = $startOfDay->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startOfDay->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)->get();

        foreach($movies as $movie) {
            $this->updateDetailsForMovie($movie);
            sleep(1);
        }
    }

    public function updateDetailsForMovie($movie) {
        Log::info("- " . $movie->title);

        if($this->hasRecentMovieInfo($movie)) {
            Log::info("-- Up to date");
            return $movie;
        }

        Log::info("-- Needs update " . $movie->updated_at->toDateTimeString() . ' ' . Carbon::today()->toDateTimeString());

        if($movie->rotten_tomatoes_id) {
            $rtMovie = $this->rottenTomatoesApi->getMovieById($movie->rotten_tomatoes_id);
        } else {
            $rtMovie = $this->rottenTomatoesApi->getMovie($movie->title);
        }

        if(!$rtMovie) {
            return null;
        }
        Log::info("-- Rotten Tomatoes Match " . $rtMovie->title);

        $movie->tomato_meter = $rtMovie->ratings->critics_score;
        $movie->rotten_tomatoes_id = $rtMovie->id;
        if (!$movie->imdb_id) {
            $movie->imdb_id = $this->getImdbIdFromRtmovie($rtMovie);
        }
        $movie->save();

        if (isset($rtMovie->abridged_directors)) {
            $abridged_directors = $rtMovie->abridged_directors;
        }
        else {
            $abridged_directors = [];
        }

        Log::info("-- Updating details now " . $rtMovie->title);
        $movieDetails = MovieDetails::firstOrCreate(array('movie_id' => $movie->id));
        $movieDetails->fill([
            "title" => $rtMovie->title,
            "synopsis" => $rtMovie->synopsis,
            "run_time" => $rtMovie->runtime,
            "director" => array_reduce($abridged_directors, function($carry, $directors) {
                if(strlen($carry)) {
                    $carry .= ', ';
                }
                return $carry . $directors->name;
            }, ""),
            "cast" => array_reduce($rtMovie->abridged_cast, function($carry, $castMember) {
                if(strlen($carry)) {
                    $carry .= ', ';
                }
                return $carry . $castMember->name;
            }, ""),
            "poster" => $this->getHiResPosterUrl($movie->imdb_id),
            "tomato_meter" => $rtMovie->ratings->critics_score,
            "genre" => array_reduce($rtMovie->genres, function($carry, $genres) {
                if(strlen($carry)) {
                    $carry .= ', ';
                }
                return $carry . $genres;
            }, ""),
        ]);

        $movieDetails->save();
        // Need to touch in case there was no change in data
        $movieDetails->touch();

        return $movie;
    }

    public function getImdbIdFromRtmovie ($rtMovie) {
        if (!isset($rtMovie->alternate_ids)) {
            return null;
        }
        if (!isset($rtMovie->alternate_ids->imdb)) {
            return null;
        }
        return $rtMovie->alternate_ids->imdb;
    }

    /**
    get high resolution poster
    Change poster url from rotten tomatoes to get higher resolution poster
     **/
    public function getHiResPosterUrl ($imdbId) {
        Log::info('--- Loading poster');
        if (!$imdbId) {
            Log::info('---- No IMDB Id for poster');
            return "images/no_poster.jpg";
        }
        $asset = "images/posters/" . $imdbId . ".jpg";
        $posterPath = public_path() . "/" . $asset;
        if(!file_exists($posterPath)) {
            $posterUrl = $this->getPosterUrl($imdbId);
            if (!$posterUrl) {
                Log::info('---- No OMDB for poster');
                return "images/no_poster.jpg";
            }
            try {
                $img = Image::make($posterUrl);
                $img->save($posterPath);
                Log::info("----  Saved poster");
            } catch (Exception $e) {
                Log::info('---- Failed to save poster ' . $e);
                return "images/no_poster.jpg";
            }
        }
        return $asset;
    }

    public function getPosterUrl($imdbId) {
        $omdbMovie = $this->omdbApi->getMovieByImdbId("tt" . $imdbId);
        if (!isset($omdbMovie->Poster)) {
            return null;
        }
        $posterUrl = str_replace("SX300", "SX700", $omdbMovie->Poster);
        if ($posterUrl == "N/A") {
            return null;
        }
        return $posterUrl;
    }

    /**
     * @param $movie
     * @return mixed
     */
    public function hasRecentMovieInfo($movie)
    {
        // Is it new?
        if (!$movie->details) {
            return false;
        }
        return $this->wasUpdatedToday($movie->details);
    }

    /**
     * @param $movie
     * @return bool
     */
    public function isJustCreated($movie)
    {
        return $movie->updated_at->eq($movie->created_at);
    }

    /**
     * @param $movie
     * @return mixed
     */
    public function wasUpdatedToday($movieDetails)
    {
        return $movieDetails->updated_at->gte(Carbon::today());
    }
}
