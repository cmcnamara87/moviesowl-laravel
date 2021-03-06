<?php
/**
 * Created by PhpStorm.
 * User: Kel
 * Date: 22/01/2016
 * Time: 10:49 AM
 */

namespace MoviesOwl\RottenTomatoes;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maknz\Slack\Facades\Slack;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\TMDB\TMDBApi;
use MoviesOwl\Trailer\TrailerService;


use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\GoogleMovies\GoogleMoviesUpdater;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use Illuminate\Console\Command;
use Yangqi\Htmldom\Htmldom;
class RottenTomatoesService
{

    private $rottenTomatoesApi;
    private $omdbApi;
    private $tmdbApi;
    private $posterService;
    private $trailerService;

    function __construct(RottenTomatoesApi $rottenTomatoesApi,
                         OMDBApi $omdbApi, TMDBApi $tmdbApi,
                         PosterService $posterService,
                         TrailerService $trailerService)
    {
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->omdbApi = $omdbApi;
        $this->tmdbApi = $tmdbApi;
        $this->posterService = $posterService;
        $this->trailerService = $trailerService;
    }


    public function updateMovie($movie) {
        Log::info("- " . $movie->title);

        if(!$movie->details) {
            Log::info('No defaults, creating default');
            $movie->details()->create([
                "title" => $movie->title,
                "synopsis" => "No Synopsis",
                "run_time" => "0",
                "director" => "",
                "cast" => "",
                "poster" => "images/no_poster.jpg",
                "tomato_meter" => -1,
                "genre" => "",
                "movie_id" => $movie->id
            ]);
            $movie = Movie::find($movie->id);
        }
        Log::info("-- Needs update " . $movie->updated_at->toDateTimeString() . ' ' . Carbon::today()->toDateTimeString());

        if($movie->rotten_tomatoes_id) {
            $rtMovie = $this->rottenTomatoesApi->getMovieById($movie->rotten_tomatoes_id);
        } else {
            $rtMovie = $this->rottenTomatoesApi->getMovie($movie->title);
        }

        if(!$rtMovie) {
            Log::info('No RT movie found, adding default');
            Slack::send('Failed to find Rotten Tomatoes ' . $movie->title);
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
        $movie->details->fill([
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
            "trailer" => $this->trailerService->getTrailerUrl($movie->imdb_id),
            "poster" => $this->getHiResPosterUrl($movie->imdb_id, $movie->title),
            "wide_poster" => $this->getWidePosterUrl($movie->imdb_id, $movie->title),
            "tomato_meter" => $rtMovie->ratings->critics_score,
            "genre" => array_reduce($rtMovie->genres, function($carry, $genres) {
                if(strlen($carry)) {
                    $carry .= ', ';
                }
                return $carry . $genres;
            }, ""),
        ]);
        $movie->details->save();
        return $movie;
    }


    private function getWidePosterUrl ($imdbId, $movieTitle) {
        Log::info('--- Loading wide poster');
        if (!$imdbId) {
            Log::info('---- No IMDB Id');
            return "images/no_poster.jpg";
        }
        $url = $this->posterService->getWidePosterUrl($imdbId);

        if (!$url) {
            Log::info('---- No OMDB Poster');
            return "images/no_poster.jpg";
        }
        $asset = $this->posterService->savePosterFromUrl($url, $movieTitle."-wide");


        if(!$asset) {
            Log::info('---- Saving failed');
            return "images/no_poster.jpg";
        }
        Log::info('---- wide poster Saved');
        return $asset;
    }

    private function getHiResPosterUrl ($imdbId, $movieTitle) {
        Log::info('--- Loading poster');
        if($this->posterService->exists($movieTitle)) {
            // return it already
            $this->posterService->getAssetPath($movieTitle);
        }
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
//        if (!isset($rtMovie->alternate_ids)) {
//            return null;
//        }
//        if (!isset($rtMovie->alternate_ids->imdb)) {
//            return null;
//        }
//        return 'tt' . $rtMovie->alternate_ids->imdb;

        Log::info('---- Getting Imdb id');
        $title = urlencode($this->rottenTomatoesApi->removePunctuation($rtMovie->title));
        $searchResults = $this->tmdbApi->searchTmdbMovieByTitle($title);

        if(isset($searchResults->results)){
            $results = $searchResults->results;
        }
        else{
            Slack::send('Failed to find IMDB ' . $rtMovie->title);
            return null;
        }
        if(count($results) == 0){
            Slack::send('Failed to find IMDB ' . $rtMovie->title);
            return null;
        }

        $tmdbMovieId = $results[0]->id;

        $tmdbMovie = $this->tmdbApi->getMovieByImdbId($tmdbMovieId);
        $imdbId = $tmdbMovie->imdb_id;
        sleep(1);
        return $imdbId;

   }
}