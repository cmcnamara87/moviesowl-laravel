<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMoviedetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('moviedetails', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->integer('tomato_meter');
			$table->text('poster');
			$table->text('synopsis');
			$table->string('run_time');
			$table->string('director');
			$table->string('cast');
			$table->string('trailer');
			$table->string('genre');
			$table->integer('movie_id')->unsigned();
			$table->foreign('movie_id')
     		 	->references('id')->on('movies')
      			->onDelete('cascade');
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
		Schema::drop('moviedetails');
	}

}
