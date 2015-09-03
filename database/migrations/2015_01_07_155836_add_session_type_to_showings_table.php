<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSessionTypeToShowingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('showings', function(Blueprint $table)
		{
            $table->string('showing_type');
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
			$table->dropColumn('showing_type');
		});
	}

}
