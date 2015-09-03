<?php
namespace MoviesOwl\Http\Controllers\Api\v1;
use Carbon\Carbon;
use Cyvelnet\Laravel5Fractal\Facades\Fractal;
use Illuminate\Routing\Controller;
use MoviesOwl\Service\SeatingService;
use MoviesOwl\Showings\Showing;
use MoviesOwl\Showings\ShowingTransformer;
use Sorskod\Larasponse\Larasponse;
use MoviesOwl\EventCinemas\EventCinemasApi;

class ShowingsController extends Controller {

    protected $seatingService;

    public function __construct(SeatingService $seatingService)
    {
        $this->seatingService = $seatingService;
    }

    /**
     * Display the specified showing.
     *
     * @param Showing $showing
     * @return Response
     * @internal param int $id
     */
	public function show(Showing $showing)
	{
        $this->seatingService->updateSeating($showing);
        return Fractal::item($showing, new ShowingTransformer)->responseJson(200);
	}


}
