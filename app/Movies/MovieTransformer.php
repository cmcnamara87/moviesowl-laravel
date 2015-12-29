<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 31/12/2014
 * Time: 3:39 PM
 */

namespace MoviesOwl\Movies;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;
use Illuminate\Support\Facades\App;
use MoviesOwl\Showings\MovieShowingTransformer;
use Carbon\Carbon;
use League\Fractal\ParamBag;
use Illuminate\Support\Facades\Route;
class MovieTransformer extends TransformerAbstract {

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'showings'
    ];

    protected $showingRepo;

    function __construct()
    {
        // FIXME: i dont know how to inject into these transformers
        $this->showingRepo = App::make('MoviesOwl\Repos\Showing\ShowingRepository');
    }


    public function transform(Movie $movie) {
        if(!count($movie->details)) {
            Log::error('No details for ' . $movie->title);
            return [
                'id' => (int) $movie->id,
                'title' => $movie->title
            ];
        }

        $new = $movie->showings()->where('start_time', '>', Carbon::yesterday())
            ->where('start_time', '<', Carbon::today())->count() > 0;

        return [
            'id' => (int) $movie->id,
            'title' => $movie->title,
            'new' => $new,
            'poster' => $movie->details->poster,
            'tomato_meter' => $movie->details->{"tomato_meter"},
            "synopsis" => $movie->details->synopsis,
            "run_time" => $movie->details->{"run_time"},
            "cast" => $movie->details->cast,
            "director" => $movie->details->director,
            'created_at' => $movie->created_at->timestamp,
            'genre' => $movie->details->genre,
            'critics' => ""
        ];
    }

    /**
     * Include Author
     *
     * @return League\Fractal\ItemResource
     */
    public function includeShowings(Movie $movie, $params)
    {
//        $cinema = $this->getCinemaId($params);
//        $startingAfter = $this->getStartingAfter();
//        $showings = $this->showingRepo->getWatchableAtCinema($movie->id, $cinema->id, $startingAfter);
        return $this->collection($movie->showings, new MovieShowingTransformer);
    }


    /**
     * @param $params
     * @return mixed
     */
    public function getCinemaId($params)
    {
        $routeParams = Route::current()->parameters();
        if(!isset($routeParams["cinemas"])) {
        }
        return $routeParams["cinemas"];
    }

    /**
     * @return static
     */
    public function getStartingAfter()
    {
        $now = Input::get('starting_after');
        if(!$now) {
            return Carbon::now();
        }
        return Carbon::createFromTimestamp($now);
    }
}