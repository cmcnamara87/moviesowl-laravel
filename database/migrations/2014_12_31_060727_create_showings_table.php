<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateShowingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('showings', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('movie_id')->references('id')->on('movies');
            $table->integer('cinema_id')->references('id')->on('cinema');
            $table->string('type');
            $table->dateTime('start_time');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('showings');
	}

}
