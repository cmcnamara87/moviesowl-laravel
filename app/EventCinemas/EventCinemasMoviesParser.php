<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 2/01/2015
 * Time: 3:44 PM
 */

namespace MoviesOwl\EventCinemas;


use Yangqi\Htmldom\Htmldom;
use Illuminate\Support\Facades\Log;
use MoviesOwl\EventCinemas\EventCinemasMovieParser;

/**
 * Class EventCinemasMoviesParser
 * @package MoviesOwl\EventCinemas
 */
class EventCinemasMoviesParser {

    protected $html;

    function __construct($html)
    {
        $this->html = new Htmldom($html);
    }


    /**
     * @param $html
     * @return array
     */
    public function getMovies() {
        return array_map(function($movieElement) {
            $movieParser = new EventCinemasMovieParser($movieElement);
            return $movieParser->getMovie();
        }, $this->getMovieElements());
    }

    /**
     * @param $html
     * @return mixed
     */
    private function getMovieElements()
    {
        $moviesElements = $this->html->find('.movie-list-item');
        return $moviesElements;
    }
}