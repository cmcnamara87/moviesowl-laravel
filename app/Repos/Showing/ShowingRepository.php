<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 18/01/2015
 * Time: 12:19 AM
 */

namespace MoviesOwl\Repos\Showing;
use Carbon\Carbon;
use MoviesOwl\Repos\EloquentBaseRepository;
use MoviesOwl\Showings\Showing;

class ShowingRepository extends EloquentBaseRepository {

    /**
     * @var Movie
     */
    private $model;

    function __construct(Showing $model) {

        $this->model = $model;
    }
    function getWatchableAtCinema($movieId, $cinemaId, Carbon $startingAfter = null)
    {
        if(!$startingAfter) {
            $startingAfter = Carbon::now();
        }
        $endOfDay = $startingAfter->copy()->endOfDay();

        return $this->model->where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('cinema_id', $cinemaId)
            ->where('movie_id', $movieId)
            ->where('start_time', '<=', $endOfDay->toDateTimeString())->orderBy('start_time', 'asc')->get();
    }

}