<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 31/12/2014
 * Time: 3:39 PM
 */

namespace MoviesOwl\Showings;

use League\Fractal\TransformerAbstract;

class MovieShowingTransformer extends TransformerAbstract {

    public function transform(Showing $showing) {
        return [
            'id' => (int)$showing->id,
            'start_time' => $showing->start_time->timestamp,
//            "cinema_id" => (int)$showing->cinema_id,
            "showing_type" => $showing->showing_type,
            "screen_type" => $showing->screen_type,
            "tickets_url" => $showing->tickets_url,
            "event_session_id" => $showing->event_session_id
        ];
    }
}