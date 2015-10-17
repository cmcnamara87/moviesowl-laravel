<?php

namespace MoviesOwl\Http\Controllers;

use MoviesOwl\Service\SeatingService;
use MoviesOwl\Showings\Showing;

class ShowingsController extends Controller {

    protected $seatingService;

    function __construct(SeatingService $seatingService)
    {
        $this->seatingService = $seatingService;
    }


    /**
	 * Display a listing of the resource.
	 * GET /showings
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /showings/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /showings
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

    /**
     * Display the specified resource.
     * GET /showings/{id}
     *
     * @param Showing $showing
     * @return Response
     * @internal param int $id
     */
	public function show(Showing $showing)
	{
        $movie = $showing->movie;
        $cinema = $showing->cinema;
        $this->seatingService->updateSeating($showing);
        return view('showings.show', compact('showing', 'movie', 'cinema'));
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /showings/{id}/edit
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
	 * PUT /showings/{id}
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
	 * DELETE /showings/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}