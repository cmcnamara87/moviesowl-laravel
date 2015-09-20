<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 2/01/2015
 * Time: 3:14 PM
 */

namespace MoviesOwl\EventCinemas;

use Carbon\Carbon;
use MoviesOwl\EventCinemas\EventCinemasApi;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
use MoviesOwl\Showings\Showing;
use Illuminate\Support\Facades\Log;
use MoviesOwl\OMDB\OMDBApi;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Console\Output\ConsoleOutput;

class EventCinemasUpdater {

    protected $eventCinemasApi;
    protected $rottenTomatoesApi;
    protected $omdbApi;
    protected $output;

    function __construct(EventCinemasApi $eventCinemasApi,
                         RottenTomatoesApi $rottenTomatoesApi,
                         OMDBApi $omdbApi, ConsoleOutput $output)
    {
        $this->eventCinemasApi = $eventCinemasApi;
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->omdbApi = $omdbApi;
        $this->output = $output;
    }

    public function update() {

        $this->output->writeln("Updating cinemas:");
        // Update cinemas list
        $eventCinemas = $this->eventCinemasApi->getCinemas('QLD');

        $cinemas = array_map(function($eventCinema) {
            return $this->getOrCreateCinema($eventCinema);
        }, $eventCinemas);

        foreach($cinemas as $cinema) {
            $this->updateMoviesAndShowings($cinema);
        }
    }

    function getOrCreateCinema($eventCinema)
    {
        $cinema = Cinema::where('location', $eventCinema->name)->first();
        if (!$cinema) {
            $cinema = Cinema::create([
                "location" => $eventCinema->name,
                "eventcinema_id" => $eventCinema->id
            ]);
        }
        return $cinema;
    }

    /**
     * @param $eventMovie
     * @return mixed
     */
    public function getOrCreateMovie($eventMovie)
    {
        $movie = Movie::firstOrCreate(array('title' => $eventMovie->title));

        $this->output->writeln("- " . $movie->title);

        if($this->hasRecentMovieInfo($movie)) {
            $this->output->writeln("-- Up to date");
            return $movie;
        }

        $this->output->writeln("-- Needs update " . $movie->updated_at->toDateTimeString() . ' ' . Carbon::today()->toDateTimeString());
        
        if($movie->rotten_tomatoes_id) {
            $rtMovie = $this->rottenTomatoesApi->getMovieById($movie->rotten_tomatoes_id);
        } else {
            $rtMovie = $this->rottenTomatoesApi->getMovie($eventMovie->title);
        }

        if(!$rtMovie) {
            return null;
        }
        $this->output->writeln("-- Rotten Tomatoes Match " . $rtMovie->title);

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
        if (!$imdbId) {
            return "images/no_poster.jpg";
        }
        $asset = "images/posters/" . $imdbId . ".jpg";
        $posterPath = public_path() . "/" . $asset;
        if(!file_exists($posterPath)) {
            $posterUrl = $this->getPosterUrl($imdbId);
            if (!$posterUrl) {
                return "images/no_poster.jpg";
            }
            try {
                $img = Image::make($posterUrl);
                $img->save($posterPath);
                $this->output->writeln("---  Saved poster");
            } catch (Exception $e) {
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
        $posterUrl = str_replace("SX300", "SX400", $omdbMovie->Poster);
        if ($posterUrl == "N/A") {
            return null;
        }
        return $posterUrl;
    }

    /**
     * @param EventCinemasSession $session
     * @param Cinema $cinema
     * @param Movie $movie
     * @return
     * @internal param $startTime
     */
    public function getOrCreateShowing(EventCinemasSession $session, Cinema $cinema, Movie $movie)
    {
        $showing = Showing::firstOrCreate(array('event_session_id' => $session->eventSessionId));
        $showing->fill([
            "movie_id" => $movie->id,
            "cinema_id" => $cinema->id,
            "start_time" => $session->startTime,
            "screen_type" => $session->type,
            "showing_type" => $session->sessionType,
            "tickets_url" => $session->ticketsUrl,
            "event_session_id" => $session->eventSessionId
        ]);
        $showing->save();
        return $showing;
    }

    /**
     * @param $cinema
     */
    public function updateMoviesAndShowings($cinema)
    {
        $this->output->writeln($cinema->location);
        $eventMovies = $this->eventCinemasApi->getMovies($cinema->eventcinema_id);

        foreach ($eventMovies as $eventMovie) {
            $movie = $this->getOrCreateMovie($eventMovie);
            if(!$movie) {
                continue;
            }
            foreach ($eventMovie->sessions as $session) {
                $this->getOrCreateShowing($session, $cinema, $movie);
            }
        }
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