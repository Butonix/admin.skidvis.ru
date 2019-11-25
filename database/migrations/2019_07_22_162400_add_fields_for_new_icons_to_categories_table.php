<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsForNewIconsToCategoriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedInteger('active_image_id')->nullable();
            $table->foreign('active_image_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('business_image_id')->nullable();
            $table->foreign('business_image_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('business_active_image_id')->nullable();
            $table->foreign('business_active_image_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['active_image_id', 'business_image_id', 'business_active_image_id']);
            $table->dropColumn(['active_image_id', 'business_image_id', 'business_active_image_id']);
        });
    }
}
