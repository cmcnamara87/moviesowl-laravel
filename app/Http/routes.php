<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

date_default_timezone_set('Australia/Brisbane');

header('Access-Control-Allow-Origin: *');
use MoviesOwl\Movies\Movie;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Showings\Showing;

Route::group(array('prefix' => 'api/v1'), function() {
    Route::resource('cities', 'Api\v1\CitiesController', ["only" => ["index"]]);
    Route::get('/cities/{cityName}/cinemas', 'Api\v1\CitiesController@cinemas');
    Route::resource('cinemas.movies', 'Api\v1\CinemaMoviesController', ["only" => ["index"]]);
    Route::resource('cinemas', 'Api\v1\CinemasController', ["only" => ["index", "show"]]);
    Route::resource('showings', 'Api\v1\ShowingsController', ["only" => "show"]);
    Route::resource('devices', 'Api\v1\DevicesController');
});

Route::get('/app', function() {
    return redirect('https://launchkit.io/websites/5SdrKqfmmjY');
//    Redirect::to('https://launchkit.io/websites/5SdrKqfmmjY');
//    Redirect::to('http://google.com');
});

Route::get('/united-states', 'CountriesController@showUnitedStates');
Route::get('/australia', 'CountriesController@showAustralia');
Route::resource('cinemas', 'CinemasController', ["only" => "show"]);
Route::resource('cinemas.movies', 'CinemaMovieController');
Route::resource('cinemas.movies.showings', 'CinemaMovieShowingsController');
Route::resource('showings', 'ShowingsController');
Route::resource('push', 'PushController');
Route::resource('cities', 'CitiesController');

Route::get('/', 'CountriesController@index');
Route::get('/cities/{cityName}/{day?}', 'CitiesController@show');
Route::get('/movies/{movies}/{cityName}/{day?}', 'MoviesController@show');
Route::get('/{cinemas}/{day?}', 'CinemasController@show');
Route::get('/{cinemas}/{movies}/{day?}', 'CinemaMovieShowingsController@index');