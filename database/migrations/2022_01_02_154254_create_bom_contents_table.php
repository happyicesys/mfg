<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sequence');
            $table->bigInteger('qty')->default(0);
            $table->bigInteger('bom_item_id')->nullable();
            $table->bigInteger('bom_header_id');
            $table->bigInteger('bom_sub_category_id')->nullable();
            $table->boolean('is_group')->default(false);
            $table->bigInteger('vmmfg_item_id')->nullable();
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
        Schema::dropIfExists('bom_contents');
    }
}
