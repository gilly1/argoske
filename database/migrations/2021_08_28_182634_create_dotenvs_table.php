<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDotenvsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dotenvs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('company_name', 100);
            $table->string('subdomain', 50);
            $table->string('host', 100);
            $table->string('db_user', 120);
            $table->string('db_pass', 150)->nullable();
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
        Schema::dropIfExists('dotenvs');
    }
}
