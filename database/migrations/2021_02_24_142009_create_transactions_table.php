<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedInteger("idEmpresa");
            $table->unsignedBigInteger("idCaja");
            $table->decimal("monto", 20, 2);
            $table->string("comentario")->nullable();
            $table->integer("status")->default(1);
            $table->unsignedInteger("idTipo");
            $table->unsignedInteger("idTipoPago")->nullable();
            $table->unsignedBigInteger("idReferencia")->nullable();
            $table->unsignedInteger("idTipoIngresoEgreso")->nullable();
            $table->timestamps();

            $table->foreign("idUsuario")->references("id")->on("users");
            $table->foreign("idEmpresa")->references("id")->on("companies");
            $table->foreign("idCaja")->references("id")->on("boxes");
            $table->foreign("idTipo")->references("id")->on("types");
            $table->foreign("idTipoPago")->references("id")->on("types");
            $table->foreign("idTipoIngresoEgreso")->references("id")->on("types");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
