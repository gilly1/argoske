<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProspectCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospect_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('work_number', 30);
            $table->string('full_name');
            $table->string('phone_number', 13);
            $table->string('secondary_phone_number', 13)->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('employer_id');
            $table->foreign('employer_id')->references('id')->on('employers');
            $table->float('ability')->nullable();
            $table->string('town')->nullable();
            $table->text('notes')->nullable();            
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
        Schema::dropIfExists('prospect_customers');
    }
}
