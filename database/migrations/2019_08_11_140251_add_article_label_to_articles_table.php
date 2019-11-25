<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArticleLabelToArticlesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedSmallInteger('article_label_id')->nullable();
            $table->foreign('article_label_id')->references('id')->on('article_labels')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropForeign(['article_label_id']);
            $table->dropColumn('article_label_id');
        });
    }
}
