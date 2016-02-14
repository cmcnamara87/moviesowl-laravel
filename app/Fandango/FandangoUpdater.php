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

    public function update($day) {
        // boston lat lon
        // lat: 42.359968
        // lon: -71.060093
        // http://data.tmsapi.com/v1.1/movies/showings?startDate=2016-01-30&numDays=2&lat=42.359968&lng=-71.060093&radius=20&units=km&api_key=
        Log::info('Updating Fandango');

        // boston mas
        $this->getMovies('Boston', 'America/New_York', 42.359968, -71.060093, $day);
        // new york, new york
        $this->getMovies('New York', 'America/New_York', 40.6643, -73.9385, $day);
        // LA, california
        $this->getMovies('Los Angeles', 'America/Los_Angeles', 34.0194, -118.4108, $day);
        // chicago
        $this->getMovies('Chicago', 'America/Chicago', 41.8376 ,-87.6818, $day);
        // houston texas
        $this->getMovies('Houston', 'America/Chicago', 29.7805, -95.3863, $day);
        // Philadelphia
        $this->getMovies('Philadelphia', 'America/New_York', 40.0094, -75.1333, $day);
        // phoenix arizona
        $this->getMovies('Phoenix', 'America/Denver', 33.5722, -112.0880, $day);
        // san antonio texas
        $this->getMovies('San Antonio', 'America/Chicago', 29.4724, -98.5251, $day);
        // san diego
        $this->getMovies('San Diego', 'America/Los_Angeles', 32.8153, -117.1350, $day);
        // dallas texas
        $this->getMovies('Dallas', 'America/Chicago', 32.7757, -96.7967, $day);
        // san jose california
        $this->getMovies('San Jose', 'America/Los_Angeles', 37.2969, -121.8193, $day);
    }

    public function getMovies($cityName, $timezone, $lat, $lon, $day) {
        Log::info('Get sessions');

        // Hard coded for boston
        $tomorrow = Carbon::$day($timezone);

        // Clear all sessions for that city
        $cinemas = Cinema::where('city', $cityName);
        foreach($cinemas as $cinema) {
            $startingAfter = Carbon::$day($cinema->timezone);
            $this->info('Clearing ' . $cinema->location . ' ' . $startingAfter->toDateTimeString());
            $endOfDay = $startingAfter->copy()->endOfDay();
            Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('start_time', '<=', $endOfDay->toDateTimeString())
                ->where('cinema_id', '<=', $cinema->id)
                ->delete();
        }

        $url = "http://data.tmsapi.com/v1.1/movies/showings?startDate=" . $tomorrow->toDateString() . "&numDays=1&lat=$lat&lng=$lon&radius=20&units=km&api_key=" . env('FANDANGO_API_KEY');
        $result = json_decode(@file_get_contents($url));
        foreach ($result as $movieElement) {
            Log::info('Get sessions for '.$movieElement->title);
            $this->getSessions($cityName, $timezone, $movieElement);
        }

    }

    public function getSessions($cityName, $timezone, $movieElement) {
        $now = Carbon::now()->toDateTimeString();
        $movie = Movie::firstOrCreate([
            'title' => $movieElement->title
        ]);

        $showings = [];
        foreach($movieElement->showtimes as $session){
            Log::info('Found Cinema '.$session->theatre->name);
            $cinema = Cinema::firstOrCreate([
                'location' => $session->theatre->name,
                'timezone' => $timezone,
                'city' => $cityName,
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