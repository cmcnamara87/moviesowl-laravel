<?php

namespace MoviesOwl\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use MoviesOwl\Repos\Showing\ShowingRepository;
use MoviesOwl\Service\SeatingService;
use MoviesOwl\Showings\Showing;
use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Movies\Movie;
class CinemaMovieShowingsController extends Controller {

    protected $showingRepo;
    /**
     * @var SeatingService
     */
    private $seatingService;

    /**
     * @param ShowingRepository $showingRepo
     * @param SeatingService $seatingService
     */
    function __construct(ShowingRepository $showingRepo, SeatingService $seatingService)
    {
        $this->showingRepo = $showingRepo;
        $this->seatingService = $seatingService;
    }


    /**
     * Display a listing of the resource.
     * GET /cinemamovieshowings
     *
     * @param Cinema $cinema
     * @param Movie $movie
     * @return Response
     */
	public function index(Cinema $cinema, Movie $movie, $day = 'today')
	{
        $startingAfter = Carbon::$day($cinema->timezone);
//        if ($startingAfter) {
//            $startingAfter = Carbon::createFromTimestamp($startingAfter);
//        } else {
//             by default show movies that have just started up to 20 minutes ago.
//            $startingAfter = Carbon::now()->subMinutes(20);
//        }

        $showings = $this->showingRepo->getWatchableAtCinema($movie->id, $cinema->id, $startingAfter);

        $showingsByTime = array_reduce($showings->all(), function($carry, $showing) {
            if($showing->start_time->hour < 12) {
                $carry['morning'][] = $showing;
            } else if($showing->start_time->hour < 17) {
                $carry['afternoon'][] = $showing;
            } else {
                $carry['evening'][] = $showing;
            }
            return $carry;
        }, [
            'morning' => [],
            'afternoon' => [],
            'evening' => []
        ]);
//        foreach($showings as $showing) {
//            // FIXME: might spam event...need to investigate this
//            $this->seatingService->updateSeating($showing);
//        }
        return view('showings.index', compact('cinema', 'movie', 'showingsByTime', 'day'));
	}

	/**
	 * Show the form for creating a new resource.
	 * GET /cinemamovieshowings/create
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /cinemamovieshowings
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 * GET /cinemamovieshowings/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /cinemamovieshowings/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /cinemamovieshowings/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /cinemamovieshowings/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}