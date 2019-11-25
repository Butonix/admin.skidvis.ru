<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAdvertisementToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
			$table->boolean('is_advertisement')->default(0);
			$table->unsignedInteger('main_category_id')->nullable();
			$table->foreign('main_category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('set null');

		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
			$table->dropForeign(['main_category_id']);
			$table->dropColumn(['is_advertisement', 'main_category_id']);
        });
    }
}
