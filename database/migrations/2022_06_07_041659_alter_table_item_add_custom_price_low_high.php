<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableItemAddCustomPriceLowHigh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('custom_price_low', 13, 2)->nullable()->after('category_id');
            $table->decimal('custom_price_high', 13, 2)->nullable()->after('custom_price_low');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'custom_price_low',
                'custom_price_high',
            ]);
        });
    }
}
