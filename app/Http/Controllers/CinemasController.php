<?php

namespace MoviesOwl\Http\Controllers;

use Illuminate\Support\Facades\Input;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
use MoviesOwl\Repos\Movie\MovieRepository;
use MoviesOwl\Showings\Showing;

class CinemasController extends Controller {

    protected $movieRepo;

    function __construct(MovieRepository $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }


    /**
	 * Display a listing of the resource.
	 * GET /cinemas
	 *
	 * @return Response
	 */
	public function index()
	{
        // get all the nerds
        $cinemas = Cinema::all();

        // lets group it
        $cinemasByCity = array_reduce($cinemas->all(), function($carry, $cinema) {
            $cinemaLocation = $cinema->city;
            if(!isset($carry[$cinemaLocation])) {
                $carry[$cinemaLocation] = [];
            }
            $carry[$cinemaLocation][] = $cinema;
            return $carry;
        }, []);
        // load the view and pass the nerds
        return view('cinemas.index', compact('cinemasByCity'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cinemas/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cinemas
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

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

	/**
	 * Display the specified resource.
	 * GET /cinemas/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Cinema $cinema, $day = 'today')
	{
//        $startingAfter = $this->getStartingAfter();
        $startingAfter = Carbon::$day($cinema->timezone);
        $endOfDay = $startingAfter->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->where('cinema_id', $cinema->id)->lists('movie_id')->unique();

        $movies = Movie::whereIn('id', $movieIds)->with(array('details', 'showings' => function($q) use ($startingAfter, $endOfDay, $cinema)
        {
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
            $q->where('cinema_id', $cinema->id);

        }))->orderBy('tomato_meter', 'desc')->get();
        
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
//        $cinema = Cinema::findOrFail($id);
//        $movies = $this->movieRepo->getWatchableAtCinema($cinema->id);
//        $movies = Movie::watchable($now)->orderBy('tomato_meter', 'desc')->get();

        return view('cinemas.show', compact('moviesByRating', 'cinema', 'movies', 'day'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /cinemas/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /cinemas/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /cinemas/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}