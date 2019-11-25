<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthProvidersConfigs extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('auth_providers_configs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedTinyInteger('auth_provider_id');
            $table->foreign('auth_provider_id')->references('id')->on('auth_providers')->onUpdate('cascade')->onDelete('cascade');

            $table->string('config_key');
            $table->string('config_value');
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
        Schema::dropIfExists('auth_providers_configs');
        Schema::enableForeignKeyConstraints();
    }
}
