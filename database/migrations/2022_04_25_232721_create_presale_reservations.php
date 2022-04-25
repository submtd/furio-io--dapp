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
        Schema::create('presale_reservations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('address')->unique();
            $table->string('salt')->index();
            $table->string('quantity');
            $table->timestamps();
            $table->unique(['address', 'salt']);
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presale_reservations');
    }
};
