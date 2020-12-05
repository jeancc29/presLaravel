<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->decimal("monto", 20, 2);
            $table->double("porcentajeInteres", 5, 2);
            $table->double("porcentajeInteresAnual", 5, 2);
            $table->integer("numeroCuotas");
            $table->date("fecha");
            $table->date("fechaPrimerPago");
            $table->string("codigoUnico");
            $table->double("porcentajeMora", 5, 2);
            $table->integer("diasGracia");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedBigInteger("idCliente");
            $table->unsignedInteger("idTipoPlazo");
            $table->unsignedInteger("idTipoAmortizacion");
            $table->unsignedBigInteger("idCaja");
            $table->unsignedInteger("idCobrador");
            $table->unsignedInteger("idGasto");
            $table->unsignedInteger("idDesembolso");
            //la llave foranea del idUsuario se agregara despues de crear la tabla usuario
            $table->foreign("idCliente")->references("id")->on("customers");
            $table->foreign("idTipoPlazo")->references("id")->on("types");
            $table->foreign("idTipoAmortizacion")->references("id")->on("types");
            $table->foreign("idCaja")->references("id")->on("boxes");
            // $table->foreign("idCobrador")->references("id")->on("boxes");
            //la llave foranea del idGasto se agregara despues de crear la tabla Loanexpenses
            $table->foreign("idDesembolso")->references("id")->on("disbursements");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropSoftDeletes();
        $table->dropForeign(['idDesembolso', 'idTipoAmortizacion', 'idTipoPlazo', 'idCliente', 'idCaja']);
        Schema::dropIfExists('loans');
    }
}
