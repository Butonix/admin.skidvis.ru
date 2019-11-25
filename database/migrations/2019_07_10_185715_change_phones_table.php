<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePhonesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('phones', function (Blueprint $table) {
            $table->string('code', 3)->nullable();
            $table->string('full_phone', 25)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('phones', function (Blueprint $table) {
            $table->dropColumn('code', 'full_phone');
        });
    }
}
