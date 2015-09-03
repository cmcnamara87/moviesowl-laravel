<?php

namespace MoviesOwl\Cinemas;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Cinema extends \Eloquent {

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["location", "eventcinema_id"];

    public function showings()
    {
        return $this->hasMany('MoviesOwl\Showings\Showing');
    }
}