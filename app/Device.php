<?php

namespace MoviesOwl;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['token', 'device_type'];
}
