<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayloadToPointsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('points', function (Blueprint $table) {
            $table->string('city_kladr_id', 100)->nullable();
            $table->foreign('city_kladr_id')->references('kladr')->on('cities')->onUpdate('cascade')->onDelete('set null');

            $table->text('payload')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['city_kladr_id']);
            $table->dropColumn(['city_kladr_id', 'payload']);
        });
    }
}
