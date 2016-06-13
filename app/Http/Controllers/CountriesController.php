<?php

namespace MoviesOwl\Http\Controllers;

use Illuminate\Http\Request;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Http\Requests;
use MoviesOwl\Http\Controllers\Controller;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $countries = array_reduce($cinemas->all(), function($carry, $cinema) {
            if(!isset($carry[$cinema->country])) {
                if($cinema->country == 'United States') {
                    $slug = 'united-states';
                }
                if($cinema->country == 'Australia') {
                    $slug = 'australia';
                }
                if($cinema->country == 'Indonesia') {
                    $slug = 'indonesia';
                }
                if($cinema->country == 'United Kingdom') {
                    $slug = 'united-kingdom';
                }
                if($cinema->country == 'Ireland') {
                    $slug = 'ireland';
                }
                $carry[$cinema->country] = [
                    "name" => $cinema->country,
                    "slug" => $slug,
                    "cities" => []
                ];
            }
            if(!in_array($cinema->city, $carry[$cinema->country]['cities'])) {
                $carry[$cinema->country]['cities'][] = $cinema->city;
            }
            return $carry;
        }, []);
        // load the view and pass the nerds
        return view('countries.index', compact('countries'));
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

    public function showUnitedStates() {
        return $this->show('United States');
    }
    public function showAustralia() {
        return $this->show('Australia');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($countryName)
    {
        $cinemas = Cinema::where('country', $countryName)->get();
        $cinemasByCity = array_reduce($cinemas->all(), function($carry, $cinema) {
            $cinemaLocation = $cinema->city;
            if(!isset($carry[$cinemaLocation])) {
                $carry[$cinemaLocation] = [];
            }
            $carry[$cinemaLocation][] = $cinema;
            return $carry;
        }, []);
        // load the view and pass the nerds
        return view('countries.show', compact('cinemasByCity', 'countryName'));
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
