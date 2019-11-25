<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMetroLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metro_lines', function (Blueprint $table) {
            $table->increments('id');

			$table->unsignedSmallInteger('city_id')->nullable();
			$table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('set null');

			$table->string('hex_color', 10)->nullable();
			$table->string('name');
			$table->unsignedInteger('ordering')->nullable();

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
        Schema::dropIfExists('metro_lines');
    }
}
