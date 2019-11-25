<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeedbacksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->text('text')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();

            $table->unsignedSmallInteger('feedback_type_id')->nullable();
            $table->foreign('feedback_type_id')->references('id')->on('feedback_types')->onUpdate('cascade')->onDelete('set null');

            $table->string('user_ip', 40)->nullable();
            $table->string('user_agent')->nullable();
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
        Schema::dropIfExists('feedbacks');
        Schema::enableForeignKeyConstraints();
    }
}
