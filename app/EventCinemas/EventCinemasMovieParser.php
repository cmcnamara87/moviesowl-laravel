<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 2/01/2015
 * Time: 4:03 PM
 */

namespace MoviesOwl\EventCinemas;

use Illuminate\Support\Facades\Log;

class EventCinemasMovieParser {

    protected $movieElement;

    function __construct($movieElement)
    {
        $this->movieElement = $movieElement;
    }

    public function getMovie() {
        return new EventCinemasMovie($this->getMovieTitle(), $this->getSessions());
    }

    /**
     * @param $movieElements
     * @return array
     */
    public function getSessions()
    {
        return array_map(function($sessionElement) {
            $sessionParser = new EventCinemasSessionParser($sessionElement);
            return $sessionParser->getSession();
        }, $this->getSessionElements());
    }


    /**
     * @param $movieElement
     * @return String
     */
    public function getMovieTitle()
    {
        $movieTitle = $this->movieElement->find('strong.title', 0)->plaintext;
        return html_entity_decode($movieTitle);
    }

    /**
     * @param $movieElements
     * @return mixed
     */
    private function getSessionElements()
    {
        $sessionElements = $this->movieElement->find('.btn');
        return $sessionElements;
    }


}