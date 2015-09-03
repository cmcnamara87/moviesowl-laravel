<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 17/01/2015
 * Time: 11:05 PM
 */

namespace MoviesOwl\Repos\Movie;

use Carbon\Carbon;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Repos\EloquentBaseRepository;

class MovieRepository extends EloquentBaseRepository {


    /**
     * @var Movie
     */
    private $model;

    function __construct(Movie $model) {

        $this->model = $model;
    }

    function getWatchable(Carbon $startingAfter = null)
    {
        if (!$startingAfter) {
            $startingAfter = Carbon::now();
        }
        return $this->model->whereHas('showings', function ($q) use ($startingAfter) {
            $endOfDay = $startingAfter->copy()->endOfDay();
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
        })->orderBy('tomato_meter', 'desc')->get();
    }

    function getWatchableAtCinema($cinemaId, Carbon $startingAfter = null)
    {
        if (!$startingAfter) {
            $startingAfter = Carbon::now();
        }
        return $this->model->whereHas('showings', function ($q) use ($startingAfter, $cinemaId) {
            $endOfDay = $startingAfter->copy()->endOfDay();
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
            $q->where('cinema_id', $cinemaId);
        })->orderBy('tomato_meter', 'desc')->get();
    }
}