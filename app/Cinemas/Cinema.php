<?php

namespace MoviesOwl\Cinemas;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Cinema extends \Eloquent implements SluggableInterface {

    use SluggableTrait;

    protected $sluggable = [
        'build_from' => 'location',
        'save_to'    => 'slug',
    ];

	// Add your validation rules here
	public static $rules = [
		// 'title' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ["location", "eventcinema_id", 'timezone', 'city', 'country'];

    public function showings()
    {
        return $this->hasMany('MoviesOwl\Showings\Showing');
    }
}