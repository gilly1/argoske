<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSideBarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('side_bars', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('parent_id');
            $table->string('title', 100);
            $table->string('url', 100)->nullable();
            $table->integer('menu_order');
            $table->string('icon', 100)->nullable();
            $table->string('description')->nullable();
            $table->string('custom_title', 100)->nullable();
            $table->string('permission', 100)->nullable();
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
        Schema::dropIfExists('side_bars');
    }
}
