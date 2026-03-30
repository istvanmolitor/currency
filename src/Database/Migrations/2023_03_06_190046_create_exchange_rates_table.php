<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExchangeRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('currency_1_id');
            $table->foreign('currency_1_id')->references('id')->on('currencies');

            $table->unsignedBigInteger('currency_2_id');
            $table->foreign('currency_2_id')->references('id')->on('currencies');

            $table->float('value');

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exchange_rates');
    }
}
