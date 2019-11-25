<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimezoneTextToCitiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cities', function (Blueprint $table) {
            $table->renameColumn('timezone', 'UTC')->change();
            $table->string('timezone', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('timezone');
            $table->renameColumn('UTC', 'timezone')->change();
        });
    }
}
