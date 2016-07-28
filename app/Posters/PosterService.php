<?php
/**
 * Created by PhpStorm.
 * User: Kel
 * Date: 7/10/2015
 * Time: 6:17 PM
 */

namespace MoviesOwl\Posters;

use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\TMDB\TMDBApi;

class PosterService
{

    protected $omdbApi;
    protected $tmdbApi;


    function __construct(OMDBApi $omdbApi, TMDBApi $tmdbApi)
    {
        $this->omdbApi = $omdbApi;
        $this->tmdbApi = $tmdbApi;
    }


    public function getWidePosterUrl($imdbId) {
        $tmdbMovie = $this->tmdbApi->getMovieByImdbId($imdbId);

        if (!isset($tmdbMovie->backdrop_path)) {
            Log::info('No wide poster found');
            return null;
        }
        $widePosterUrl = "http://image.tmdb.org/t/p/w780".$tmdbMovie->backdrop_path;

        if ($widePosterUrl == "N/A") {
            Log::info('No wide poster available');
            return null;
        }
        return $widePosterUrl;
    }

    // get the imdb poster url
    // download a poster from any url save to disk
    // save poster interal url for movie

    public function getImdbPosterUrl($imdbId) {
        //$omdbMovie = $this->omdbApi->getMovieByImdbId($imdbId);

        $tmdbMovie = $this->tmdbApi->getMovieByImdbId($imdbId);
        if (!isset($tmdbMovie->poster_path)) {
            Log::info('No poster found');
            return null;
        }
        //$posterUrl = str_replace("SX300", "SX700", $omdbMovie->Poster);

        $posterUrl = "http://image.tmdb.org/t/p/w780".$tmdbMovie->poster_path;

        if ($posterUrl == "N/A") {
            Log::info('No poster available');
            return null;
        }
        return $posterUrl;
    }

    /**
     * Checks if a movie poster is already saved
     * @param $name
     * @return bool
     */
    public function exists($name) {
        $asset = $this->getAssetPath($name);
        $posterPath = public_path() . "/" . $asset;
        return file_exists($posterPath);
    }

    public function getAssetPath($name) {
        return "images/posters/" . snake_case(str_replace('/', '', $name)) . ".jpg";
    }

    public function savePosterFromUrl ($url, $name) {
        $asset = $this->getAssetPath($name);
        $posterPath = public_path() . "/" . $asset;
        if(!file_exists($posterPath)) {
            try {
                $img = Image::make($url);
                $img->save($posterPath);
                Log::info("---  Saved poster");
            } catch (Exception $e) {
                return null;
            }
        }
        return $asset;
    }




//    /**
//    get high resolution poster
//    Change poster url from rotten tomatoes to get higher resolution poster
//     **/
//    public function getHiResPosterUrl ($imdbId) {
//        if (!$imdbId) {
//            return "images/no_poster.jpg";
//        }
//        $asset = "images/posters/" . $imdbId . ".jpg";
//        $posterPath = public_path() . "/" . $asset;
//        if(!file_exists($posterPath)) {
//            $posterUrl = $this->getPosterUrl($imdbId);
//            if (!$posterUrl) {
//                return "images/no_poster.jpg";
//            }
//            try {
//                $img = Image::make($posterUrl);
//                $img->save($posterPath);
//                Log::info("---  Saved poster");
//            } catch (Exception $e) {
//                return "images/no_poster.jpg";
//            }
//        }
//        return $asset;
//    }
//
//    public function getPosterUrl($imdbId) {
//        $omdbMovie = $this->omdbApi->getMovieByImdbId($imdbId);
//        if (!isset($omdbMovie->Poster)) {
//            return null;
//        }
//        $posterUrl = str_replace("SX300", "SX700", $omdbMovie->Poster);
//        if ($posterUrl == "N/A") {
//            return null;
//        }
//        return $posterUrl;
//    }

}