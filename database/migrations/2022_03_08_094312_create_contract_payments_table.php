<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contract_items_id')->nullable();
            $table->foreign('contract_items_id')->references('id')->on('contract_items');
            $table->dateTime('month');
            $table->int('trans_no');
            $table->float('principle');
            $table->float('intrest');
            $table->float('loading');
            $table->float('installment');
            $table->float('balance');
            $table->float('paid')->nullable();
            $table->float('actual_balance')->nullable();
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
        Schema::dropIfExists('contract_payments');
    }
}
