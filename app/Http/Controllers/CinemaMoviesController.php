<?php

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
class CinemaMoviesController extends Controller {

    protected $movieRepo;


    /**
     * Display a listing of the resource.
     * GET /movies
     *
     * @return Response
     */
    public function index(Cinema $cinema)
    {
//        $cinema = Cinema::findOrFail($cinemaId);
        $movies = Movie::watchableAtCinema($cinema->id, Carbon::now())->orderBy('tomato_meter', 'desc')->get();

        // load the view and pass the nerds
        return View::make('movies.index', compact('movies', 'cinema'));
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
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $movie = Movie::findOrFail($id);

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