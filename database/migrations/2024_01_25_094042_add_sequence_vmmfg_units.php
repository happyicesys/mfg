<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSequenceVmmfgUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->integer('sequence')->unsigned()->default(1)->index();
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
            $table->dropColumn('sequence');
        });
    }
}
