<?php

namespace MoviesOwl\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Movies\MovieDetails;
use MoviesOwl\Movies\MovieDetailsUpdater;
use MoviesOwl\OMDB\OMDBApi;
use MoviesOwl\Posters\PosterService;
use MoviesOwl\RottenTomatoes\RottenTomatoesApi;
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
    protected $rottenTomatoesApi;
    protected $movieDetailsUpdater;
    protected $OMDBApi;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PosterService $posterService,
                                RottenTomatoesApi $rottenTomatoesApi,
                                MovieDetailsUpdater $movieDetailsUpdater,
                                OMDBApi $OMDBApi)
    {
        parent::__construct();
        $this->posterService = $posterService;
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->movieDetailsUpdater = $movieDetailsUpdater;
        $this->OMDBApi = $OMDBApi;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles('php://stdout');

        // Get movie posters for movies that are on today
        // Do this every day
        $startOfDay = Carbon::today();
        $endOfDay = $startOfDay->copy()->tomorrow()->endOfDay();


        $movieIds = DB::table('showings')
            ->select('movie_id', DB::raw('count(*) as total'))
            ->where('start_time', '>=', $startOfDay->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->groupBy('movie_id')
            ->orderBy('total', 'desc')
            ->lists('movie_id');

//        $movieIds =  Showing::where('start_time', '>=', $startOfDay->toDateTimeString())
//            ->where('start_time', '<=', $endOfDay->toDateTimeString())->distinct()->lists('movie_id');

        $movies = Movie::whereIn('id', $movieIds)
            ->where('imdb_id', '=', '')
            ->orderBy('id', 'desc')->get();

        foreach($movies as $movie) {
            $this->updateImdbId($movie);
        }
    }

    private function updateImdbId($movie) {
        $this->info($movie->title);

        // running rotten tomatoes search
        $this->info('Checking rotten tomatoes');
        $response = $this->rottenTomatoesApi->getMovies($movie->title);

        if(isset($response->movies) && count($response->movies)) {
            $index = 0;
            foreach($response->movies as $rtMovie) {
                $this->info(($index + 1) . ' ' . $rtMovie->title . ' ' . $rtMovie->year . ' ' . $rtMovie->id);
                $index += 1;
            }
            $rtIndex = $this->ask('Select a movie', false);
            if($rtIndex) {
                $movie->rotten_tomatoes_id = $response->movies[$rtIndex - 1]->id;
                $movie->save();
                $this->movieDetailsUpdater->updateMovie($movie);
                $movie = Movie::find($movie->id);
            }
        }


        // No need to continue, its all done
        if($movie->imdb_id) {
            return;
        }

        // Setup IMDB
        $url = null;

        $this->info('Checking omdb');
        $response = $this->OMDBApi->getMovies($movie->title);
        if(isset($response->Search) && count($response->Search)) {
            $index = 0;
            foreach($response->Search as $omdbMovie) {
                $this->info(($index + 1) . ' ' . $omdbMovie->Title . ' ' . $omdbMovie->Year . ' ' . $omdbMovie->imdbID);
                $index += 1;
            }
            $movieIndex = $this->ask('Select a movie', false);
            if($movieIndex) {
                $movie->imdb_id = $response->Search[$movieIndex - 1]->imdbID;
                $url = $this->posterService->getImdbPosterUrl($movie->imdb_id);
            }
        }

        // No imdb yet, ask manually
        if(!$movie->imdb_id) {
            $imdbId = $this->ask($movie->title . ' (Manual IMDB ID)', false);
            if($imdbId) {
                $movie->imdb_id = $imdbId;
                $url = $this->posterService->getImdbPosterUrl($movie->imdb_id);
            }
        }
        Log::info('ID ' . $movie->imdb_id);

        // No url yet
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
