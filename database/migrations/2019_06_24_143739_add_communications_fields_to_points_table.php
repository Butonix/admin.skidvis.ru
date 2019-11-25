<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommunicationsFieldsToPointsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('points', function (Blueprint $table) {
            $table->unsignedInteger('phone_id')->nullable();
            $table->foreign('phone_id')->references('id')->on('phones')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('email_id')->nullable();
            $table->foreign('email_id')->references('id')->on('emails')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('points', function (Blueprint $table) {
            $table->dropForeign(['phone_id', 'email_id']);
            $table->dropColumn(['phone_id', 'email_id']);
        });
    }
}
