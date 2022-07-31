<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClosuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('closures', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedInteger("idEmpresa");
            $table->unsignedBigInteger("idUsuario");
            $table->unsignedBigInteger("idCaja");
            $table->decimal("totalSegunUsuario", 20, 2);
            $table->decimal("totalSegunSistema", 20, 2);
            $table->decimal("montoEfectivo", 20, 2);
            $table->decimal("montoCheques", 20, 2)->default(0);
            $table->decimal("montoTarjetas", 20, 2)->default(0);
            $table->decimal("montoTransferencias", 20, 2)->default(0);
            $table->decimal("diferencia", 20, 2)->default(0);
            $table->string("comentario")->nullable();
            $table->integer("status")->default(1);
            $table->timestamps();

            $table->foreign("idUsuario")->references("id")->on("users");
            $table->foreign("idEmpresa")->references("id")->on("companies");
            $table->foreign("idCaja")->references("id")->on("boxes");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('closures');
    }
}
