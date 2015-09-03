<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 3/01/2015
 * Time: 2:23 PM
 */

namespace MoviesOwl\EventCinemas;

use Illuminate\Support\Facades\Log;

class EventCinemasMovie {

    public $title;
    public $sessions;

    function __construct($title, $sessions)
    {
        $this->title = $title;
        $this->sessions = $sessions;
    }


}