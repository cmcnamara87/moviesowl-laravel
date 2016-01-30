<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 4/01/2015
 * Time: 4:33 PM
 */

namespace MoviesOwl\RottenTomatoes;
use Illuminate\Support\Facades\Log;

class RottenTomatoesApi {


    protected $apiKey = "akr6ay2seh8r2uhdjfdrrwm4";
    public function getMovies($title) {
        $title = urlencode($this->removePunctuation($title));
        $url = "http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey={$this->apiKey}&q=$title";
        $movies = json_decode(@file_get_contents($url));
        return $movies;
    }
    public function getMovie($title) {
        $title = urlencode($this->removePunctuation($title));
        $url = "http://api.rottentomatoes.com/api/public/v1.0/movies.json?apikey={$this->apiKey}&q=$title&page_limit=1";
        Log::info("--- URL " . $url);
        $movies = json_decode(@file_get_contents($url));
        if(!isset($movies->movies) || !$movies->movies) {
            Log::warning("Failed to get rotten tomatoes for $title");
            return null;
        }
        usleep(250);
        $movie = $this->getMovieById($movies->movies[0]->id);
        return $movie;
    }

    public function getMovieById ($id) {
        $url = "http://api.rottentomatoes.com/api/public/v1.0/movies/{$id}.json?apikey={$this->apiKey}";
        $movie = json_decode(@file_get_contents($url));
        Log::info("--- URL " . $url);
        return $movie;
    }


    /**
     * @param $title
     * @return mixed
     */
    public function removePunctuation($title) {
        $title = trim(preg_replace("/[^.a-zA-Z 0-9]+/", " ", $title));
        return $title;
    }
}