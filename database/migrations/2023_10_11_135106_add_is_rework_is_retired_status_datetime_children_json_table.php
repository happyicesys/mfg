<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReworkIsRetiredStatusDatetimeChildrenJsonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->boolean('is_rework')->default(false);
            $table->boolean('is_retired')->default(false);
            $table->datetime('status_datetime')->nullable();
            $table->json('children_json')->nullable();
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
            $table->dropColumn('is_rework');
            $table->dropColumn('is_retired');
            $table->dropColumn('status_datetime');
            $table->dropColumn('children_json');
        });
    }
}
