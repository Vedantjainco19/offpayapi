<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('token_details', function (Blueprint $table) {
            $table->increments('tokenId');
            $table->string('tokenname')->nullable();
            $table->integer('amount');
            $table->string('status');
            $table->datetime('expiryTime');
            $table->string('userMobileNo');
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
        Schema::dropIfExists('token_details');
    }
};
