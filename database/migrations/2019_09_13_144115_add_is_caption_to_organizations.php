<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCaptionToOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organizations', function (Blueprint $table) {
			$table->boolean('is_caption')->default(0);
			$table->boolean('is_all_similar_disabled')->default(0);
			$table->boolean('is_advertisement')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
			$table->dropColumn(['is_caption', 'is_all_similar_disabled', 'is_advertisement']);
        });
    }
}
