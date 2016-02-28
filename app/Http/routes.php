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

Route::get('sitemap', function(){

    // create new sitemap object
    $sitemap = App::make("sitemap");

    // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
    // by default cache is disabled
    $sitemap->setCache('laravel.sitemap', 60);

    // check if there is cached sitemap and build new only if is not
    if (!$sitemap->isCached())
    {

//        Route::get('/united-states', 'CountriesController@showUnitedStates');
//        Route::get('/australia', 'CountriesController@showAustralia');
//        Route::resource('cinemas', 'CinemasController', ["only" => "show"]);
//        Route::resource('cinemas.movies', 'CinemaMovieController');
//        Route::resource('cinemas.movies.showings', 'CinemaMovieShowingsController');
//        Route::resource('showings', 'ShowingsController');
//        Route::resource('push', 'PushController');
//        Route::resource('cities', 'CitiesController');
//
//        Route::get('/movies/{movies}/{cityName}/{day?}', 'MoviesController@show');
//        Route::get('/{cinemas}/{movies}/{day?}', 'CinemaMovieShowingsController@index');
//
        // add item to the sitemap (url, date, priority, freq)
        $sitemap->add(URL::to('/'), \Carbon\Carbon::today(), '0.5', 'weekly');
        $sitemap->add(URL::to('/united-states'), \Carbon\Carbon::today(), '0.6', 'weekly');
        $sitemap->add(URL::to('/australia'), \Carbon\Carbon::today(), '0.6', 'weekly');

        $days = ['today', 'now', 'tomorrow'];

        // the cities
        $cinemas = Cinema::all();
        $cities = array_reduce($cinemas->all(), function($carry, $cinema) {
            if(!in_array($cinema->city, $carry)) {
                $carry[] = $cinema->city;
            }
            return $carry;
        }, []);
        foreach($cities as $city) {
            foreach($days as $day) {
                $sitemap->add(url("cities/{$city}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily');
            }
        }
        // the cinemas
        foreach($cinemas as $cinema) {
            foreach($days as $day) {
                $sitemap->add(url("{$cinema->slug}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily');
            }
        }

//        // the cinema movies
        // only show todays movies, showing more makes it crash
        $movieIds =  Showing::where('start_time', '>=', \Carbon\Carbon::today())
            ->distinct()->lists('movie_id');
        $movies = Movie::whereIn('id', $movieIds)->with('details')->get();

        foreach($movies as $movie) {
//            // find the cinemas
            $cinemaIds = Showing::where('movie_id', $movie->id)
                ->lists('cinema_id');
//
            $cinemas = Cinema::whereIn('id', $cinemaIds)->get();
            foreach($cinemas as $cinema) {
//                foreach($days as $day) {
                    $images = [];
                    if($movie->details) {
                        $images[] = [
                            'url' => asset("{$movie->details->poster}"),
                            'title' => $movie->title
                        ];
                    }
                    // hard coded for today, because doing x3 seems to cause the page to crash
                    $day = 'today';
                    $sitemap->add(url("{$cinema->slug}/{$movie->slug}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily', $images);
//                }
            }
        }
    }

    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');

});



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