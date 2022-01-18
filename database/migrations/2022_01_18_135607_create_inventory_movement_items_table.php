<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryMovementItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_movement_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bom_item_id');
            $table->bigInteger('inventory_movement_id');
            $table->bigInteger('supplier_quote_price_id');
            $table->text('remarks')->nullable();
            $table->integer('status')->default(1);
            $table->integer('amount')->default(0);
            $table->decimal('qty', 13, 2)->default(0);
            $table->bigInteger('bom_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('inventory_movement_items');
    }
}
