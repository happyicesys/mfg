<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmmfgTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vmmfg_tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vmmfg_item_id');
            $table->bigInteger('vmmfg_unit_id');
            $table->boolean('is_done')->default(false);
            $table->boolean('is_checked')->default(false);
            $table->bigInteger('done_by')->nullable();
            $table->bigInteger('checked_by')->nullable();
            $table->datetime('done_time')->nullable();
            $table->datetime('checked_time')->nullable();
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('vmmfg_tasks');
    }
}
