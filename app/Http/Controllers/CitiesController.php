<?php

namespace MoviesOwl\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Http\Requests;
use MoviesOwl\Http\Controllers\Controller;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Showings\Showing;

class CitiesController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($cityName, $day = 'today')
    {

        $cinemas = Cinema::where('city', $cityName)->get();
        $cinemasByLetter = array_reduce($cinemas->all(), function($carry, $cinema) {
            $letter = substr($cinema->location, 0, 1);
            if(!isset($carry[$letter])) {
                $carry[$letter] = [];
            }
            $carry[$letter][] = $cinema;
            return $carry;
        }, []);

        $firstCinema = $cinemas[0];
        $startingAfter = Carbon::$day($firstCinema->timezone);
        $endOfDay = $startingAfter->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->whereIn('cinema_id', $cinemas->lists('id'))
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

        return view('cities.show', compact('cinemasByLetter', 'cityName', 'moviesByRating', 'day'));
    }
}
