<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 30/01/16
 * Time: 12:23 PM
 */

namespace MoviesOwl\Fandango;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;

class FandangoUpdater {

    public function update() {
        // boston lat lon
        // lat: 42.359968
        // lon: -71.060093
        // http://data.tmsapi.com/v1.1/movies/showings?startDate=2016-01-30&numDays=2&lat=42.359968&lng=-71.060093&radius=20&units=km&api_key=
        Log::info('Updating Fandango');
        $this->getMovies();
    }

    public function getMovies() {
        Log::info('Get sessions');
        $url = "http://data.tmsapi.com/v1.1/movies/showings?startDate=2016-01-31&numDays=1&lat=42.359968&lng=-71.060093&radius=20&units=km&api_key=" . env('FANDANGO_API_KEY');
        $result = json_decode(@file_get_contents($url));
        Log::info($result);
        foreach ($result as $movieElement) {
            Log::info('Get sessions for '.$movieElement->title);
            $this->getSessions($movieElement);
        }

    }

    public function getSessions($movieElement) {
        $now = Carbon::now()->toDateTimeString();
        $movie = Movie::firstOrCreate([
            'title' => $movieElement->title
        ]);

        $showings = [];
        foreach($movieElement->showtimes as $session){
            Log::info('Found Cinema '.$session->theatre->name);
            $cinema = Cinema::firstOrCreate([
                'location' => $session->theatre->name,
                'timezone' => 'America/New_York',
                'city' => 'Boston',
                'country' => 'United States'
            ]);

            $startTime = Carbon::parse($session->dateTime, $cinema->timezone);
            Log::info('Session time '.$startTime->toDateTimeString());

            if(isset($session->ticketURI)){
                $ticketUrl = $session->ticketURI;
            }
            else{
                $ticketUrl = "";
            }
            $showings[] = [
                "movie_id" => $movie->id,
                "cinema_id" => $cinema->id,
                "start_time" => $startTime->toDateTimeString(),
                'created_at' => $now,
                'updated_at' => $now,
                "tickets_url" => $ticketUrl,
            ];
        }

        // Chunk it for mass insert
        $showingsChunks = array_chunk($showings, 100);
        foreach($showingsChunks as $chunk) {
            DB::table('showings')->insert($chunk);
        }
    }


}