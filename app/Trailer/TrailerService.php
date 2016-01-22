<?php

/**
 * Created by PhpStorm.
 * User: Kel
 * Date: 21/01/2016
 * Time: 6:47 PM
 */
namespace MoviesOwl\Trailer;

use Illuminate\Support\Facades\Log;
use MoviesOwl\TMDB\TMDBApi;

class TrailerService
{
    protected $tmdbApi;

    function __construct(TMDBApi $tmdbApi)
    {
        $this->tmdbApi = $tmdbApi;
    }


    public function getTrailerUrl ($imdbId)
    {
        Log::info('--- Loading Trailer Url');
        if (!$imdbId) {
            Log::info('---- No IMDB Id for trailer');
            return "";
        }

        $trailerResults = $this->tmdbApi->getMovieTrailerByImdbId($imdbId);

        if(!isset($trailerResults->results)){
            Log::info('---- No trailer found');
            return "";
        }
        //some has results: [] --> set but empty
        if(!$trailerResults->results){
            Log::info('---- No trailer found');
            return "";
        }

        $trailerObj = $trailerResults->results;
        return $trailerObj[0]->key;

    }


}