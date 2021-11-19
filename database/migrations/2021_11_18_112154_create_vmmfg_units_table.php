<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmmfgUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vmmfg_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit_no');
            $table->bigInteger('vmmfg_job_id');
            $table->string('serial_no')->nullable();
            $table->bigInteger('vmmfg_scope_id')->nullable();
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
        Schema::dropIfExists('vmmfg_units');
    }
}
