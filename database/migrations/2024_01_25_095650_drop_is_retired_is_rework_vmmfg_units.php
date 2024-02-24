<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropIsRetiredIsReworkVmmfgUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->dropColumn('is_retired');
            $table->dropColumn('is_rework');
            $table->dropColumn('parent_id');
            $table->dropColumn('children_json');
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
            //
        });
    }
}
