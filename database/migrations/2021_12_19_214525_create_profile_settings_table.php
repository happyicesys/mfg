<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('profile_id');
            $table->string('theme_background_url')->nullable();
            $table->string('theme_sidebar_background_color')->nullable();
            $table->string('theme_sidebar_font_color')->nullable();
            $table->string('vmmfg_job_batch_no_title')->nullable();
            $table->string('vmmfg_unit_vend_id_title')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_settings');
    }
}
