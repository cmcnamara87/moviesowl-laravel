<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\Showings\Showing;

class AddMissingImdbIdsCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:imdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load details from IMDB.';

    protected $posterService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PosterService $posterService)
    {
        parent::__construct();
        $this->posterService = $posterService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get movie posters for movies that are on today
        // Do this every day
        $startOfDay = Carbon::today();
        $endOfDay = $startOfDay->copy()->endOfDay();

        $movieIds =  Showing::where('start_time', '>=', $startOfDay->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)
            ->where('imdb_id', '=', '')
            ->orderBy('id', 'desc')->get();

        foreach($movies as $movie) {
            $this->updateImdbId($movie);
        }
    }

    private function updateImdbId($movie) {
        $imdbId = $this->ask($movie->title . ' (IMDB ID)', false);
        $url = null;
        if($imdbId) {
            $movie->imdb_id = $imdbId;
            $url = $this->posterService->getImdbPosterUrl($imdbId);
        }
        Log::info('ID ' . $movie->imdb_id);
        if (!$url) {
            $url = $this->ask($movie->title . ' (URL)', false);
        }
        if(!$url) {
            return;
        }
        $asset = $this->posterService->savePosterFromUrl($url, $movie->title);
        Log::info('Saved poster to disk');
        if (!$asset) {
            return;
        }
        if(is_null($movie->details) || !count($movie->details)) {
            // Create a stub movie details if we havent got one
            MovieDetails::create([
                'title' => $movie->title,
                'movie_id' => $movie->id
            ]);
        }
        $movieDetails = $movie->details;
        $movieDetails->poster = $asset;
        $movieDetails->save();
        $movie->save();
        Log::info('Movie updated');
    }
}
