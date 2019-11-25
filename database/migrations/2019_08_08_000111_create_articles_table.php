<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('text')->nullable();
            $table->text('short_description')->nullable();
            $table->string('author')->nullable();

            $table->unsignedBigInteger('creator_id')->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('organization_id')->nullable();
            $table->foreign('organization_id')->references('id')->on('organizations')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedInteger('cover_id')->nullable();
            $table->foreign('cover_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->smallInteger('read_time')->nullable();
            $table->smallInteger('views')->default(0);
            $table->boolean('is_actual')->default(false);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('articles');
        Schema::enableForeignKeyConstraints();
    }
}
