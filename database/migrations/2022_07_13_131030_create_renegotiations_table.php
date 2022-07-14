<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRenegotiationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('renegotiations', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedBigInteger("idPrestamo");
            $table->decimal("monto", 20, 2);
            $table->double("porcentajeInteres", 10, 2);
            $table->double("porcentajeInteresAnual", 10, 2);
            $table->decimal("montoInteres", 20, 2)->default(0);
            $table->integer("numeroCuotas");
            $table->date("fecha");
            $table->date("fechaPrimerPago");
            $table->double("porcentajeMora", 5, 2);
            $table->integer("diasGracia")->default(0);
            $table->decimal("capitalTotal", 20, 2)->default(0);
            $table->decimal("interesTotal", 20, 2)->default(0);
            $table->decimal("capitalPendiente", 20, 2)->default(0);
            $table->decimal("interesPendiente", 20, 2)->default(0);
            $table->decimal("mora", 20, 2)->default(0);
            $table->decimal("cuota", 20, 2)->default(0);
            $table->integer("numeroCuotasPagadas")->default(0);
            $table->integer("cuotasAtrasadas")->default(0);
            $table->integer("diasAtrasados")->default(0);
            $table->date("fechaProximoPago")->nullable();
            $table->unsignedInteger("idTipoPlazo");
            $table->unsignedInteger("idTipoAmortizacion");
            $table->timestamps();

            $table->foreign("idPrestamo")->references("id")->on("loans");
            $table->foreign("idUsuario")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('renegotiations');
    }
}
