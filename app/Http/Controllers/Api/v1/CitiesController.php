<?php

namespace MoviesOwl\Http\Controllers\Api\v1;

use Cyvelnet\Laravel5Fractal\Facades\Fractal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Cinemas\CinemaTransformer;
use MoviesOwl\Http\Requests;
use MoviesOwl\Http\Controllers\Controller;

class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cities = Cinema::select(DB::raw("CONCAT_WS(', ', city, country) as 'name'"))
            ->distinct()
            ->orderBy('country', 'asc')
            ->orderBy('city', 'asc')
            ->get();
        // todo: add transformer
        return response()->json(['data' => $cities]);
    }

    public function cinemas($cityName) {
        $parts = explode(",", $cityName);
        $city = trim($parts[0]);
        $country = trim($parts[1]);
        $cinemas = Cinema::where('city', '=', $city)->where('country', '=', $country)
            ->get();
        return Fractal::collection($cinemas, new CinemaTransformer)->responseJson(200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
