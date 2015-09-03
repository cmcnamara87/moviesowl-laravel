<?php

namespace MoviesOwl\Movies;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Carbon\Carbon;
use MoviesOwl\Showings\Showing;
use Illuminate\Support\Facades\Log;

class Movie extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["title"];

    public function scopeWatchableAtCinema($query, $cinemaId, Carbon $startingAfter = null) {
        if(!$startingAfter) {
            $startingAfter = Carbon::now();
        }
        return $query->whereHas('showings', function ($q) use ($cinemaId, $startingAfter)
        {
            $endOfDay = $startingAfter->copy()->endOfDay();
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
            $q->where('cinema_id', $cinemaId);
        });
    }

    public function scopeWatchable($query, Carbon $startingAfter = null) {
        if(!$startingAfter) {
            $startingAfter = Carbon::now();
        }
        return $query->whereHas('showings', function ($q) use ($startingAfter)
        {
            $endOfDay = $startingAfter->copy()->endOfDay();
            $q->where('start_time', '>=', $startingAfter->toDateTimeString());
            $q->where('start_time', '<=', $endOfDay->toDateTimeString());
        });
    }

    public function showings()
    {
        return $this->hasMany('MoviesOwl\Showings\Showing');
    }
    public function cinemas()
    {
        return $this->hasManyThrough('MoviesOwl\Cinemas\Cinema', 'MoviesOwl\Showings\Showing');
    }
    public function details()
    {
        return $this->hasOne('MoviesOwl\Movies\MovieDetails');
    }

    public function score() {
        if($this->tomato_meter > 75) {
            return 'good';
        }
        if($this->tomato_meter > 50) {
            return 'average';
        }
        return 'bad';
    }
}