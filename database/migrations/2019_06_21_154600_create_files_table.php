<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->string('fileable_id')->nullable();
            $table->string('fileable_type')->nullable();
            $table->string('public_path')->nullable();
            $table->string('local_path')->nullable();
            $table->string('name')->nullable();
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->text('payload')->nullable();
            $table->timestamp('file_delete_at')->nullable();
            $table->timestamp('file_deleted_at')->nullable();
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
        Schema::dropIfExists('files');
    }
}
