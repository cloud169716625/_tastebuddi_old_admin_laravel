<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExclusiveOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exclusive_offers', function (Blueprint $table) {
            $table->bigIncrements('exclusive_offer_id');
            $table->string('label', 150);   
            $table->string('item_name', 150);   
            $table->string('promo', 150);   
            $table->string('image_url')->nullable(true);
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
        Schema::dropIfExists('exclusive_offers');
    }
}
