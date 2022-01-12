<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAssemblyLocationOrderByBomItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->bigInteger('order_by')->nullable();
            $table->bigInteger('supplier_id')->nullable();
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
            $table->dropColumn('order_by');
            $table->dropColumn('supplier_id');
        });
    }
}
