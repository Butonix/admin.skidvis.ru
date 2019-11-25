<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWishlistCountToOrganizations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->smallInteger('wishlist_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('wishlist_count');
        });
    }
}
