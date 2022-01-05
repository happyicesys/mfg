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
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('remarks')->nullable();
            $table->bigInteger('bom_item_type_id')->nullable();
            $table->boolean('is_inventory')->default(true);
            $table->boolean('is_header')->default(false);
            $table->boolean('is_sub_header')->default(false);
            $table->boolean('is_part')->default(false);
            $table->bigInteger('available_qty')->default(0);
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
