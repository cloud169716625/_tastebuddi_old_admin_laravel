<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->bigIncrements('country_id');
            $table->string('country_numeric_code', 10)->nullable();
            $table->string('capital', 45)->nullable();            
            $table->char('country_alpha_code_2', 2)->nullable();
            $table->char('country_alpha_code_3', 3)->nullable();
            $table->char('currency_code', 3)->nullable();
            $table->string('currency_name', 45)->nullable();
            $table->char('emoji', 2)->nullable();
            $table->string('full_name', 150)->nullable();
            $table->string('country_name', 45)->nullable();
            $table->char('tl_domain', 3)->nullable();   
            $table->string('flag_url', 150)->nullable();  
            $table->string('background_url', 150)->nullable();         
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
        Schema::dropIfExists('countries');
    }
}
