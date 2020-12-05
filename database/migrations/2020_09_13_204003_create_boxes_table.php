<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("description");
            $table->double("initialBalance")->default(0);
            $table->boolean("validateCashBreakdown")->default(0);
            $table->boolean("validateCheckBreakdown")->default(0);
            $table->boolean("validateCreditCardBreakdown")->default(0);
            $table->boolean("validateTransferBreakdown")->default(0);
            
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
        Schema::dropIfExists('boxes');
    }
}
