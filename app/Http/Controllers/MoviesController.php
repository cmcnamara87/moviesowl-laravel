<?php

namespace MoviesOwl\Http\Controllers;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
use MoviesOwl\Repos\Movie\MovieRepository;
use MoviesOwl\Showings\Showing;

class MoviesController extends Controller {

    protected $movieRepo;

    function __construct(MovieRepository $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }


    public function index() {
        $startingAfter = Carbon::today();
        $endOfDay = $startingAfter->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)->with(array('details'))->orderBy('tomato_meter', 'desc')->get();

        // group the movies
        $moviesByRating = array_reduce($movies->all(), function($carry, $movie) {
            $rating = '';
            if($movie->tomato_meter > 75) {
                $rating = 'Great';
            } else if ($movie->tomato_meter > 50) {
                $rating = 'Good';
            } else if ($movie->tomato_meter >= 0) {
                $rating = 'Bad';
            } else {
                $rating = 'Unknown';
            }
            if(!isset($carry[$rating])) {
                $carry[$rating] = [];
            }
            $carry[$rating][] = $movie;
            return $carry;
        }, []);

        return view('movies.index', compact('moviesByRating', 'movies'));
    }

    /**
     * Display the specified resource.
     * GET /movies/{id}
     *
     * @param Movie $movie
     * @return Response
     * @internal param int $id
     */
	public function show(Movie $movie)
	{
        // get all the cinemas that are showing this movie today
        $startingAfter = Carbon::today();
        $endOfDay = $startingAfter->copy()->endOfDay();
        $cinemaIds = Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->where('movie_id', $movie->id)
            ->lists('cinema_id');

        $cinemas = Cinema::whereIn('id', $cinemaIds)->get();

        $cinemasByCity = array_reduce($cinemas->all(), function($carry, $cinema) {
            $cinemaLocation = $cinema->city . ', ' . $cinema->country;
            if(!isset($carry[$cinemaLocation])) {
                $carry[$cinemaLocation] = [];
            }
            $carry[$cinemaLocation][] = $cinema;
            return $carry;
        }, []);


        return view('movies.show', compact('movie', 'cinemasByCity'));
	}
}