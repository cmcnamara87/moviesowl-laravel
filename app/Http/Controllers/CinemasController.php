<?php

namespace MoviesOwl\Http\Controllers;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use Carbon\Carbon;
use MoviesOwl\Repos\Movie\MovieRepository;

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

        // load the view and pass the nerds
        return view('cinemas.index', compact('cinemas'));
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

	/**
	 * Display the specified resource.
	 * GET /cinemas/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Cinema $cinema)
	{
//        $cinema = Cinema::findOrFail($id);
        $movies = $this->movieRepo->getWatchableAtCinema($cinema->id);
//        $movies = Movie::watchable($now)->orderBy('tomato_meter', 'desc')->get();

        return View::make('cinemas.show', compact('movies', 'cinema'));
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