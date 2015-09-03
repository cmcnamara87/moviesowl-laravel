<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSluggableColumns extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('movies', function(Blueprint $table)
		{
			$table->string('slug')->nullable();
		});

        Schema::table('cinemas', function(Blueprint $table)
        {
            $table->string('slug')->nullable();
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
			$table->dropColumn('slug');
		});

        Schema::table('cinemas', function(Blueprint $table)
        {
            $table->dropColumn('slug');
        });
	}

}