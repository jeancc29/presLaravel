<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->increments("id");
            $table->string("nombre");
            $table->string("foto")->nullable();
            $table->integer("status");
            $table->integer("diasGracia")->default(0);
            $table->decimal("porcentajeMora", 10, 2)->default(0);
            $table->unsignedInteger("idEmpresa");
            $table->unsignedInteger("idTipoMora");
            $table->unsignedInteger("idDireccion");
            $table->unsignedInteger("idContacto");
            $table->unsignedInteger("idMoneda");
            // $table->unsignedInteger("idContacto");
            // $table->unsignedInteger("idCliente");

            $table->foreign("idDireccion")->references("id")->on("addresses");
            $table->foreign("idContacto")->references("id")->on("contacts");
            $table->foreign("idTipoMora")->references("id")->on("types");
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
        Schema::dropIfExists('companies');
    }
}
