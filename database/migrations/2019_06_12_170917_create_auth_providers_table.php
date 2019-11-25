<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthProvidersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('auth_providers', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('icon_url')->nullable();
            $table->unsignedTinyInteger('ordering')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('auth_providers');
    }
}
