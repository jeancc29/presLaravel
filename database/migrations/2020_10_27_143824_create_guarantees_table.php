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
            $table->string("matricula")->nullable();
            $table->string("marca")->nullable();
            $table->string("modelo")->nullable();
            $table->string("chasis")->nullable();
            $table->integer("estado");
            $table->string("placa", 30)->nullable();
            $table->date("anoFabricacion")->nullable();
            $table->string("motorOSerie", 50)->nullable();
            $table->integer("cilindros")->nullable();
            $table->string("color", 50)->nullable();
            $table->integer("numeroPasajeros")->nullable();
            $table->integer("numeroPuertas")->nullable();
            $table->integer("fuerzaMotriz")->nullable();
            $table->integer("capacidadCarga")->nullable();
            $table->string("placaAnterior", 30)->nullable();
            $table->date("fechaExpedicion", 30)->nullable();
            $table->string("foto")->nullable();
            $table->string("fotoMatricula")->nullable();
            $table->string("fotoLicencia")->nullable();
            $table->unsignedInteger("idEmpresa");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idTipo");
            // $table->unsignedInteger("idTipoEmision");
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
