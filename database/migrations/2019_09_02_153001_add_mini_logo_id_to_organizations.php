<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMiniLogoIdToOrganizations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->unsignedInteger('mini_logo_id')->nullable();
            $table->foreign('mini_logo_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign('mini_logo_id');
            $table->dropColumn('mini_logo_id');
        });
    }
}
