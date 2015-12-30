<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 2/01/2015
 * Time: 3:14 PM
 */

namespace MoviesOwl\EventCinemas;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MoviesOwl\EventCinemas\EventCinemasApi;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
use MoviesOwl\Showings\Showing;
use Illuminate\Support\Facades\Log;
use MoviesOwl\OMDB\OMDBApi;
use Intervention\Image\ImageManagerStatic as Image;
use Symfony\Component\Console\Output\ConsoleOutput;

class EventCinemasUpdater {

    protected $eventCinemasApi;
    protected $rottenTomatoesApi;
    protected $omdbApi;
    protected $output;

    function __construct(EventCinemasApi $eventCinemasApi, ConsoleOutput $output)
    {
        $this->eventCinemasApi = $eventCinemasApi;
        $this->output = $output;
    }

    public function update() {

        Log::info("Updating cinemas:");
        // Update cinemas list
        $eventCinemas = $this->eventCinemasApi->getCinemas();

        $cinemas = array_map(function($eventCinema) {
            return $this->getOrCreateCinema($eventCinema);
        }, $eventCinemas);

        foreach($cinemas as $cinema) {
            if(!$cinema) {
                continue;
            }
            $this->updateMoviesAndShowings($cinema);
        }
    }

    function getOrCreateCinema($eventCinema)
    {
        $cinema = Cinema::where('location', $eventCinema->name)->first();
        if (!$cinema) {
            $allCinemasLocationData = json_decode(file_get_contents(app_path() . '/EventCinemas/timezones.json'));
            if(!isset($allCinemasLocationData->{$eventCinema->name})) {
                Log::error('Missing cinema in timezones data ' . $eventCinema->name);
                return false;
            }
            $cinemaLocationData = $allCinemasLocationData->{$eventCinema->name};
            $cinema = Cinema::create([
                "location" => $eventCinema->name . ' Event',
                "eventcinema_id" => $eventCinema->id,
                "city" => $cinemaLocationData->city,
                "timezone" => $cinemaLocationData->timezone,
                "country" => $cinemaLocationData->country
            ]);
        }
        return $cinema;
    }

    /**
     * @param $eventMovie
     * @return mixed
     */
    public function getOrCreateMovie($eventMovie)
    {
        return Movie::firstOrCreate(array('title' => $eventMovie->title));
    }



//    /**
//     * @param EventCinemasSession $session
//     * @param Cinema $cinema
//     * @param Movie $movie
//     * @return
//     * @internal param $startTime
//     */
//    public function getOrCreateShowing(EventCinemasSession $session, Cinema $cinema, Movie $movie)
//    {
//        $showing = Showing::firstOrCreate(array('event_session_id' => $session->eventSessionId));
//        $showing->fill([
//            "movie_id" => $movie->id,
//            "cinema_id" => $cinema->id,
//            "start_time" => $session->startTime,
//            "screen_type" => $session->type,
//            "showing_type" => $session->sessionType,
//            "tickets_url" => $session->ticketsUrl,
//            "event_session_id" => $session->eventSessionId
//        ]);
//        $showing->save();
//        return $showing;
//    }

    /**
     * @param $cinema
     */
    public function updateMoviesAndShowings($cinema)
    {
        Log::info($cinema->location);
        $now = Carbon::now()->toDateTimeString();
        $eventMovies = $this->eventCinemasApi->getMovies($cinema);

        foreach ($eventMovies as $eventMovie) {
            $movie = $this->getOrCreateMovie($eventMovie);
            if(!$movie) {
                continue;
            }

            // Get all the showings all together
            $showings = array_map(function($session) use ($movie, $cinema, $now) {
                return [
                    "movie_id" => $movie->id,
                    "cinema_id" => $cinema->id,
                    "start_time" => $session->startTime,
                    "screen_type" => $session->type,
                    "showing_type" => $session->sessionType,
                    "tickets_url" => $session->ticketsUrl,
                    "event_session_id" => $session->eventSessionId,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }, $eventMovie->sessions);

            // Chunk it for mass insert
            $showingsChunks = array_chunk($showings, 100);
            foreach($showingsChunks as $chunk) {
                DB::table('showings')->insert($chunk);
            }
        }
    }
}