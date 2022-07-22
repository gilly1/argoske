<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('work_number');
            $table->string('designation', 30)->nullable();
            $table->string('department', 30)->nullable();
            $table->string('deo', 30)->nullable();
            $table->string('section', 30)->nullable();
            $table->string('station', 30)->nullable();
            $table->float('gross_salary')->nullable();
            $table->float('net_salary')->nullable();
            $table->string('pin_no', 30)->nullable();
            $table->string('town', 30)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('secondry_phone_number')->nullable();
            $table->string('email', 40)->nullable();
            $table->text('address')->nullable();
            $table->string('back_account_number')->nullable();
            $table->unsignedBigInteger('prospect_customer_id');
            $table->foreign('prospect_customer_id')->references('id')->on('prospect_customers');
            $table->string('last_name', 20)->nullable();
            $table->string('other_names')->nullable();
            $table->dateTime('dob')->nullable();
            $table->string('place_of_birth', 50)->nullable();
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->unsignedBigInteger('identification_type_id')->nullable();
            $table->foreign('identification_type_id')->references('id')->on('identification_types');
            $table->unsignedBigInteger('nationality_id')->nullable();
            $table->foreign('nationality_id')->references('id')->on('nationalities');
            $table->unsignedBigInteger('serial_number')->nullable();
            $table->integer('id_number')->nullable();
            $table->dateTime('date_of_issue')->nullable();
            $table->string('place_of_issue')->nullable();
            $table->string('district', 40)->nullable();
            $table->string('division', 40)->nullable();
            $table->string('location', 40)->nullable();
            $table->string('sub_location', 40)->nullable();    
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
        Schema::dropIfExists('account_customers');
    }
}
