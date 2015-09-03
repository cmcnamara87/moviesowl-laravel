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

function getSubDomain() {
    if (App::environment('local')) {
        return 'localhost';
    }
    if(App::environment('production')) {
        return 'api.moviesowl.com';
    }
}

Route::group(array('domain' => getSubDomain()), function()
{
    Route::group(array('prefix' => 'v1'), function() {
        Route::resource('cinemas.movies', 'Api\v1\CinemaMoviesController', ["only" => ["index"]]);
        Route::resource('cinemas', 'Api\v1\CinemasController', ["only" => ["index", "show"]]);
        Route::resource('showings', 'Api\v1\ShowingsController', ["only" => "show"]);
    });
});

Route::get('/app', function() {
    Redirect::to('https://launchkit.io/websites/5SdrKqfmmjY');
});

Route::get('/', 'MoviesController@index');

Route::bind('movies', function($value, $route)
{
    return Movie::findOrFail($value);
});
Route::bind('cinemas', function($value, $route)
{
    return Cinema::findOrFail($value);
});
Route::model('showings', 'MoviesOwl\Showings\Showing');

Route::resource('movies', 'MoviesController');
Route::resource('cinemas', 'CinemasController');
Route::resource('cinemas.movies', 'CinemaMovieController');
Route::resource('cinemas.movies.showings', 'CinemaMovieShowingsController');
Route::resource('showings', 'ShowingsController');