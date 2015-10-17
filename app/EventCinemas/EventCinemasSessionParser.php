<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 3/01/2015
 * Time: 2:31 PM
 */

namespace MoviesOwl\EventCinemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EventCinemasSessionParser {

    protected $sessionElement;

    function __construct($sessionElement)
    {
        $this->sessionElement = $sessionElement;
    }

    public function getSession() {
        return new EventCinemasSession(
            $this->getStartTime(),
            $this->getType(),
            $this->getSessionType(),
            $this->getEventSessionId(),
            $this->getTicketsUrl()
        );
    }

    /**
     * @param $sessionElement
     * @return mixed
     */
    private function getStartTime()
    {
        $startTimeString = $this->sessionElement->{"data-starttime"};
        $startTime = Carbon::createFromFormat('Y-m-d h:i A', $startTimeString);
        return $startTime;
    }

    public function getType() {
        $screenType = $this->sessionElement->{"data-screentype"};
        if($screenType == 0) return 'standard';
        if($screenType == 2) return 'gold class';
        if($screenType == 4) return 'vmax';

        return 'standard';
    }

    public function getSessionType() {
        $attributes = json_decode(htmlspecialchars_decode($this->sessionElement->{"data-attributes"}));
        if(!$attributes) {
            return 'standard';
        }
        foreach($attributes as $attribute) {
            if($attribute->Code === '3D') {
                return '3D';
            }
        }
        return 'standard';
    }

    public function getEventSessionId() {
        return $this->sessionElement->{"data-id"};
    }

    public function getTicketsUrl() {
        $sessionId = $this->getEventSessionId();
        return "https://www.eventcinemas.com.au/Ticketing/Order#sessionId=$sessionId&bookingSource=www|sessions";
    }
}