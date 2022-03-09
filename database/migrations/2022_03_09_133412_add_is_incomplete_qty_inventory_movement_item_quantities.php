<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsIncompleteQtyInventoryMovementItemQuantities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory_movement_item_quantities', function (Blueprint $table) {
            $table->boolean('is_incomplete_qty')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_movement_item_quantities', function (Blueprint $table) {
            $table->dropColumn('is_incomplete_qty');
        });
    }
}
