<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vaults', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->bigInteger('address_id')->index();
            $table->timestamp('start_time');
            $table->bigInteger('balance')->index();
            $table->bigInteger('deposited')->index();
            $table->bigInteger('compounded')->index();
            $table->bigInteger('claimed')->index();
            $table->bigInteger('taxed');
            $table->bigInteger('awarded');
            $table->boolean('negative')->index();
            $table->boolean('penalized')->index();
            $table->boolean('maxed')->index();
            $table->boolean('banned')->index();
            $table->boolean('team_wallet');
            $table->boolean('complete')->index();
            $table->bigInteger('maxed_rate');
            $table->bigInteger('direct_referrals')->index();
            $table->bigInteger('airdrop_sent');
            $table->bigInteger('airdrop_received');
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
        Schema::dropIfExists('vaults');
    }
};
