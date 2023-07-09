<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReportsUpdateForeignReasonId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['reason_id']);
        });
        
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedInteger('reason_id')->change();
            $table->foreign('reason_id')->references('id')->on('report_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['reason_id']);
        });
        
        Schema::table('reports', function (Blueprint $table) {
            $table->unsignedBigInteger('reason_id')->change();
            $table->foreign('reason_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
