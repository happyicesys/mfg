<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterQtyBomHeadersBomContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bom_headers', function (Blueprint $table) {
            $table->decimal('qty', 13, 2)->change();
        });

        Schema::table('bom_contents', function (Blueprint $table) {
            $table->decimal('qty', 13, 2)->change();
        });

        Schema::table('bom_items', function (Blueprint $table) {
            $table->decimal('available_qty', 13, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
