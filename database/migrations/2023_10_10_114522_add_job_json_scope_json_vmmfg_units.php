<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJobJsonScopeJsonVmmfgUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->string('destination')->nullable();
            $table->string('origin')->nullable();
            $table->json('vmmfg_job_json')->nullable()->after('vmmfg_job_id');
            $table->json('vmmfg_scope_json')->nullable()->after('vmmfg_scope_id');
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
            $table->dropColumn('destination');
            $table->dropColumn('origin');
            $table->dropColumn('vmmfg_job_json');
            $table->dropColumn('vmmfg_scope_json');
        });
    }
}
