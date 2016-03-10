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

class FandangoUpdater
{

    public function update($day)
    {
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
        $this->getMovies('Chicago', 'America/Chicago', 41.8376, -87.6818, $day);
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


        // top 100 cities
        $this->getMovies("Austin", "America/Chicago", 30.3072, -97.756, $day);
        $this->getMovies("Jacksonville", "America/New_York", 30.337, -81.6613, $day);
        $this->getMovies("San Francisco", "America/Los_Angeles", 37.7751, -122.4193, $day);
        $this->getMovies("Indianapolis", "America/New_York", 39.7767, -86.1459, $day);
        $this->getMovies("Columbus", "America/New_York", 39.9848, -82.985, $day);
        $this->getMovies("Fort Worth", "America/Chicago", 32.7795, -97.3463, $day);
        $this->getMovies("Charlotte", "America/New_York", 35.2087, -80.8307, $day);
        $this->getMovies("Detroit", "America/New_York", 42.383, -83.1022, $day);
        $this->getMovies("El Paso", "America/Denver", 31.8484, -106.427, $day);
        $this->getMovies("Seattle", "America/Los_Angeles", 47.6205, -122.3509, $day);
//        $this->getMovies("Denver", "Colorado", 39.7618, -104.8806, $day);
//        $this->getMovies("Washington", "District of Columbia", 38.9041, -77.0171, $day);
//        $this->getMovies("Memphis", "Tennessee", 35.1035, -89.9785, $day);
//        $this->getMovies("Boston", "Massachusetts", 42.332, -71.0202, $day);
//        $this->getMovies("Nashville", "Tennessee", 36.1718, -86.785, $day);
//        $this->getMovies("Baltimore", "Maryland", 39.3002, -76.6105, $day);
//        $this->getMovies("Oklahoma City", "Oklahoma", 35.4671, -97.5137, $day);
//        $this->getMovies("Portland", "Oregon", 45.537, -122.65, $day);
//        $this->getMovies("Las Vegas", "Nevada", 36.2277, -115.264, $day);
//        $this->getMovies("Louisville", "Kentucky", 38.1781, -85.6667, $day);
//        $this->getMovies("Milwaukee", "Wisconsin", 43.0633, -87.9667, $day);
//        $this->getMovies("Albuquerque", "New Mexico", 35.1056, -106.6474, $day);
//        $this->getMovies("Tucson", "Arizona", 32.1543, -110.8711, $day);
//        $this->getMovies("Fresno", "California", 36.7827, -119.7945, $day);
//        $this->getMovies("Sacramento", "California", 38.5666, -121.4686, $day);
//        $this->getMovies("Long Beach", "California", 33.8091, -118.1553, $day);
//        $this->getMovies("Kansas City", "Missouri", 39.1252, -94.5511, $day);
//        $this->getMovies("Mesa", "Arizona", 33.4019, -111.7174, $day);
//        $this->getMovies("Atlanta", "Georgia", 33.7629, -84.4227, $day);
//        $this->getMovies("Virginia Beach", "Virginia", 36.7793, -76.024, $day);
//        $this->getMovies("Omaha", "Nebraska", 41.2647, -96.0419, $day);
//        $this->getMovies("Colorado Springs", "Colorado", 38.8673, -104.7607, $day);
//        $this->getMovies("Raleigh", "North Carolina", 35.8302, -78.6414, $day);
//        $this->getMovies("Miami", "Florida", 25.7752, -80.2086, $day);
//        $this->getMovies("Oakland", "California", 37.7699, -122.2256, $day);
//        $this->getMovies("Minneapolis", "Minnesota", 44.9633, -93.2683, $day);
//        $this->getMovies("Tulsa", "Oklahoma", 36.1279, -95.9023, $day);
//        $this->getMovies("Cleveland", "Ohio", 41.4781, -81.6795, $day);
//        $this->getMovies("Wichita", "Kansas", 37.6907, -97.3427, $day);
//        $this->getMovies("New Orleans", "Louisiana", 30.0686, -89.939, $day);
//        $this->getMovies("Arlington", "Texas", 32.7007, -97.1247, $day);
//        $this->getMovies("Bakersfield", "California", 35.3212, -119.0183, $day);
//        $this->getMovies("Tampa", "Florida", 27.9701, -82.4797, $day);
//        $this->getMovies("Aurora", "Colorado", 39.7082, -104.8235, $day);
//        $this->getMovies("Honolulu", "Hawai", 21.3259, -157.8453, $day);
//        $this->getMovies("Anaheim", "California", 33.8555, -117.7601, $day);
//        $this->getMovies("Santa Ana", "California", 33.7365, -117.8826, $day);
//        $this->getMovies("Corpus Christi", "Texas", 27.7543, -97.1734, $day);
//        $this->getMovies("Riverside", "California", 33.9381, -117.3932, $day);
//        $this->getMovies("StLouis", "Missouri", 38.6357, -90.2446, $day);
//        $this->getMovies("Lexington", "Kentucky", 38.0402, -84.4584, $day);
//        $this->getMovies("Pittsburgh", "Pennsylvania", 40.4398, -79.9766, $day);
//        $this->getMovies("Stockton", "California", 37.9763, -121.3133, $day);
//        $this->getMovies("Anchorage", "Alaska", 61.2176, -149.8953, $day);
//        $this->getMovies("Cincinnati", "Ohio", 39.1399, -84.5064, $day);
//        $this->getMovies("Saint Paul", "Minnesota", 44.9489, -93.1039, $day);
//        $this->getMovies("Greensboro", "North Carolina", 36.0965, -79.8271, $day);
//        $this->getMovies("Toledo", "Ohio", 41.6641, -83.5819, $day);
//        $this->getMovies("Newark", "New Jersey", 40.7242, -74.1726, $day);
//        $this->getMovies("Plano", "Texas", 33.0508, -96.7479, $day);
//        $this->getMovies("Henderson", "Nevada", 36.0122, -115.0375, $day);
//        $this->getMovies("Lincoln", "Nebraska", 40.809, -96.6804, $day);
//        $this->getMovies("Orlando", "Florida", 28.4159, -81.2988, $day);
//        $this->getMovies("Jersey City", "New Jersey", 40.7114, -74.0648, $day);
//        $this->getMovies("Chula Vista", "California", 32.6277, -117.0152, $day);
//        $this->getMovies("Buffalo", "New York", 42.8925, -78.8597, $day);
//        $this->getMovies("Fort Wayne", "Indiana", 41.0882, -85.1439, $day);
//        $this->getMovies("Chandler", "Arizona", 33.2829, -111.8549, $day);
//        $this->getMovies("StPetersburg", "Florida", 27.762, -82.6441, $day);
//        $this->getMovies("Laredo", "Texas", 27.5477, -99.4869, $day);
//        $this->getMovies("Durham", "North Carolina", 35.981, -78.9056, $day);
//        $this->getMovies("Irvine", "California", 33.6784, -117.7713, $day);
//        $this->getMovies("Madison", "Wisconsin", 43.0878, -89.4301, $day);
//        $this->getMovies("Norfolk", "Virginia", 36.923, -76.2446, $day);
//        $this->getMovies("Lubbock", "Texas", 33.5665, -101.8867, $day);
//        $this->getMovies("Gilbert", "Arizona", 33.3102, -111.7422, $day);
//        $this->getMovies("Winston-Salem", "North Carolina", 36.1033, -80.2606, $day);
//        $this->getMovies("Glendale", "Arizona", 33.5331, -112.1899, $day);
//        $this->getMovies("Reno", "Nevada", 39.4745, -119.7765, $day);
//        $this->getMovies("Hialeah", "Florida", 25.8699, -80.3029, $day);
//        $this->getMovies("Garland", "Texas", 32.9098, -96.6304, $day);
//        $this->getMovies("Chesapeake", "Virginia", 36.6794, -76.3018, $day);
//        $this->getMovies("Irving", "Texas", 32.8577, -96.97, $day);
//        $this->getMovies("North Las Vegas", "Nevada", 36.283, -115.0893, $day);
//        $this->getMovies("Scottsdale", "Arizona", 33.6687, -111.8237, $day);
//        $this->getMovies("Baton Rouge", "Louisiana", 30.4485, -91.1259, $day);
//        $this->getMovies("Fremont", "California", 37.4944, -121.9411, $day);
//        $this->getMovies("Richmond", "Virginia", 37.5314, -77.476, $day);
//        $this->getMovies("Boise", "Idaho", 43.5985, -116.2311, $day);
//        $this->getMovies("San Bernardino", "California", 34.1393, -117.2953, $day);
    }

