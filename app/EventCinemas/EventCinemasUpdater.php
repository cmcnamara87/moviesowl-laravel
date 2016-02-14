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

    public function update($day = 'tomorrow') {

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
            // clear all the session that are already there
            $startingAfter = Carbon::$day($cinema->timezone);
            $this->info('Clearing ' . $cinema->location . ' ' . $startingAfter->toDateTimeString());
            $endOfDay = $startingAfter->copy()->endOfDay();
            Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('start_time', '<=', $endOfDay->toDateTimeString())
                ->where('cinema_id', '<=', $cinema->id)
                ->delete();

            $this->updateMoviesAndShowings($cinema, $day);
            sleep(1);
        }
    }

    function getOrCreateCinema($eventCinema)
    {
        $cinemaName = $eventCinema->name  . ' Event';
        $cinema = Cinema::where('location', $cinemaName)->first();
        if (!$cinema) {
            $allCinemasLocationData = json_decode(file_get_contents(app_path() . '/EventCinemas/timezones.json'));
            if(!isset($allCinemasLocationData->{$eventCinema->name})) {
                Log::error('Missing cinema in timezones data ' . $eventCinema->name);
                return false;
            }
            $cinemaLocationData = $allCinemasLocationData->{$eventCinema->name};
            $cinema = Cinema::create([
                "location" => $cinemaName,
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


    public function updateMoviesAndShowings($cinema, $day = 'tomorrow')
    {
        Log::info($cinema->location);
        $now = Carbon::now()->toDateTimeString();
        $eventMovies = $this->eventCinemasApi->getMovies($cinema, $day);

        foreach ($eventMovies as $eventMovie) {
            $movie = $this->getOrCreateMovie($eventMovie);
            if(!$movie) {
                continue;
            }

            // Get all the showings all together
            $showings = array_map(function($session) use ($movie, $cinema, $now) {
                Log::info('Session: ' . $session->startTime->toDateTimeString());

                return [
                    "movie_id" => $movie->id,
                    "cinema_id" => $cinema->id,
                    "start_time" => $session->startTime->toDateTimeString(),
                    "screen_type" => $session->type, // gold class, vmax
                    "showing_type" => $session->sessionType, // 3d
                    "tickets_url" => $session->ticketsUrl,
                    "event_session_id" => $session->eventSessionId,
                    "cinema_size" => Showing::getScreenSizeFromSeats($session->seatsAvailable),
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