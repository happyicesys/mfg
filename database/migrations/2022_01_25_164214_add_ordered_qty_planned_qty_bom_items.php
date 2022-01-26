<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderedQtyPlannedQtyBomItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->decimal('ordered_qty', 13, 2)->default(0);
            $table->decimal('planned_qty', 13, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->dropColumn('ordered_qty');
            $table->dropColumn('planned_qty');
        });
    }
}
