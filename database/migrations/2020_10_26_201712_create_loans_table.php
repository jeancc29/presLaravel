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
            $table->double("porcentajeInteres", 10, 2);
            $table->double("porcentajeInteresAnual", 10, 2);
            $table->decimal("montoInteres", 20, 2)->default(0);
            $table->integer("numeroCuotas");
            $table->date("fecha");
            $table->date("fechaPrimerPago");
            $table->string("codigo")->nullable();
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
            $table->integer("status")->default(1);
            $table->date("fechaProximoPago")->nullable();
            $table->unsignedInteger("idEmpresa");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedBigInteger("idCliente");
            $table->unsignedInteger("idTipoPlazo");
            $table->unsignedInteger("idTipoAmortizacion");
            $table->unsignedBigInteger("idCaja")->nullable();
            $table->unsignedInteger("idCobrador")->nullable();
            $table->unsignedInteger("idRuta")->nullable();
            // $table->unsignedInteger("idGasto");
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
