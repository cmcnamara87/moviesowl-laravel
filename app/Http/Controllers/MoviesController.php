<?php

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
use MoviesOwl\Repos\Movie\MovieRepository;

class MoviesController extends Controller {

    protected $movieRepo;

    function __construct(MovieRepository $movieRepo)
    {
        $this->movieRepo = $movieRepo;
    }


    /**
	 * Display a listing of the resource.
	 * GET /movies
	 *
	 * @return Response
	 */
	public function index()
	{
        $cinemas = Cinema::all();
        // watchable right now
        $movies = $this->movieRepo->getWatchable();

        // new this week
        $moviesNewThisWeek = Movie::where('created_at', '>', Carbon::now()->subWeeks(1))->orderBy('tomato_meter', 'desc')->get();
        if($movies->count()) {
            $topMovie = $moviesNewThisWeek->first();
        }

        $moviesCriticalAcclaim = Movie::whereHas('showings', function ($q) {
            $startingAfter = Carbon::now()->startOfDay();
            $endOfDay = $startingAfter->copy()->endOfDay();
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
        })->where('tomato_meter', '>', 75)->orderBy('tomato_meter', 'desc')->get();

        return View::make('movies.index', compact('movies', 'topMovie', 'moviesNewThisWeek', 'moviesCriticalAcclaim', 'cinemas'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /movies/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /movies
	 *
	 * @return Response
	 */
	public function store()
	{
		//
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
//        $movie = Movie::findOrFail($id);

        $cinemas = Cinema::whereHas('showings', function($q) use ($movie)
        {
            $q->where('movie_id', $movie->id);
            $q->where('start_time', '>=', Carbon::now()->toDateTimeString());
            $q->where('start_time', '<=', Carbon::now()->endOfDay()->toDateTimeString());
        })->get();



        return View::make('movies.show', compact('movie', 'cinemas'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /movies/{id}/edit
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
	 * PUT /movies/{id}
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
	 * DELETE /movies/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}