<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedBigInteger("idCliente");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idEmpresa");
            $table->unsignedInteger("idTipoPago");
            $table->unsignedInteger("idCaja")->nullable();
            $table->double("monto");
            $table->double("devuelta")->default(0);
            $table->double("descuento")->default(0);
            $table->string("comentario")->nullable();
            $table->string("concepto")->nullable();
            $table->date("fecha");
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign('idCliente')->references('id')->on('customers');
            $table->foreign('idPrestamo')->references('id')->on('loans');
            $table->foreign('idEmpresa')->references('id')->on('companies');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pays');
    }
}
