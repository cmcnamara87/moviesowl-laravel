<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezonesToCinemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cinemas', function(Blueprint $table)
        {
            $table->string('timezone');
            $table->string('city');
            $table->string('country');
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
            $table->dropColumn('timezone');
            $table->dropColumn('city');
            $table->dropColumn('country');
        });
    }
}
