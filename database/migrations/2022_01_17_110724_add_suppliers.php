<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuppliers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('company_name');
            $table->string('attn_name')->nullable();
            $table->string('attn_contact')->nullable();
            $table->string('email')->nullable();
            $table->string('url')->nullable();
            $table->bigInteger('payment_term_id')->nullable();
            $table->bigInteger('country_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->dropColumn('attn_name');
            $table->dropColumn('attn_contact');
            $table->dropColumn('email');
            $table->dropColumn('url');
            $table->dropColumn('payment_term_id');
            $table->dropColumn('country_id');
        });
    }
}
