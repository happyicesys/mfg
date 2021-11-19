<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('address');
            $table->string('postcode');
            $table->string('city')->nullable();
            $table->bigInteger('state_id')->nullable();
            $table->boolean('is_city')->default(true);
            $table->boolean('is_state')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->bigInteger('country_id');
            $table->morphs('modelable');
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
        Schema::dropIfExists('addresses');
    }
}
