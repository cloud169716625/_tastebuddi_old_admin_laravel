<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifiedBusinessItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verified_business_items', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('item_id')->unsigned();
            $table->foreign('item_id')
                ->references('item_id')
                ->on('items')
                ->onDelete('cascade');
            $table->bigInteger('location_id')->unsigned();
            $table->foreign('location_id')
                    ->references('location_id')
                    ->on('locations')
                    ->onDelete('cascade');
            $table->bigInteger('item_detail_id')->unsigned();
            $table->decimal('price', 13, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verified_business_items');
    }
}
