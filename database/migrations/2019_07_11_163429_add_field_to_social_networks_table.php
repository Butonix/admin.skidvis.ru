<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToSocialNetworksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('social_networks', function (Blueprint $table) {
            $table->string('name')->unique()->change();
            $table->string('display_name')->after('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('social_networks', function (Blueprint $table) {
            $table->dropUnique('name');
            $table->dropColumn('display_name');
        });
    }
}
