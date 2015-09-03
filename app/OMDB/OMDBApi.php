<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 4/01/2015
 * Time: 12:42 PM
 */

namespace MoviesOwl\OMDB;


class OMDBApi {

    public function getMovie($title) {
        $encodedTitle = urlencode($title);
        $url = "http://www.omdbapi.com/?t=$encodedTitle&plot=short&r=json&tomatoes=true";
        return json_decode(@file_get_contents($url));
    }

    public function getMovieByImdbId($id) {
        $url = "http://www.omdbapi.com/?i={$id}&plot=full&r=json";
        return json_decode(@file_get_contents($url));
    }
}