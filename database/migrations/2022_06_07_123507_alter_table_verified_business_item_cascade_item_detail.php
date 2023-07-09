<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableVerifiedBusinessItemCascadeItemDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verified_business_items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_detail_id')->change();

            $table->foreign('item_detail_id')
                ->references('item_detail_id')
                ->on('item_details')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verified_business_items', function (Blueprint $table) {
            $table->dropForeign(['item_detail_id']);

            $table->bigInteger('item_detail_id')->unsigned()->change();
        });
    }
}
