<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 18/01/2015
 * Time: 12:17 AM
 */

namespace MoviesOwl\Repos\Cinema;

use MoviesOwl\Cinemas\Cinema;
use MoviesOwl\Repos\EloquentBaseRepository;

class CinemaRepository extends EloquentBaseRepository {

    /**
     * @var Cinema
     */
    private $model;

    function __construct(Cinema $model) {

        $this->model = $model;
    }
}