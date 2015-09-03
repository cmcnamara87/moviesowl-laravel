<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddDetailsToMoviesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('movies', function(Blueprint $table)
		{
            $table->text('synopsis');
            $table->string('run_time');
            $table->string('director');
            $table->string('cast');
            $table->string('trailer');
//            Synopsis, run time, director, casts, trailer, screen type, movie type, seats.
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('movies', function(Blueprint $table)
		{
			$table->dropColumn('synopsis');
            $table->dropColumn('run_time');
            $table->dropColumn('director');
            $table->dropColumn('cast');
            $table->dropColumn('trailer');
		});
	}

}
