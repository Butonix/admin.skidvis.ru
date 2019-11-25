<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetNullableFieldsInPhonesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('phones', function (Blueprint $table) {
            $table->string('phone', 22)->nullable()->change();
            $table->unsignedInteger('phoneable_id')->nullable()->change();
            $table->string('phoneable_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('phones', function (Blueprint $table) {
            $table->string('phone', 10)->nullable(false)->change();
            $table->unsignedInteger('phoneable_id')->nullable(false)->change();
            $table->string('phoneable_type')->nullable(false)->change();
        });
    }
}
