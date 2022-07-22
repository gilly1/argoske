<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('number', 30);
            $table->string('name', 50);
            $table->string('commission_rate');
            $table->unsignedBigInteger('round_method_id');
            $table->foreign('round_method_id')->references('id')->on('round_methods');
            $table->float('precision');
            $table->integer('retirement_age');
            $table->integer('accounts');            
            $table->enum('is_approved', ['0', '1'])->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('employers');
    }
}
