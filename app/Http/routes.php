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

// usage inside a laravel route
Route::get('video', function()
{
    $cityName = 'Brisbane';
    $day = 'tomorrow';
    $cinemas = Cinema::where('city', $cityName)->get();
    $firstCinema = $cinemas[0];
    $startingAfter = \Carbon\Carbon::$day($firstCinema->timezone);
    $endOfDay = $startingAfter->copy()->endOfDay();

    $movieIds =  Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
        ->where('start_time', '<=', $endOfDay->toDateTimeString())
        ->whereIn('cinema_id', $cinemas->lists('id'))
        ->distinct()->lists('movie_id');

    $movies = Movie::whereIn('id', $movieIds)->whereHas('details', function($query) {
        $query->where('tomato_meter', '>=', 80);
    })->with(array('details'))->orderBy('tomato_meter', 'desc')->get();


    $counter = 0;
    $width = 720;
    $height = 405;

    $titleFontSize = 57;
    $titleY = $height / 2 - $titleFontSize;
    $titleX = $width / 2;

    $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
    $path = storage_path("app/movie/img$imageid.jpg");
    $img = Image::canvas($width, $height, '#FB1B75');
    $img->text("WHAT'S ON AT THE MOVIES", $width / 2, $height / 2 - 25,
        function($font) use ($img, $width, $titleFontSize, $height, $titleX, $titleY){
            $font->file('/Users/craig/Desktop/TEST.otf');
            $font->size($titleFontSize);
            $font->color('#fff');
            $font->align('center');
            $font->valign('top');
        });
    $img->save($path);

    foreach($movies as $movie) {
        if(strpos($movie->title, "Star") !== false) {
            continue;
        }
        if(strpos($movie->title, "Mermaid") !== false) {
            continue;
        }

        if(!$movie->details->wide_poster) {
            continue;
        }



        $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
        $path = storage_path("app/movie/img$imageid.jpg");
        $img = Image::canvas($width, $height, '#1B1D27');

// use callback to define details
        $subtitleSpace = 25;
        $subtitleFontSize = 30;
        $tomatoWidth = 30;

        $img->text(strtoupper($movie->title), $titleX, $titleY,
            function($font) use ($img, $width, $titleFontSize, $height, $titleX, $titleY, $subtitleSpace, $tomatoWidth, $movie, $path){
                $font->file('/Users/craig/Desktop/TEST.otf');
                $font->size($titleFontSize);
                $font->color('#fff');
                $font->align('center');
                $font->valign('top');
//        $font->angle(45);

                $box = $font->getBoxSize();
                if($box['width'] > $width) {
                    return;
                }

                $subtitleX = $titleX + $tomatoWidth / 2;
                $subtitleY = $titleY + $box['height'] + $subtitleSpace;
                $img->text($movie->details->tomato_meter . '%', $subtitleX, $subtitleY , function($font2) use ($img, $titleX, $subtitleX, $subtitleY, $tomatoWidth, $path) {
                    $font2->file('/Library/Fonts/Futura.ttc');
                    $font2->size(30);
                    $font2->color('#fdf6e3');
                    $font2->align('center');
                    $font2->valign('top');

                    $box2 = $font2->getBoxSize();

                    $tomatoX = (int)($subtitleX - ($box2['width'] / 2) - $tomatoWidth - 10);
                    $tomatoY = (int)$subtitleY - 3;
                    $img->insert('/Users/craig/Desktop/movie/tomato.png', 'top-left', $tomatoX, $tomatoY);
                });
            });
        $img->save($path);

        $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
        $path = storage_path("app/movie/img$imageid.jpg");
        Image::make(public_path($movie->details->wide_poster))
            ->resize($width, $height)
            ->save($path);

    }

    $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
    $path = storage_path("app/movie/img$imageid.jpg");
    $img = Image::canvas($width, $height, '#FB1B75');
    $img->text("MOVIESOWL.COM", $width / 2, $height / 2 - 25,
        function($font) use ($img, $width, $titleFontSize, $height, $titleX, $titleY){
            $font->file('/Users/craig/Desktop/TEST.otf');
            $font->size($titleFontSize);
            $font->color('#fff');
            $font->align('center');
            $font->valign('top');
        });
    $img->save($path);

    $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
    $path = storage_path("app/movie/img$imageid.jpg");
    $img = Image::canvas($width, $height, '#FB1B75');
    $img->insert("/Users/craig/Sites/moviesowl-laravel/public/images/owl.png", 'center');
    $img->save($path);

    $hootFontSize = 30;
    $imageid = $invID = str_pad($counter++, 3, '0', STR_PAD_LEFT);
    $path = storage_path("app/movie/img$imageid.jpg");
    $img = Image::canvas($width, $height, '#FB1B75');
    $img->text("hoot. hoot. hoot.", $width / 2, $height / 2 - 25,
        function($font) use ($img, $width, $hootFontSize, $height, $titleX, $titleY){
            $font->file('/Users/craig/Desktop/TEST.otf');
            $font->size($hootFontSize);
            $font->color('#fff');
            $font->align('center');
            $font->valign('top');
        });
    $img->save($path);

});


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
            $cinemaIds = DB::table('showings')->select('cinema_id')
                ->where('movie_id', $movie->id)
                ->distinct()
                ->lists('cinema_id');
//
            $cinemas = Cinema::whereIn('id', $cinemaIds)->get();
            foreach($cinemas as $cinema) {
//                foreach($days as $day) {
                    // hard coded for today, because doing x3 seems to cause the page to crash
                    $day = 'today';
                    $sitemap->add(url("{$cinema->slug}/{$movie->slug}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily');
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