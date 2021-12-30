<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('remarks')->nullable();
            // $table->bigInteger('bom_group_id')->nullable();
            $table->bigInteger('bom_sub_category_id')->nullable();
            $table->bigInteger('bom_item_type_id')->nullable();
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
        Schema::dropIfExists('bom_items');
    }
}
