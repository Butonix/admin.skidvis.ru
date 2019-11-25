<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('link');

            $table->unsignedTinyInteger('social_network_id')->nullable();
            $table->foreign('social_network_id')->references('id')->on('social_networks')->onUpdate('cascade')->onDelete('cascade');

            $table->string('social_user_id')->nullable();
            $table->unsignedInteger('social_account_id');
            $table->string('social_account_type');
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
        Schema::dropIfExists('social_accounts');
        Schema::enableForeignKeyConstraints();
    }
}
