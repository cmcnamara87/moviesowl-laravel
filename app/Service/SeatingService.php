<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 18/01/2015
 * Time: 7:25 PM
 */

namespace MoviesOwl\Service;
use Carbon\Carbon;
use MoviesOwl\Cinema21\Cinema21Api;
use MoviesOwl\EventCinemas\EventCinemasApi;
use MoviesOwl\Repos\Showing\ShowingRepository;
use MoviesOwl\Showings\Showing;

class SeatingService {

    protected $eventCinemasApi;
    protected $repo;
    protected  $cinema21Api;

    function __construct(EventCinemasApi $eventCinemasApi, ShowingRepository $repo, Cinema21Api $cinema21Api)
    {
        $this->eventCinemasApi = $eventCinemasApi;
        $this->cinema21Api = $cinema21Api;
        $this->repo = $repo;
    }

    /**
     * @param Showing $showing
     */
    public function updateSeating(Showing $showing) {
        if(!$showing->{"event_session_id"} && !$showing->data) {
            return;
        }
        if($showing->{"event_session_id"}){
            if ($showing->start_time->lte(Carbon::now($showing->cinema->timezone)->subMinutes(15))) {
                return;
            }
            $showing->seats = $this->eventCinemasApi->getSeats($showing->{"event_session_id"});
            $this->repo->store($showing);
        }

        if($showing->data) {
            if ($showing->start_time->lte(Carbon::now($showing->cinema->timezone)->addMinutes(15))) {
                return;
            }
            $showing->seats =  $this->cinema21Api->getSeats($showing);
            $this->repo->store($showing);
        }

    }


    /**
     * @param Showing $showing
     * @return mixed
     */
    public function isSeatingDataOld(Showing $showing)
    {
        if ($showing->start_time->lte(Carbon::now())) {
            return $this->isSeatDataMinutesOld($showing, 1);
        }
        if($this->isShowingStartingInMinutes($showing, 5)) {
            // Update every minute
            return $this->isSeatDataMinutesOld($showing, 1);
        }
        if($this->isShowingStartingInMinutes($showing, 15)) {
            // Update every 5 mins
            return $this->isSeatDataMinutesOld($showing, 5);
        }
        if($this->isShowingStartingInMinutes($showing, 30)) {
            // 10 mins
            return $this->isSeatDataMinutesOld($showing, 10);
        }
        if($this->isShowingStartingInMinutes($showing, 60)) {
            // 15 mins
            return $this->isSeatDataMinutesOld($showing, 15);
        }
        // every hour
        return $this->isSeatDataMinutesOld($showing, 60);
    }

    /**
     * @param Showing $showing
     * @return mixed
     */
    public function isShowingStartingInMinutes(Showing $showing, $minutes)
    {
        return $showing->start_time->lte(Carbon::now()->addMinutes($minutes));
    }

    public function isSeatDataMinutesOld(Showing $showing, $minutes) {
        return $showing->seats_updated_at->lte(Carbon::now()->subMinutes($minutes));
    }


}