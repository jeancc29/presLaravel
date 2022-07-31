<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->date("fecha");
            $table->string("concepto");
            $table->double("monto", 20);
            $table->text("comentario")->nullable();
            $table->unsignedBigInteger("idCaja")->nullable();
            $table->unsignedInteger("idTipo"); //Tipo categoria
            $table->unsignedInteger("idTipoPago"); //Tipo categoria
            $table->unsignedInteger("idUsuario");
            $table->unsignedInteger("idEmpresa");
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
        Schema::dropIfExists('expenses');
    }
}
