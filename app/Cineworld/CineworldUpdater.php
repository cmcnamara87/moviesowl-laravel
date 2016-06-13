<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 30/01/16
 * Time: 12:23 PM
 */

namespace MoviesOwl\Cineworld;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Showings\Showing;

class CineworldUpdater
{

    public function update($day)
    {
        Log::info('Updating Cineworld');
//        curl -x https://51.254.218.165:8888 -L http://www.cineworld.co.uk/syndication/listings.xml

        $countries = [
            [
                "url" => "http://www.cineworld.co.uk/syndication/listings_ie.xml",
                "country" => "Ireland",
                "timezone" => "Europe/London"
            ],
            [
                "url" => "http://www.cineworld.co.uk/syndication/listings.xml",
                "country" => "United Kingdom",
                "timezone" => "Europe/London"
            ]
        ];

        foreach($countries as $countryData) {
            Log::info('Country: ' . $countryData['country']);
            $curl_scraped_page = $this->getWithProxy($countryData['url']);

            $data = new \SimpleXMLElement($curl_scraped_page);

            foreach($data->cinema as $cinemaData) {
                Log::info('Cinema: ' . $cinemaData['name']);

                $addressParts = explode(', ', $cinemaData['address']);
                $cityName = end($addressParts);

                // remove cineworld at front of names
                $cinemaName = str_replace("Cineworld $cityName -", '', $cinemaData['name']);
                // remove cineworld at front of names
                $cinemaName = str_replace("Cineworld ", '', $cinemaName);
                $cinemaName = trim("$cinemaName, Cineworld $cityName");

                $cinema = Cinema::firstOrCreate([
                    'location' => $cinemaName,
                    'timezone' => $countryData['timezone'],
                    'city' => $cityName,
                    'country' => $countryData['country']
                ]);

                $startingAfter = Carbon::today($cinema->timezone);
                Log::info('Clearing old ' . $cinema->location . ' ' . $startingAfter->toDateTimeString());
                Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                    ->where('cinema_id', $cinema->id)
                    ->delete();


                foreach($cinemaData->listing->film  as $movieData){
                    $movie = Movie::firstOrCreate([
                        'title' => $movieData['title']
                    ]);

                    foreach ($movieData->shows->show as $showingData){
                        Showing::create([
                            "movie_id" => $movie->id,
                            "cinema_id" => $cinema->id,
                            "start_time" => Carbon::parse($showingData['time'], $countryData['timezone']),
                            "tickets_url" => $showingData['url']
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    private function getWithProxy($url)
    {
        // proxy from www.freeproxylists.net/?c=GB&pt=&pr=&a%5B%5D=0&a%5B%5D=1&a%5B%5D=2&u=0
        $proxy = 'https://51.254.218.165:8888';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $curl_scraped_page = curl_exec($ch);
        curl_close($ch);
        return $curl_scraped_page;
    }


}