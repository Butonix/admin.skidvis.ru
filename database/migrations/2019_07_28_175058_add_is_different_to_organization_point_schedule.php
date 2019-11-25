<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsDifferentToOrganizationPointSchedule extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organization_point_schedule', function (Blueprint $table) {
            $table->boolean('is_different')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organization_point_schedule', function (Blueprint $table) {
            $table->dropColumn('is_different');
        });
    }
}
