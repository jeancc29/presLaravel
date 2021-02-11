<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaydetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paydetails', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->bigInteger("idPago");
            $table->bigInteger("idAmortizacion");
            $table->double("capital");
            $table->double("interes");
            $table->double("mora")->default(0);
            $table->double("descuento")->default(0);
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
        Schema::dropIfExists('paydetails');
    }
}
