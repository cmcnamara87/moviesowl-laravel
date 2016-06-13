<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCinema21ToLowercase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cinemas = \MoviesOwl\Cinemas\Cinema::where('city', 'Jakarta')->get();
        $cinemas->each(function($cinema) {
            $cinema->location = ucwords(strtolower($cinema->location));
            $cinema->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
