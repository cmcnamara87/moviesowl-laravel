<?php
/**
 * Created by PhpStorm.
 * User: cmcnamara87
 * Date: 17/01/2015
 * Time: 11:07 PM
 */

namespace MoviesOwl\Repos;

class EloquentBaseRepository {

    protected $eloquent;

    function __construct($eloquent)
    {
        $this->eloquent = $eloquent;
    }

    function getAll() {
        return $this->eloquent->all();
    }

    function getById($id) {
        return $this->eloquent->findById($id);
    }

    function store($model) {
        $model->save();
    }
}