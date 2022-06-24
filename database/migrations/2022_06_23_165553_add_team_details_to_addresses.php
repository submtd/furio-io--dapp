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
        Schema::table('addresses', function (Blueprint $table) {
            $table->integer('referrer_id')->after('uuid')->default(0)->index();
            $table->integer('airdrops_sent')->after('name')->default(0)->index();
            $table->integer('direct_referrals')->after('name')->default(0)->index();
            $table->string('instagram')->after('airdrops_sent')->nullable();
            $table->string('facebook')->after('airdrops_sent')->nullable();
            $table->string('medium')->after('airdrops_sent')->nullable();
            $table->string('discord')->after('airdrops_sent')->nullable();
            $table->string('telegram')->after('airdrops_sent')->nullable();
            $table->string('twitter')->after('airdrops_sent')->nullable();
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('referrer_id');
            $table->dropColumn('airdrops_sent');
            $table->dropColumn('direct_referrals');
            $table->dropColumn('instagram');
            $table->dropColumn('facebook');
            $table->dropColumn('medium');
            $table->dropColumn('discord');
            $table->dropColumn('telegram');
            $table->dropColumn('twitter');
        });
    }
};
