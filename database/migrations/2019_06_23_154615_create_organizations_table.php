<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('organizations');
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            $table->unsignedInteger('avatar_id')->nullable();
            $table->foreign('avatar_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('cover_id')->nullable();
            $table->foreign('cover_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');

            $table->decimal('rating', 5,2)->default(0);
            $table->unsignedInteger('phone_id')->nullable();
            $table->foreign('phone_id')->references('id')->on('phones')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedInteger('email_id')->nullable();
            $table->foreign('email_id')->references('id')->on('emails')->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('organizations');
    }
}
