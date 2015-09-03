<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 3/01/2015
 * Time: 2:36 PM
 */

namespace MoviesOwl\EventCinemas;


class EventCinemasCinema {

    public $name;
    public $id;

    function __construct($id, $name)
    {
        $this->name = $name;
        $this->id = $id;
    }


}