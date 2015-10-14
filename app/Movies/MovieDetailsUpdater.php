<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 13/10/15
 * Time: 12:48 AM
 */

namespace MoviesOwl\Movies;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\GoogleMovies\GoogleMoviesUpdater;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
use MoviesOwl\Showings\Showing;
use Yangqi\Htmldom\Htmldom;

class MovieDetailsUpdater {

    private $rottenTomatoesApi;
    private $omdbApi;
    private $posterService;

    function __construct(RottenTomatoesApi $rottenTomatoesApi,
                         OMDBApi $omdbApi,
                         PosterService $posterService)
    {
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->omdbApi = $omdbApi;
        $this->posterService = $posterService;
    }

    public function updateAll() {
        Log::info('Updating Movie Details For Today');
        $startOfDay = Carbon::today();
        $endOfDay = $startOfDay->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startOfDay->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)->get();

        foreach($movies as $movie) {
            $this->updateMovie($movie);
            sleep(1);
        }
    }

    public function updateMovie($movie) {
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

            $movieDetails = MovieDetails::firstOrCreate(array('movie_id' => $movie->id));
            $movieDetails->fill([
                "title" => $movie->title,
                "synopsis" => "No Synopsis",
                "run_time" => "0",
                "director" => "",
                "cast" => "",
                "poster" => $this->getHiResPosterUrl($movie->imdb_id, $movie->title),
                "tomato_meter" => 0,
                "genre" => "",
            ]);

            $movieDetails->save();
            return $movie;
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
            "poster" => $this->getHiResPosterUrl($movie->imdb_id, $movie->title),
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

    private function getHiResPosterUrl ($imdbId, $movieTitle) {
        Log::info('--- Loading poster');
        if (!$imdbId) {
            Log::info('---- No IMDB Id');
            return "images/no_poster.jpg";
        }
        $url = $this->posterService->getImdbPosterUrl($imdbId);
        if (!$url) {
            Log::info('---- No OMDB Poster');
            return "images/no_poster.jpg";
        }
        $asset = $this->posterService->savePosterFromUrl($url, $movieTitle);

        if(!$asset) {
            Log::info('---- Saving failed');
            return "images/no_poster.jpg";
        }
        Log::info('---- Saved');
        return $asset;
    }


    private function getImdbIdFromRtmovie ($rtMovie) {
        if (!isset($rtMovie->alternate_ids)) {
            return null;
        }
        if (!isset($rtMovie->alternate_ids->imdb)) {
            return null;
        }
        return 'tt' . $rtMovie->alternate_ids->imdb;
    }

    /**
     * @param $movie
     * @return mixed
     */
    private function hasRecentMovieInfo($movie)
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
    private function isJustCreated($movie)
    {
        return $movie->updated_at->eq($movie->created_at);
    }

    /**
     * @param $movie
     * @return mixed
     */
    private function wasUpdatedToday($movieDetails)
    {
        return $movieDetails->updated_at->gte(Carbon::today());
    }
}