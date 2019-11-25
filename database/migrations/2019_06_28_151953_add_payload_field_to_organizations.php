<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayloadFieldToOrganizations extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('payload', 1000)->nullable()->after('cover_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('payload');
        });
    }
}
