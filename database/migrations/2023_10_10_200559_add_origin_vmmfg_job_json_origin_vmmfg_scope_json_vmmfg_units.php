<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginVmmfgJobJsonOriginVmmfgScopeJsonVmmfgUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->json('origin_vmmfg_job_json')->nullable()->after('origin');
            $table->json('origin_vmmfg_scope_json')->nullable()->after('origin');
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
            $table->dropColumn('origin_vmmfg_job_json');
            $table->dropColumn('origin_vmmfg_scope_json');
        });
    }
}
