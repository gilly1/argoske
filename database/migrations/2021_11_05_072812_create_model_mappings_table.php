<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModelMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('approver_model');
            $table->string('approved_model');
            $table->string('approval_rate')->nullable();
            $table->string('rejection_rate')->nullable();
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
        Schema::dropIfExists('model_mappings');
    }
}
