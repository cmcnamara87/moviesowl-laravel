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

class PosterService
{



    protected $omdbApi;


    function __construct(OMDBApi $omdbApi)
    {
        $this->omdbApi = $omdbApi;
    }

    // get the imdb poster url
    // download a poster from any url save to disk
    // save poster interal url for movie


    public function getImdbPosterUrl($imdbId) {
        $omdbMovie = $this->omdbApi->getMovieByImdbId($imdbId);

        if (!isset($omdbMovie->Poster)) {
            Log::info('No poster found');
            return null;
        }
        $posterUrl = str_replace("SX300", "SX700", $omdbMovie->Poster);
        if ($posterUrl == "N/A") {
            Log::info('No poster available');
            return null;
        }
        return $posterUrl;
    }

    public function exists($name) {
        $asset = "images/posters/" . snake_case($name) . ".jpg";
        $posterPath = public_path() . "/" . $asset;
        return file_exists($posterPath);
    }

    public function savePosterFromUrl ($url, $name) {
        $asset = "images/posters/" . snake_case($name) . ".jpg";
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