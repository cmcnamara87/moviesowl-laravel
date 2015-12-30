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

    public function update()
    {
        Log::info('Updating from Google Movies for Brisbane');
        $this->updateForCity('Brisbane', 'Australia', 'Australia/Brisbane');
        $this->updateForCity('Sydney', 'Australia', 'Australia/Sydney');
        $this->updateForCity('Melbourne', 'Australia', 'Australia/Melbourne');
        $this->updateForCity('Adelaide', 'Australia', 'Australia/Adelaide');
        $this->updateForCity('Perth', 'Australia', 'Australia/Perth');
        $this->updateForCity('Darwin', 'Australia', 'Australia/Darwin');
        $this->updateForCity('Cairns', 'Australia', 'Australia/Brisbane');
        $this->updateForCity('Canberra', 'Australia', 'Australia/Canberra');
    }

    public function updateForCity($city, $country, $timezone)
    {
        $page = 0;
        // 0 = today, 1 = tomorrow
        $date = 0;
        do {
            $url = "http://www.google.com/movies?near=" . urlencode($city . ' ' . $country) . "&start=" . ($page * 10) . "&date=$date";
            $googleMovies = @file_get_contents($url);
            $html = new Htmldom($googleMovies);
            // how many pages
            $pageCount = count($html->find('#navbar td')) - 2;
            $this->processPage($html, $city, $country, $timezone);
            $page++;
        } while($page < $pageCount);
    }

    /**
     * @param $html
     */
    public function processPage($html, $city, $country, $timezone)
    {
// Find all article blocks
        foreach ($html->find('.theater') as $cinemaElement) {
            $cinemaName = $cinemaElement->find('h2.name a', 0)->plaintext;
            Log::info('Cinema: ' . $cinemaName);
            if (strpos($cinemaName, 'Event') !== false) {
                Log::info('- Skipping Event');
                continue;
            }
            $cinema = Cinema::firstOrCreate([
                'location' => $cinemaName,
                'timezone' => $timezone,
                'city' => $city,
                'country' => $country
            ]);

            foreach ($cinemaElement->find('.movie') as $movieElement) {
                $movie = Movie::firstOrCreate([
                    'title' => html_entity_decode($movieElement->find('.name a', 0)->plaintext)
                ]);
                Log::info('Movie: ' . $movie->title);

                $isPm = false;
                foreach (array_reverse($movieElement->find('.times span[style^="color:"]')) as $showingElement) {
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
                    $timestamp = Carbon::today()->timestamp + ($hours * 60 * 60) + ($minutes * 60);
                    $startTime = Carbon::createFromTimestamp($timestamp, $cinema->timezone);

                    Log::info('Session: ' . $startTime->toDateTimeString());
                    $showing = Showing::firstOrCreate([
                        "movie_id" => $movie->id,
                        "cinema_id" => $cinema->id,
                        "start_time" => $timestamp
                    ]);
                }
            }
        }
    }
}