<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserPreferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_preferences');

        Schema::create('user_preferences', function (Blueprint $table) {
            $table->bigIncrements('user_preference_id');
            $table->tinyInteger('is_convert_currency');
            $table->char('currency_code', 3);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::table('user_preferences', function($table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
