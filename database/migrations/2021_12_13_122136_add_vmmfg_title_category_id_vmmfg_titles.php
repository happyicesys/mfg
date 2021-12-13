<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVmmfgTitleCategoryIdVmmfgTitles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vmmfg_titles', function (Blueprint $table) {
            $table->bigInteger('vmmfg_title_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vmmfg_titles', function (Blueprint $table) {
            $table->dropColumn('vmmfg_title_category_id');
        });
    }
}
