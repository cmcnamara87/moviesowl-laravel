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
        $startingAfter = $this->getStartingAfter($cinema);
        $endOfDay = $startingAfter->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->where('cinema_id', $cinema->id)->distinct()->lists('movie_id')->unique();

        // There are no movies left for today, try tomorrow (this should be moved to the app in the future)
        if(!count($movieIds)) {
            $startingAfter = Carbon::tomorrow();
            $endOfDay = $startingAfter->copy()->endOfDay();

            $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('start_time', '<=', $endOfDay->toDateTimeString())
                ->where('cinema_id', $cinema->id)->distinct()->lists('movie_id')->unique();
        }

        $movies = Movie::whereIn('id', $movieIds)->with(array('details', 'showings' => function($q) use ($startingAfter, $endOfDay, $cinema)
        {
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
            $q->where('cinema_id', $cinema->id);

        }))->orderBy('tomato_meter', 'desc')->get();

        foreach($movies as $movie) {
            // set if they are new
            // did they have a session 1 week ago?
            $movie->new = Showing::where('movie_id', $movie->id)
                    ->where('cinema_id', $cinema->id)
                    ->where('start_time', '>', Carbon::today($cinema->timezone)->subDays(7))
                    ->where('start_time', '<', Carbon::today($cinema->timezone)->subDays(6))->count() == 0;
        }
        return Fractal::collection($movies, new MovieTransformer)->responseJson(200);
    }

        /**
     * @return static
     */
    public function getStartingAfter($cinema)
    {
//        $now = Carbon::now("Asia/Jakarta")->timestamp;
//        return Carbon::now("Asia/Jakarta");
        // TODO: extract this
        $now = Input::get('starting_after');
        if (!$now) {
            // by default show movies that have just started up to 20 minutes ago.
            return Carbon::now()->subMinutes(20);
        }
        // 1463752375
        // 1463752408
//        dd($now);
        return Carbon::createFromTimestamp($now, $cinema->timezone);
    }
}
