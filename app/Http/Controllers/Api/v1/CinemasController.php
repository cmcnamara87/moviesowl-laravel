<?php

namespace MoviesOwl\Http\Controllers\Api\v1;

use Cyvelnet\Laravel5Fractal\Facades\Fractal;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Http\Controllers\Controller;
use MoviesOwl\Cinemas\CinemaTransformer;

class CinemasController extends Controller {

	/**
	 * Display a listing of cinemas
	 *
	 * @return Response
	 */
	public function index()
	{
		$cinemas = Cinema::orderBy('name', 'asc')->get();
        return Fractal::collection($cinemas, new CinemaTransformer)->responseJson(200);
	}

    /**
     * Display the specified cinema.
     *
     * @param Cinema $cinema
     * @return Response
     * @internal param int $id
     */
	public function show(Cinema $cinema)
	{
        return Fractal::item($cinema, new CinemaTransformer)->responseJson(200);
	}
}
