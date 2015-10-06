<?php

namespace MoviesOwl\Console\Commands;

use Illuminate\Console\Command;
use MoviesOwl\Movies\Movie;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
            $imdbId = $this->ask($movie->title . ' (IMDB ID)');
            if(!$imdbId) {
                continue;
            }
            if(strlen($imdbId) != 7) {
                $this->error('Invalid Imdb ID should be 7 numbers');
                continue;
            }
            if(!is_numeric($imdbId)) {
                $this->error('Invalid Imdb ID - ID should be all numbers');
                continue;
            }
            $movie->imdb_id = $imdbId;
            $movie->save();
        }
    }
}
