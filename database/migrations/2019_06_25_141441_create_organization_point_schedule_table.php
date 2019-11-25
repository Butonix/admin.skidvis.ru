<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationPointScheduleTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('organization_point_schedule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->time('mon_start')->nullable();
            $table->time('mon_end')->nullable();
            $table->time('tue_start')->nullable();
            $table->time('tue_end')->nullable();
            $table->time('wed_start')->nullable();
            $table->time('wed_end')->nullable();
            $table->time('thu_start')->nullable();
            $table->time('thu_end')->nullable();
            $table->time('fri_start')->nullable();
            $table->time('fri_end')->nullable();
            $table->time('sat_start')->nullable();
            $table->time('sat_end')->nullable();
            $table->time('sun_start')->nullable();
            $table->time('sun_end')->nullable();
            $table->boolean('type')->default(0);
            $table->string('scheduleable_id')->nullable();
            $table->string('scheduleable_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('organization_point_schedule');
    }
}
