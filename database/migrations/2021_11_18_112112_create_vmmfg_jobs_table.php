<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVmmfgJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vmmfg_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->string('model');
            $table->string('racking_name')->nullable();
            $table->datetime('order_date')->nullable();
            $table->datetime('due_date')->nullable();
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
        Schema::dropIfExists('vmmfg_jobs');
    }
}
