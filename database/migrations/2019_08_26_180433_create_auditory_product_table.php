<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuditoryProductTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('auditory_product', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedSmallInteger('auditory_id')->nullable();
            $table->foreign('auditory_id')->references('id')->on('auditories')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('auditory_product');
        Schema::enableForeignKeyConstraints();
    }
}
