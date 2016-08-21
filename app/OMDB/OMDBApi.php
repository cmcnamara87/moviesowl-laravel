<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 4/01/2015
 * Time: 12:42 PM
 */

namespace MoviesOwl\OMDB;


class OMDBApi {

    public function getMovies($title) {
        $encodedTitle = urlencode($this->getCleanMovieTitle($title));
        $url = "http://www.omdbapi.com/?s=$encodedTitle&r=json&type=movie";
        return json_decode(@file_get_contents($url));
    }
    public function getMovie($title) {
        $encodedTitle = urlencode($title);
        $url = "http://www.omdbapi.com/?t=$encodedTitle&plot=short&r=json&tomatoes=true";
        return json_decode(@file_get_contents($url));
    }

    public function getMovieByImdbId($id) {
        $url = "http://www.omdbapi.com/?i={$id}&plot=full&r=json";
        return json_decode(@file_get_contents($url));
    }

    /**
     * @param $movie
     * @return mixed
     */
    private function getCleanMovieTitle($movieTitle)
    {
        $movieTitle = preg_replace("/[^A-Za-z0-9 ]/", '', $movieTitle);
        $movieTitle = str_replace('3D', '', $movieTitle);
        $movieTitle = str_replace('2D', '', $movieTitle);
        $movieTitle = str_replace('Babes in Arms', '', $movieTitle);
        $movieTitle = str_replace('and', '', $movieTitle);
        Log::info(' stripped movie title: ' . $movieTitle);
        return $movieTitle;
    }
}