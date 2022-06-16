<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmortizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amortizations', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idTipo");
            $table->string("numeroCuota");
            $table->double("cuota", 20, 2);
            $table->double("interes", 20, 2);
            $table->double("capital", 20, 2);
            $table->double("mora", 20, 2)->default(0);
            $table->double("capitalRestante", 20, 2);
            $table->double("capitalSaldado", 20, 2)->default(0);
            $table->double("interesSaldado", 20, 2)->default(0);
            $table->double("capitalPendiente", 20, 2)->default(0);
            $table->double("interesPendiente", 20, 2)->default(0);
            $table->double("moraPendiente", 20, 2)->default(0);
            $table->boolean("pagada")->default(0);
            $table->date("fecha");
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
        Schema::dropIfExists('amortizations');
    }
}
