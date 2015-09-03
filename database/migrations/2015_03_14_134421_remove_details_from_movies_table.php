<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveDetailsFromMoviesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('movies', function(Blueprint $table)
		{
			$table->dropColumn('tomato_meter');
			$table->dropColumn('poster');
			$table->dropColumn('synopsis');
			$table->dropColumn('run_time');
			$table->dropColumn('director');
			$table->dropColumn('cast');
			$table->dropColumn('trailer');
			$table->dropColumn('genre');
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
			
			$table->integer('tomato_meter');
			$table->text('poster');
			$table->text('synopsis');
			$table->string('run_time');
			$table->string('director');
			$table->string('cast');
			$table->string('trailer');
			$table->string('genre');
		});
	}

}
