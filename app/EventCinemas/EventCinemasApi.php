<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 2/01/2015
 * Time: 3:16 PM
 */

namespace MoviesOwl\EventCinemas;

use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasMoviesParser;
use MoviesOwl\EventCinemas\EventCinemasCinema;
use Yangqi\Htmldom\Htmldom;
use Carbon\Carbon;

class EventCinemasApi {

    /**
     * @return mixed
     */
    public function getCinemas()
    {
        $url = "https://www.eventcinemas.com.au";
        $eventCinemas = @file_get_contents($url);
        $html = new Htmldom($eventCinemas);
//        $cinemasElements = $html->find('[data-value="QLD"] a');
        $cinemasElements = $html->find('#cinema-select .top-select-option a');
        return array_map(function($cinemaElement) {
            return new EventCinemasCinema($cinemaElement->{'data-id'}, $cinemaElement->{'data-name'});
        }, $cinemasElements);
    }

    public function getTypeName($screenTypeId) {
        if($screenTypeId == 0) return 'standard';
        if($screenTypeId == 2) return 'gold class';
        if($screenTypeId == 4) return 'vmax';
        return 'standard';
    }

    public function getMovies(Cinema $cinema, $day) {
        $eventCinemaId = $cinema->eventcinema_id;

        $dateString = Carbon::$day()->toDateString();
        // https://www.eventcinemas.com.au/Cinemas/GetSessions?cinemaIds=48&date=2015-12-31
        Log::info('Requesting data');
        $moviesData = json_decode(@file_get_contents("https://www.eventcinemas.com.au/Cinemas/GetSessions?cinemaIds=" . $eventCinemaId . "&date=$dateString"));
        Log::info('Got data');
        if(!$moviesData || !isset($moviesData->Data) || !isset($moviesData->Data->Movies)) {
            return [];
        }
        $movies = array_map(function($movieData) use ($cinema) {
            $sessions = array_map(function($sessionData) use ($cinema) {

                $ticketsUrl = "https://www.eventcinemas.com.au/Ticketing/Order#sessionId=". $sessionData->Id . "&bookingSource=www|sessions";
                $eventCinemaSession = new EventCinemasSession(
                    Carbon::parse($sessionData->StartTime, $cinema->timezone),
                    $this->getTypeName($sessionData->ScreenTypeId),
                    $sessionData->Is3d == true ? '3D' : 'standard',
                    $sessionData->Id,
                    $ticketsUrl,
                    $sessionData->SeatsAvailable ? $sessionData->SeatsAvailable : 0
                );
                return $eventCinemaSession;
            }, $movieData->CinemaModels[0]->Sessions);
            return new EventCinemasMovie(html_entity_decode($movieData->Name),$sessions);
        }, $moviesData->Data->Movies);
        return $movies;
    }

    public function getSeats($eventSessionId) {
        $sessionId = $eventSessionId; //$showing->{"event_session_id"};
//        $sessionId = 5251800;
        //
        $url = "https://www.eventcinemas.com.au/Ticketing/Order/GetSeating?sessionId=$sessionId";
        $json = json_decode(@file_get_contents($url));

        if(!$json) {
            return [];
        }

        $htmlText = $json->Data;
        if(!$htmlText) {
            return [];
        }

        $html = new Htmldom($htmlText);

        return array_map(function($rowElement) {
            return array_map(function($seatElement) {
                $type = "fail";
                if($seatElement->{"data-seat"}) {
                    $type = "available";
                } else if ($seatElement->class === "taken") {
                    $type = "taken";
                } else if ($seatElement->class === "spacer") {
                    $type = "spacer";
                }
                return $type;
            }, $rowElement->find("li.taken,li.spacer,li[data-seat]"));
        }, $html->find(".seats .row"));
    }
}