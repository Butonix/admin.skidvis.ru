<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToOrganizationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('link')->nullable()->after('cover_id');
            $table->string('avatar_color', 50)->nullable()->after('avatar_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['link', 'avatar_color']);
        });
    }
}
