<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_headers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sequence');
            $table->bigInteger('qty')->default(0);
            $table->bigInteger('bom_item_id')->nullable();
            $table->bigInteger('bom_id');
            $table->bigInteger('bom_category_id');
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
        Schema::dropIfExists('bom_headers');
    }
}
