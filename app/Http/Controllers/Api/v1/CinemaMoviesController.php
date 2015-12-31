<?php

namespace MoviesOwl\Http\Controllers\Api\v1;

use Cyvelnet\Laravel5Fractal\Facades\Fractal;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use MoviesOwl\Movies\MovieTransformer;
use MoviesOwl\Cinemas\Cinema;
use Carbon\Carbon;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Showings\Showing;

class CinemaMoviesController extends Controller
{

    /**
     * Display a listing of showings
     *
     * @param Cinema $cinema
     * @return Response
     */
    public function index(Cinema $cinema)
    {
        $startingAfter = $this->getStartingAfter();
        $endOfDay = $startingAfter->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->where('cinema_id', $cinema->id)->distinct()->lists('movie_id');

        // There are no movies left for today, try tomorrow (this should be moved to the app in the future)
        if(!count($movieIds)) {
            $startingAfter = Carbon::tomorrow();
            $endOfDay = $startingAfter->copy()->endOfDay();

            $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('start_time', '<=', $endOfDay->toDateTimeString())
                ->where('cinema_id', $cinema->id)->distinct()->lists('movie_id');
        }

        $movies = Movie::whereIn('id', $movieIds)->with(array('details', 'showings' => function($q) use ($startingAfter, $endOfDay, $cinema)
        {
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
            $q->where('cinema_id', $cinema->id);

        }))->orderBy('tomato_meter', 'desc')->get();
        return Fractal::collection($movies, new MovieTransformer)->responseJson(200);
    }

        /**
     * @return static
     */
    public function getStartingAfter()
    {
        // TODO: extract this
        $now = Input::get('starting_after');
        if (!$now) {
            // by default show movies that have just started up to 20 minutes ago.
            return Carbon::now()->subMinutes(20);
        }
        return Carbon::createFromTimestamp($now);
    }
}
