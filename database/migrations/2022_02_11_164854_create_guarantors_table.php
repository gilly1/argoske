<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuarantorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('designation', 30);
            $table->string('department', 30);
            $table->string('station', 30);
            $table->string('section', 30);
            $table->string('last_name', 30);
            $table->string('other_names', 100);
            $table->dateTime('d0b');
            $table->unsignedBigInteger('gender_id');
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->string('district_of_birth', 30);
            $table->unsignedBigInteger('identification_type_id');
            $table->foreign('identification_type_id')->references('id')->on('identification_types');
            $table->unsignedBigInteger('nationality_id');
            $table->foreign('nationality_id')->references('id')->on('nationalities');
            $table->integer('id_number');
            $table->integer('serial_number');
            $table->dateTime('date_of_issue');
            $table->string('place_of_issue', 30);
            $table->string('district', 30);
            $table->string('division', 30);
            $table->string('location', 30);
            $table->string('sub_location', 30);
            $table->string('phone_number');
            $table->string('email')->nullable();            
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
        Schema::dropIfExists('guarantors');
    }
}
