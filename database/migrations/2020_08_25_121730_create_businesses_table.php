<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments("id");
            $table->string("nombre")->nullable();
            $table->string("tipo")->nullable();
            $table->string("tiempoExistencia")->nullable();
            $table->unsignedInteger("idDireccion");
            // $table->unsignedInteger("idContacto");
            // $table->unsignedInteger("idCliente");

            $table->foreign("idDireccion")->references("id")->on("addresses");
            // $table->foreign("idCliente")->references("id")->on("customers");
            // $table->foreign("idContacto")->references("id")->on("contacts");
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
        $table->dropForeign(['idCliente', 'idDireccion', 'idContacto']);
        Schema::dropIfExists('businesses');
    }
}
