<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCinemaSizeToShowingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('showings', function(Blueprint $table)
		{
			$table->string('cinema_size');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('showings', function(Blueprint $table)
		{
		    $table->dropColumn('cinema_size');
		});
	}

}
