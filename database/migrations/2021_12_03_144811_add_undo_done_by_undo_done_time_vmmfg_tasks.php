<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUndoDoneByUndoDoneTimeVmmfgTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_tasks', function (Blueprint $table) {
            $table->bigInteger('undo_done_by')->nullable();
            $table->datetime('undo_done_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vmmfg_tasks', function (Blueprint $table) {
            $table->dropColumn('undo_done_by');
            $table->dropColumn('undo_done_time');
        });
    }
}
