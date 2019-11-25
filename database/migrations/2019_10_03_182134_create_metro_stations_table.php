<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetroStationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metro_stations', function (Blueprint $table) {
            $table->increments('id');

			$table->unsignedSmallInteger('city_id')->nullable();
			$table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('set null');

			$table->unsignedInteger('metro_line_id')->nullable();
			$table->foreign('metro_line_id')->references('id')->on('metro_lines')->onUpdate('cascade')->onDelete('set null');

			$table->string('name');
			$table->unsignedInteger('ordering')->nullable();

			$table->float('latitude', 9, 6)->nullable();
			$table->float('longitude', 9, 6)->nullable();

			$table->string('api_id')->nullable();

			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metro_stations');
    }
}
