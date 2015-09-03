<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSeatsCountToShowingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('showings', function(Blueprint $table)
		{
			$table->integer('seats_count');
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
			$table->dropColumn('seats_count');
		});
	}

}
