<?php

namespace MoviesOwl\Http\Controllers;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
use MoviesOwl\Repos\Movie\MovieRepository;
use MoviesOwl\Showings\Showing;

class MoviesController extends Controller
{

    protected $movieRepo;

    function __construct(MovieRepository $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }


//    public function index() {
//        $startingAfter = Carbon::today();
//        $endOfDay = $startingAfter->copy()->endOfDay();
//
//        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
//            ->where('start_time', '<=', $endOfDay->toDateTimeString())
//            ->distinct()->lists('movie_id');
//
//        $movies = Movie::whereIn('id', $movieIds)->with(array('details'))->orderBy('tomato_meter', 'desc')->get();
//
//        // group the movies
//        $moviesByRating = array_reduce($movies->all(), function($carry, $movie) {
//            $rating = '';
//            if($movie->tomato_meter > 75) {
//                $rating = 'Great';
//            } else if ($movie->tomato_meter > 50) {
//                $rating = 'Good';
//            } else if ($movie->tomato_meter > 0) {
//                $rating = 'Bad';
//            } else {
//                $rating = 'Unknown';
//            }
//            if(!isset($carry[$rating])) {
//                $carry[$rating] = [];
//            }
//            $carry[$rating][] = $movie;
//            return $carry;
//        }, []);
//
//        return view('movies.index', compact('moviesByRating', 'movies'));
//    }

    /**
     * Display the specified resource.
     * GET /movies/{id}
     *
     * @param Movie $movie
     * @return Response
     * @internal param int $id
     */
    public function show(Movie $movie, $cityName, $day = 'today')
    {
        // todo: fix this hack, add a city table
        // currently i just get the first cinema, and get its timezone
        $cinema = Cinema::where('city', $cityName)->first();
        $timezone = $cinema->timezone;

        // cinemas in city
        $cinemas = Cinema::where('city', $cityName)->get();

        // get all the cinemas that are showing this movie today
        $startingAfter = Carbon::$day($timezone);
        $endOfDay = $startingAfter->copy()->endOfDay();
        $showings = Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->where('movie_id', $movie->id)
            ->where('cinema_id', $cinemas->pluck('id')->all())
            ->get();


        $cinemas = $cinemas->reduce(function ($carry, $cinema) use ($showings) {
            $filteredShowings = [];
            foreach($showings as $showing) {
                if($showing->cinema_id == $cinema->id) {
                    $filteredShowings[] = $showing;
                }
            }
            $filteredShowings = collect($filteredShowings);

            if(count($filteredShowings)) {
                $cinema->showings = $filteredShowings;
                $carry[] = $cinema;
            }
            return $carry;
        }, []);

        return view('movies.show', compact('movie', 'cinemas', 'country', 'cityName', 'day'));
    }
}