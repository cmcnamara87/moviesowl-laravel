<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSeatsUpdatedToShowingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('showings', function(Blueprint $table)
		{
		    $table->dateTime('seats_updated_at');
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
            $table->dropColumn('seats_updated_at');
		});
	}

}
