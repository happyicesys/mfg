<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVmmfgJobIdVmmfgUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->bigInteger('vmmfg_job_id')->nullable()->change();
            $table->bigInteger('vmmfg_scope_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {

        });
    }
}
