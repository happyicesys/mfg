<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_units', function (Blueprint $table) {
            $table->id();
            $table->string('batch')->nullable();
            $table->string('code')->nullable();
            $table->integer('container')->unsigned();
            $table->boolean('is_retired')->default(false);
            $table->string('name')->nullable();
            $table->text('remarks')->nullable();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
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
        Schema::dropIfExists('master_units');
    }
}
