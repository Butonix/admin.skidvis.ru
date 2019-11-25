<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileParentIdToFiles extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('files', function (Blueprint $table) {
            $table->unsignedInteger('file_parent_id')->nullable()->after('id');
            $table->foreign('file_parent_id')->references('id')->on('files')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('files', function (Blueprint $table) {
            $table->dropForeign(['file_parent_id']);
            $table->dropColumn('file_parent_id');
        });
    }
}