    public function getMovies($cityName, $timezone, $lat, $lon, $day)
    {
        Log::info("Loading sessions $cityName $day");

        // Hard coded for boston
        $tomorrow = Carbon::$day($timezone);

        // Clear all sessions for that city
        $cinemas = Cinema::where('city', $cityName);
        $startingAfter = Carbon::$day($timezone);
        Log::info('Clearing ' . $timezone);
        $endOfDay = $startingAfter->copy()->endOfDay();
        Showing::where('start_time', ' >= ', $startingAfter->toDateTimeString())
            ->where('start_time', ' <= ', $endOfDay->toDateTimeString())
            ->whereHas('cinema', function($query) use ($cityName) {
                $query->whereIn('city', $cityName);
            })
            ->delete();

        $url = "http://data.tmsapi.com/v1.1/movies/showings?startDate=" . $tomorrow->toDateString() . "&numDays=1&lat=$lat&lng=$lon&radius=20&units=km&api_key=" . env('FANDANGO_API_KEY');
        $result = json_decode(@file_get_contents($url));
        $showings = [];
        foreach ($result as $movieElement) {
            Log::info('Get sessions for ' . $movieElement->title);
            $now = Carbon::now()->toDateTimeString();
            $movie = Movie::firstOrCreate([
                'title' => $movieElement->title
            ]);
            foreach ($movieElement->showtimes as $session) {
                Log::info('Found Cinema ' . $session->theatre->name);
                $cinema = Cinema::firstOrCreate([
                    'location' => $session->theatre->name,
                    'timezone' => $timezone,
                    'city' => $cityName,
                    'country' => 'United States'
                ]);

                $startTime = Carbon::parse($session->dateTime, $cinema->timezone);
                Log::info('Session time ' . $startTime->toDateTimeString());

                if (isset($session->ticketURI)) {
                    $ticketUrl = $session->ticketURI;
                    if (strpos($ticketUrl, 'fandango') !== false) {
                        $pid = '7990990';
                        $linkId = "10576771";
                        $stuff = "&wssaffid=11836&wssac=123";
                        $encodedUrl = urlencode($ticketUrl . $stuff);
                        $ticketUrl = "http://www.qksrv.net/click-$pid-$linkId?url=$encodedUrl";
                    }
                } else {
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
        }
        // Chunk it for mass insert
        $showingsChunks = array_chunk($showings, 100);
        foreach ($showingsChunks as $chunk) {
            DB::table('showings')->insert($chunk);
        }
    }


}