<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("photo")->nullable();
            $table->string("names");
            $table->string("surnames");
            $table->string("nickname")->nullable();
            $table->date("birthDate");
            $table->integer("numberDependents")->nullable();
            $table->string("gender");
            $table->string("maritalStatus");
            $table->string("recidenceType");
            $table->string("timeInResidence")->nullable();
            $table->string("referredBy")->nullable();
            $table->integer("status")->default(1);
            $table->unsignedInteger('idContact');
            $table->unsignedInteger('idAddress');
            $table->unsignedInteger('idDocument');
            $table->unsignedInteger('idJob');
            $table->unsignedInteger('idBusiness');
            
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
        Schema::dropIfExists('customers');
    }
}
