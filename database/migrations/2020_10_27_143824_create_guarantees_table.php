<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuaranteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantees', function (Blueprint $table) {
            $table->increments("id");
            $table->decimal("tasacion", 20, 2);
            $table->string("descripcion");
            $table->string("matricula");
            $table->string("marca");
            $table->string("chasis");
            $table->integer("estado");
            $table->string("placa", 30);
            $table->date("anoFabricacion");
            $table->string("motorOSerie", 50);
            $table->integer("cilindros");
            $table->string("color", 50);
            $table->integer("numeroPasajeros");
            $table->integer("numeroPuertas");
            $table->integer("fuerzaMotriz");
            $table->integer("capacidadCarga");
            $table->string("placaAnterior", 30);
            $table->date("fechaExpedicion", 30);
            $table->string("foto");
            $table->string("fotoMatricula");
            $table->string("fotoLicencia");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idTipo");
            $table->unsignedInteger("idTipoEmision");
            $table->unsignedInteger("idTipoCondicion");
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
        Schema::dropIfExists('guarantees');
    }
}
