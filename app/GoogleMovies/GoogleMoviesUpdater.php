<?php
/**
 * Created by PhpStorm.
 * User: craig
 * Date: 13/10/15
 * Time: 12:46 AM
 */

namespace MoviesOwl\GoogleMovies;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManagerStatic as Image;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\EventCinemas\EventCinemasUpdater;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
use MoviesOwl\Showings\Showing;
use Yangqi\Htmldom\Htmldom;

class GoogleMoviesUpdater {

    public function update($day = 'tomorrow')
    {
        Log::info('Updating from Google Movies for Brisbane');
        $this->updateForCity('Brisbane', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Sydney', 'Australia', 'Australia/Sydney', $day);
        $this->updateForCity('Gosford', 'Australia', 'Australia/Sydney', $day);
        $this->updateForCity('Newcastle', 'Australia', 'Australia/Sydney', $day);
        $this->updateForCity('Melbourne', 'Australia', 'Australia/Melbourne', $day);
        $this->updateForCity('Adelaide', 'Australia', 'Australia/Adelaide', $day);
        $this->updateForCity('Perth', 'Australia', 'Australia/Perth', $day);
        $this->updateForCity('Darwin', 'Australia', 'Australia/Darwin', $day);
        $this->updateForCity('Cairns', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Canberra', 'Australia', 'Australia/Canberra', $day);

        // New Cinemas 10/2/16
        $this->updateForCity('Gold Coast', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Wollongong', 'Australia', 'Australia/Sydney', $day);
        $this->updateForCity('Central Coast', 'Australia', 'Australia/Sydney', $day);
        $this->updateForCity('Hobart', 'Australia', 'Australia/Hobart', $day);
        $this->updateForCity('Geelong', 'Australia', 'Australia/Victoria', $day);
        $this->updateForCity('Townsville', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Cairns', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Toowoomba', 'Australia', 'Australia/Brisbane', $day);
        $this->updateForCity('Ballarat', 'Australia', 'Australia/Melbourne', $day);
        $this->updateForCity('Bendigo', 'Australia', 'Australia/Melbourne', $day);
        $this->updateForCity('Albury', 'Australia', 'Australia/Sydney', $day);
    }

    public function updateForCity($city, $country, $timezone, $day)
    {
        $startingAfter = Carbon::$day($timezone);
        $endOfDay = $startingAfter->copy()->endOfDay();

        // 0 = today, 1 = tomorrow
        if($day == 'today') {
            $date = 0;
        } else {
            $date = 1;
        }
        $html = $this->getPage($city, $country, 0, $date);
        $this->processPage($html, $city, $country, $timezone, $day);

        // how many pages
        $pageCount = count($html->find('#navbar td')) - 2;

        // process the rest of the pages
        for($page = 1; $page < $pageCount; $page++) {
            $html = $this->getPage($city, $country, $page, $date);
            $this->processPage($html, $city, $country, $timezone, $day);
            sleep(2);
        }
    }

    public function processPage($html, $city, $country, $timezone, $day)
    {
        $now = Carbon::now()->toDateTimeString();

        $showings = collect([]);
        foreach ($html->find('.theater') as $cinemaElement) {
            $cinemaNameElement = $cinemaElement->find('h2.name a', 0);
            if(!$cinemaNameElement) {
                Log::error('Couldnt find a cinema name...thats not good');
                continue;
            }
            $cinemaName = $cinemaNameElement->plaintext;
            Log::info('Cinema: ' . $cinemaName);


            $cinema = Cinema::firstOrCreate([
                'location' => $cinemaName,
                'timezone' => $timezone,
                'city' => $city,
                'country' => $country
            ]);
            $startingAfter = Carbon::$day($cinema->timezone);
            Log::info('Clearing ' . $cinema->location . ' ' . $startingAfter->toDateTimeString());
            $endOfDay = $startingAfter->copy()->endOfDay();
            Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('start_time', '<=', $endOfDay->toDateTimeString())
                ->where('cinema_id', $cinema->id)
                ->delete();

            foreach ($cinemaElement->find('.movie') as $movieElement) {
                $title = html_entity_decode($movieElement->find('.name a', 0)->plaintext);
                $title = str_replace('&#39;', "'", $title);
                $movie = Movie::firstOrCreate([
                    'title' => $title
                ]);
                
//                Log::info('Movie: ' . $movie->title);

                $isPm = false;

                $showTimeElements = array_reverse($movieElement->find('.times span[style^="color:"]'));
                // Get all the showings all together

                foreach($showTimeElements as $showingElement) {
                    $timeString = $showingElement->plaintext;
                    $parts = explode(':', $timeString);

                    if (strpos($timeString, 'pm') !== false) {
                        $isPm = true;
                    }
                    if (strpos($timeString, 'am') !== false) {
                        $isPm = false;
                    }
                    $hours = intval(preg_replace("/[^0-9 ]/", '', $parts[0]));
                    if ($isPm && $hours != 12) {
                        $hours += 12;
                    }
                    $minutes = intval($parts[1]);
                    $timestamp = Carbon::$day($cinema->timezone)->timestamp + ($hours * 60 * 60) + ($minutes * 60);
                    $startTime = Carbon::createFromTimestamp($timestamp, $cinema->timezone);

//                    Log::info('Session: ' . $startTime->toDateTimeString());
                    $showings->push([
                        "movie_id" => $movie->id,
                        "cinema_id" => $cinema->id,
                        "start_time" => $startTime->toDateTimeString(),
                        'created_at' => $now,
                        'updated_at' => $now,
                        "tickets_url" => '',
                    ]);
                }
            }
        }

        // Chunk it for mass insert
        $showingsChunks = $showings->chunk(100)->toArray();
        foreach($showingsChunks as $chunk) {
            DB::table('showings')->insert($chunk);
        }
    }

    /**
     * Gets the HTML for the page
     * @param $city
     * @param $country
     * @param $page
     * @param $date
     * @return array
     */
    private function getPage($city, $country, $page, $date)
    {
        $url = "http://www.google.com/movies?near=" . urlencode($city . ' ' . $country) . "&start=" . ($page * 10) . "&date=$date";
        $googleMovies = @file_get_contents($url);
        return new Htmldom($googleMovies);
    }
}