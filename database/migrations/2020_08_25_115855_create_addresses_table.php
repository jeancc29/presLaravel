<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments("id");
            $table->text("direccion")->nullable();
            $table->text("direccion2")->nullable();
            $table->string("codigoPostal")->nullable();
//            $table->unsignedInteger("idEstado");
//            $table->unsignedInteger("idCiudad");
//            $table->unsignedInteger("idPais")->nullable();
//            $table->string("sector")->nullable();
//            $table->string("numero")->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
