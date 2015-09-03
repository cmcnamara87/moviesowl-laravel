<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 31/12/2014
 * Time: 3:39 PM
 */

namespace MoviesOwl\Cinemas;

use League\Fractal\TransformerAbstract;
use Carbon\Carbon;
use MoviesOwl\Cinemas\Cinema;

class CinemaTransformer extends TransformerAbstract {

    public function transform(Cinema $cinema) {
        return [
            'id' => (int)$cinema->id,
            'location' => $cinema->location
        ];
    }
}