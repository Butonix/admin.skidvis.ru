<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleLabelsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('article_labels', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name')->unique()->nullable();

            $table->unsignedInteger('image_id')->nullable();
            $table->foreign('image_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('article_labels');
        Schema::enableForeignKeyConstraints();
    }
}
