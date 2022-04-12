<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBomItemParentIdBomItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bom_items', function (Blueprint $table) {
            $table->bigInteger('bom_item_parent_id')->nullable();
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
            $table->dropColumn('bom_item_parent_id');
        });
    }
}
