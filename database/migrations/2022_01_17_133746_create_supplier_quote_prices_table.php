<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierQuotePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supplier_quote_prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bom_item_id');
            $table->bigInteger('country_id');
            $table->bigInteger('currency_rate_id');
            $table->bigInteger('supplier_id');
            $table->text('remarks')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('base_price')->nullable();
            $table->integer('avg_price')->nullable();
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
        Schema::dropIfExists('supplier_quote_prices');
    }
}
