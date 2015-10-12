<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use MoviesOwl\Movies\Movie;
use MoviesOwl\Posters\PosterService;

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
    protected $description = 'Command description.';

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
        $movies = Movie::where('imdb_id', '=', '')->get();
        foreach($movies as $movie) {
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
                continue;
            }
            $asset = $this->posterService->savePosterFromUrl($url, $movie->title);
            Log::info('Saved poster to disk');
            if (!$asset) {
                continue;
            }
            $movieDetails = $movie->details;
            $movieDetails->poster = $asset;
            $movieDetails->save();
            $movie->save();
            Log::info('Movie updated');
        }
    }
}
