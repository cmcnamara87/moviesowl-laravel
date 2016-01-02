<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 31/12/2014
 * Time: 3:39 PM
 */

namespace MoviesOwl\Showings;

use League\Fractal\TransformerAbstract;
use MoviesOwl\Showings\Showing;
use Carbon\Carbon;
use MoviesOwl\EventCinemas\EventCinemasApi;
class ShowingTransformer extends TransformerAbstract {


    public function transform(Showing $showing) {
        $availableCount = 0;
        foreach($showing->seats as $row) {
            foreach($row as $seat) {
                if($seat == 'available') {
                    $availableCount += 1;
                }
            }
        }

        return [
            'id'                => (int)$showing->id,
            'start_time'        => $showing->start_time->timestamp,
            "cinema_id"         => (int)$showing->cinema_id,
            "showing_type"      => $showing->showing_type,
            "screen_type"       => $showing->screen_type,
            "tickets_url"       => $showing->tickets_url,
            "cinema_size"       => $showing->cinema_size,
            "seats"             => $showing->seats,
            "seats_count"       => $showing->seats_count,
            "full"              => floor($availableCount / $showing->seats_count * 100),
            "seats_updated_at"  => $showing->seats_updated_at->timestamp,
            "event_session_id"  => $showing->event_session_id
        ];
    }
}