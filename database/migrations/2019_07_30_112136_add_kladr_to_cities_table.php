<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKladrToCitiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('kladr', 100)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropPrimary('kladr');
            $table->dropColumn('kladr');
        });
    }
}
