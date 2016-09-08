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
use MoviesOwl\RottenTomatoes\RottenTomatoesService;
use MoviesOwl\Showings\Showing;
use MoviesOwl\Trailer\TrailerService;

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
    protected $trailerService;
    protected $rottenTomatoesService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PosterService $posterService,
                                RottenTomatoesApi $rottenTomatoesApi,
                                MovieDetailsUpdater $movieDetailsUpdater, TrailerService $trailerService,
                                RottenTomatoesService $rottenTomatoesService, OMDBApi $OMDBApi)
    {
        parent::__construct();
        $this->posterService = $posterService;
        $this->rottenTomatoesApi = $rottenTomatoesApi;
        $this->movieDetailsUpdater = $movieDetailsUpdater;
        $this->OMDBApi = $OMDBApi;
        $this->trailerService = $trailerService;
        $this->rottenTomatoesService = $rottenTomatoesService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles('php://stdout');

        // Get movie posters for movies that are on today and tomorrow
        $startOfDay = Carbon::yesterday();
        $endOfDay = $startOfDay->copy()->tomorrow()->endOfDay();

        // Get the movies with the most showings (those are a priority, vs the one off movies)
        $movieIds = DB::table('showings')
            ->select('movie_id', DB::raw('count(*) as total'))
            ->where('start_time', '>=', $startOfDay->toDateTimeString())
            ->where('start_time', '<=', $endOfDay->toDateTimeString())
            ->groupBy('movie_id')
            ->orderBy('total', 'desc')
            ->get();

        $movies = array_reduce($movieIds, function ($carry, $movieId) {
            $movie = Movie::find($movieId->movie_id);
            $this->info($movie->title);
            $this->info($movie->title . ' ' .
                $movie->rotten_tomatoes_id . ' ' .
                $movie->imdb_id . ' ');
            if ($movie->rotten_tomatoes_id == '' || $movie->rotten_tomatoes_id == 0 || $movie->imdb_id == ''
                || (isset($movie->details) && $movie->details && strpos($movie->details->poster, 'no_poster') !== false)
            ) {
                $this->info('- Missing ' . $movie->title . ' ' . $movieId->total);
                $carry[] = $movie;
            }
            return $carry;
        }, []);


        // Update each movie
        foreach ($movies as $movie) {
            $this->updateImdbId($movie);
        }
    }

    private function updateImdbId($movie)
    {
        $this->info($movie->title);

        // running rotten tomatoes search
        $movie = $this->updateRottenTomatoes($movie);

        $url = null;

        // Setup IMDB
        if ($movie->imdb_id) {
            $this->info('Already has IMDb');
        } else {
            $this->info('Checking IMDB');
            $imdbId = $this->getImdbId($movie->title);
            if (!$imdbId) {
                $movieTitle = $this->ask('No matches, enter movie title', false);
                if ($movieTitle) {
                    $imdbId = $this->getImdbId($movieTitle);
                }
            }
            // No imdb yet, ask manually
            if (!$imdbId) {
                $imdbId = $this->ask($movie->title . ' (Manual IMDB ID)', false);
            }
            if ($imdbId) {
                $movie->imdb_id = $imdbId;
                $movie->save();
            }
        }

        // Update IMDB
        Log::info('ID ' . $movie->imdb_id);

        // does the poster exists
        $url = '';
        if ($this->posterService->exists($movie->title)) {
            $this->info('Already has poster');
            $asset = $this->posterService->getAssetPath($movie->title);
            $widePosterAsset = $this->posterService->getAssetPath($movie->title . "-wide");
            $trailerUrl = $movie->trailer;
        } else {
            $widePosterUrl = false;
            if ($movie->imdb_id) {
                $url = $this->posterService->getImdbPosterUrl($movie->imdb_id);
                $widePosterUrl = $this->posterService->getWidePosterUrl($movie->imdb_id);
                $trailerUrl = $this->trailerService->getTrailerUrl($movie->imdb_id);
            }
            // No url yet
            if (!$url) {
                $url = $this->ask($movie->title . ' (URL)', false);
            }
            if (!$url) {
                return;
            }
            $asset = $this->posterService->savePosterFromUrl($url, $movie->title);
            $widePosterAsset = false;
            if ($widePosterUrl) {
                $widePosterAsset = $this->posterService->savePosterFromUrl($widePosterUrl, $movie->title . "-wide");
            }

            Log::info('Saved poster to disk');
            if (!$asset) {
                return;
            }
            if (is_null($movie->details) || !count($movie->details) || !$movie->details) {
                // Create a stub movie details if we havent got one
                MovieDetails::create([
                    'title' => $movie->title,
                    'movie_id' => $movie->id
                ]);
            }
        }
        $movieDetails = $movie->details;
        $movieDetails->poster = $asset;
        if ($widePosterAsset) {
            $movieDetails->wide_poster = $widePosterAsset;
        }
        if (isset($trailerUrl)) {
            $movieDetails->trailer = $trailerUrl;
        }
        $movieDetails->save();
        $movie->save();

        Log::info('Movie updated');
    }

    /**
     * @param $movieTitle
     * @return array
     */
    private function getRottenTomatoesId($movieTitle)
    {
        $this->info('Searching Rotten Tomatoes for ' . $movieTitle);
        $response = $this->rottenTomatoesApi->getMovies($movieTitle);

        if (isset($response->movies) && count($response->movies)) {
            $index = 0;
            foreach ($response->movies as $rtMovie) {
                $this->info(($index + 1) . ' ' . $rtMovie->year . ' ' . $rtMovie->title . ' ' . $rtMovie->id);
                $index += 1;
                if($rtMovie->year == '2016' && $rtMovie->title == $movieTitle) {
                    $rtIndex = $index;
                }
            }
            if(!isset($rtIndex)) {
                foreach ($response->movies as $rtMovie) {
                    if($rtMovie->year == '2015' && $rtMovie->title == $movieTitle) {
                        $rtIndex = $index;
                    }
                }
            }
            if (isset($rtIndex)) {
                // rotten tomatoes id
                return $response->movies[$rtIndex - 1]->id;
            }
            $rtIndex = $this->ask('Select a movie', false);
            if ($rtIndex) {
                // rotten tomatoes id
                return $response->movies[$rtIndex - 1]->id;
            }
        }
        return false;
    }

    /**
     * @param $movieTitle
     * @return mixed|null
     */
    private function getImdbId($movieTitle)
    {
        $this->info('Searching IMDB for ' . $movieTitle);
        $response = $this->OMDBApi->getMovies($movieTitle);
        if (isset($response->Search) && count($response->Search)) {
            $index = 0;

            foreach ($response->Search as $omdbMovie) {
                $this->info(($index + 1) . ' ' . $omdbMovie->Year . ' ' . $omdbMovie->Title . ' ' . $omdbMovie->imdbID);
                $index += 1;
                if ($omdbMovie->Year == '2016' && $omdbMovie->Title == $movieTitle) {
                    $movieIndex = $index;
                }
            }
            if(!isset($rtIndex)) {
                foreach ($response->movies as $rtMovie) {
                    if ($omdbMovie->Year == '2015' && $omdbMovie->Title == $movieTitle) {
                        $movieIndex = $index;
                    }
                }
            }
            if(!isset($movieIndex)) {
                $movieIndex = $this->ask('Select a movie', false);
            }
            if ($movieIndex) {
                // imdb id
                return $response->Search[$movieIndex - 1]->imdbID;
            }
        }
        return false;
    }

    /**
     * @param $movie
     * @return array
     */
    private function updateRottenTomatoes($movie)
    {
        $this->info('Updating Rotten Tomatoes');
        if ($movie->rotten_tomatoes_id) {
            return $this->rottenTomatoesService->updateMovie($movie);
        }
        $movieTitle = $movie->title;

        $rottenTomatoesId = $this->getRottenTomatoesId($movieTitle);
        if (!$rottenTomatoesId) {
            $movieTitle = $this->ask('No matches, enter movie title', false);
            if ($movieTitle) {
                $rottenTomatoesId = $this->getRottenTomatoesId($movieTitle);
            }
        }
        if (!$rottenTomatoesId) {
            return $movie;
        }
        $movie->rotten_tomatoes_id = $rottenTomatoesId;
        $movie->save();
        return $this->rottenTomatoesService->updateMovie($movie);
    }
}
