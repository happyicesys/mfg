<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_items', function (Blueprint $table) {
            $table->index('vmmfg_title_id')->change();
            $table->index('sequence')->change();
        });

        Schema::table('vmmfg_jobs', function (Blueprint $table) {
            $table->index('batch_no')->change();
        });

        Schema::table('vmmfg_tasks', function (Blueprint $table) {
            $table->index('vmmfg_item_id')->change();
            $table->index('vmmfg_unit_id')->change();
        });

        Schema::table('vmmfg_titles', function (Blueprint $table) {
            $table->index('sequence')->change();
            $table->index('vmmfg_scope_id')->change();
            $table->index('vmmfg_title_category_id')->change();
        });

        Schema::table('vmmfg_units', function (Blueprint $table) {
            $table->index('unit_no')->change();
            $table->index('vmmfg_job_id')->change();
            $table->index('vmmfg_scope_id')->change();
            $table->index('vend_id')->change();
            $table->index('order_date')->change();
            $table->index('refer_completion_unit_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
