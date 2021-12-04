<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmmfgItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vmmfg_items', function (Blueprint $table) {
            $table->id();
            $table->string('sequence');
            $table->string('name');
            $table->text('remarks')->nullable();
            $table->bigInteger('vmmfg_title_id');
            $table->boolean('is_required_upload')->default(false);
            $table->boolean('is_required')->default(false);
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
        Schema::dropIfExists('vmmfg_items');
    }
}
