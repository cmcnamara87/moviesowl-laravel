<?php

/**
 * Created by PhpStorm.
 * User: Kel
 * Date: 21/01/2016
 * Time: 12:34 PM
 */

namespace MoviesOwl\TMDB;


class TMDBApi
{
    public function getMovieByImdbId($id) {
        $url = "http://api.themoviedb.org/3/movie/{$id}?api_key=05629307398e2d06e10c5f0b99fd7b38";
        return json_decode(@file_get_contents($url));
    }
}