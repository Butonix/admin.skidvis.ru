<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddActiveFieldsToOrganizationPointSchedule extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('organization_point_schedule', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->boolean('mon_active')->default(false)->after('mon_end');
            $table->boolean('tue_active')->default(false)->after('tue_end');
            $table->boolean('wed_active')->default(false)->after('wed_end');
            $table->boolean('thu_active')->default(false)->after('thu_end');
            $table->boolean('fri_active')->default(false)->after('fri_end');
            $table->boolean('sat_active')->default(false)->after('sat_end');
            $table->boolean('sun_active')->default(false)->after('sun_end');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('organization_point_schedule', function (Blueprint $table) {
            $table->boolean('type')->default(0);
            $table->dropColumn(['mon_active', 'tue_active', 'wed_active', 'thu_active', 'fri_active', 'sat_active', 'sun_active']);
        });
    }
}
