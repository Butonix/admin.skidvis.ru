<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMetroStationIdToPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
			$table->unsignedInteger('metro_station_id')->nullable();
			$table->foreign('metro_station_id')->references('id')->on('metro_stations')->onUpdate('cascade')->onDelete('set null');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
			$table->dropForeign(['metro_station_id']);
			$table->dropColumn(['metro_station_id']);
        });
    }
}
