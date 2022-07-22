<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApproverStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approver_statuses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('modelMapping_id');
            $table->foreign('modelMapping_id')->references('id')->on('model_mappings');
            $table->integer('approver_model_id');
            $table->integer('weight');
            $table->integer('status');
            $table->integer('approved');
            $table->integer('super_admin');
            $table->text('reason')->nullable();
            $table->enum('is_approved', ['0', '1'])->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users2');
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
        Schema::dropIfExists('approver_statuses');
    }
}
