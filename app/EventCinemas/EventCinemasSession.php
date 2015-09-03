<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 3/01/2015
 * Time: 2:24 PM
 */

namespace MoviesOwl\EventCinemas;

class EventCinemasSession {

    public $type;
    public $startTime;
    public $sessionType;
    public $ticketsUrl;
    public $eventSessionId;

    function __construct($startTime, $screenType, $sessionType, $eventSessionId, $ticketsUrl)
    {
        $this->startTime = $startTime;
        $this->type = $screenType;
        $this->sessionType = $sessionType;
        $this->eventSessionId = $eventSessionId;
        $this->ticketsUrl = $ticketsUrl;
    }
}