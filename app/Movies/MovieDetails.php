<?php

namespace MoviesOwl\Movies;

class MovieDetails extends \Eloquent {

     protected $table = 'moviedetails';
	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

// Don't forget to fill this array
    protected $fillable = ["title", "synopsis", "run_time", "director", "cast", "poster", "tomato_meter", "genre", "movie_id"];

    public function movie()
    {
        return $this->hasOne('MoviesOwl\Movies\Movie');
    }

}


