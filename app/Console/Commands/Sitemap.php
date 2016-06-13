<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Showings\Showing;

class Sitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'owl:sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles('php://stdout');

        // create new sitemap object
        $sitemap = App::make("sitemap");

        // check if there is cached sitemap and build new only if is not

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
            Log::info("Sitemap City - {{$city}}");
            foreach($days as $day) {
                $sitemap->add(url("cities/{$city}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily');
            }
        }
        // the cinemas
        foreach($cinemas as $cinema) {
            Log::info("Sitemap Cinema - {{$cinema->location}}");
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
                Log::info("Sitemap - Cinema Movie {{$cinema->location}} {{$movie->title}}");
//                foreach($days as $day) {
                // hard coded for today, because doing x3 seems to cause the page to crash
                $day = 'today';
                $sitemap->add(url("{$cinema->slug}/{$movie->slug}/{$day}"), \Carbon\Carbon::today(), '0.9', 'daily');
            }
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        $sitemap->store('xml', 'sitemap');
    }
}
