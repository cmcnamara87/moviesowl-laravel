<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWidePosterToMoviedetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('moviedetails', function(Blueprint $table)
        {
            $table->text('wide_poster');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moviedetails', function(Blueprint $table)
        {
            $table->dropColumn('wide_poster');
        });
    }
}
