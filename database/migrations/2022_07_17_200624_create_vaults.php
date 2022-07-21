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
            $table->timestamp('start_time')->nullable();
            $table->string('balance')->index();
            $table->string('deposited')->index();
            $table->string('compounded')->index();
            $table->string('claimed')->index();
            $table->string('taxed');
            $table->string('awarded');
            $table->boolean('negative')->index();
            $table->boolean('penalized')->index();
            $table->boolean('maxed')->index();
            $table->boolean('banned')->index();
            $table->boolean('team_wallet');
            $table->boolean('complete')->index();
            $table->string('maxed_rate');
            $table->string('direct_referrals')->index();
            $table->string('airdrop_sent');
            $table->string('airdrop_received');
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
