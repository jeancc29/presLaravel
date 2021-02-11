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
            $table->unsignedInteger("idEmpresa");
            $table->unsignedInteger("idTipoPago");
            $table->unsignedInteger("idCaja");
            $table->double("monto");
            $table->double("devuelta");
            $table->string("comentario");
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
        Schema::dropIfExists('pays');
    }
}
