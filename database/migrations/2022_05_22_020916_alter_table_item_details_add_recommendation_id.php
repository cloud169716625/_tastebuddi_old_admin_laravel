<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableItemDetailsAddRecommendationId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->unsignedBigInteger('recommendation_id')->after('location_id')->nullable();
        });
        
        Schema::table('item_details', function (Blueprint $table) {
            $table->foreign('recommendation_id')->references('recommendation_id')->on('recommendations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_details', function (Blueprint $table) {
            $table->dropForeign(['recommendation_id']);
        });

        Schema::table('item_details', function (Blueprint $table) {
            $table->dropColumn('recommendation_id');
        });
    }
}
