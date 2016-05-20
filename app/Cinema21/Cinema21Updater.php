<?php
namespace MoviesOwl\Cinema21;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
use MoviesOwl\RottenTomatoes\RottenTomatoesService;
use MoviesOwl\Showings\Showing;
use Yangqi\Htmldom\Htmldom;
use Carbon\Carbon;

class Cinema21Updater
{
    protected $rottenTomatoesService;


    /**
     * Cinema21Updater constructor.
     */
    public function __construct(RottenTomatoesService $rottenTomatoesService)
    {
        $this->rottenTomatoesService = $rottenTomatoesService;
    }

    public function update($day){
        $api = new Cinema21Api();
        try {
            $cinemasData = $api->getCinemas();
        } catch(ClientException $e) {
            Log::info("Failed to load " . $e->getMessage());
        }

        foreach ($cinemasData as $cinemaData) {

            Log::info('Cinema: ' . $cinemaData->cinema_name);

            $cinema = Cinema::firstOrCreate([
                'location' => $cinemaData->cinema_name,
                'timezone' => 'Asia/Jakarta',
                'city' => 'Jakarta',
                'country' => 'Indonesia'
            ]);
            //TODO: add cinema id

            $startingAfter = Carbon::today($cinema->timezone);
            Log::info('Clearing old ' . $cinema->location . ' ' . $startingAfter->toDateTimeString());
            $endOfDay = $startingAfter->copy()->endOfDay();
            Showing::where('start_time', '>=', $startingAfter->toDateTimeString())
                ->where('cinema_id', $cinema->id)
                ->delete();

            try {
                Log::info("Getting sessions");
                $moviesData = $api->getMovies($cinemaData->cinema_id);
            } catch(Exception $e) {
                Log::error('error???');
            }
            if(!isset($moviesData)) {
                continue;
            }


            foreach($moviesData as $movieData){
                $movie = Movie::firstOrCreate([
                    'title' => $movieData->title
                ]);

                foreach ($movieData->date_show as $dateIndex => $date){
                    $times = $movieData->time_show[$dateIndex];
                    foreach($times as $timeIndex => $time){

                        $startTime = Carbon::parse(implode("-", array_reverse(explode("-", $date))) . ' ' .
                            $time, $cinema->timezone);

                        Showing::create([
                            "movie_id" => $movie->id,
                            "cinema_id" => $cinema->id,
                            "start_time" => $startTime->toDateTimeString(),
                            "tickets_url" => '',
                            "data" => json_encode([
                                "cinema_id" => $cinemaData->cinema_id,
                                "movie_id" => $movieData->movie_id,
                                "date_show" => $date,
                                "time_show" => $time,
                                "studio_id" => $movieData->studio_id[$dateIndex][$timeIndex]
                            ])
                        ]);
                    }
                }
            }
            sleep(1);
        }


        Log::info('Updating Movie Details For Indonesia for today');
        $startOfDay = Carbon::today();
        $endOfDay = $startOfDay->copy()->endOfDay();

        $indonesianCinemaIds = Cinema::where('city', 'Jakarta')->lists('id');

        $movieIds =  Showing::where('start_time', '>=', $startOfDay->toDateTimeString())
            ->whereIn('cinema_id', $indonesianCinemaIds)
            ->where('start_time', '<=', $endOfDay->toDateTimeString())->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)->get();

        foreach($movies as $movie) {
            // map movie title, to a real movie

            // get details from rotten tomatoes
            $this->rottenTomatoesService->updateMovie($movie);

            sleep(1);
        }


    }
}