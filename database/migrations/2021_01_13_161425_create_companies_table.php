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
            $table->text("direccion")->nullable();
            $table->unsignedInteger("idContacto");
            $table->unsignedInteger("idMoneda");
            $table->unsignedInteger("idNacionalidad")->nullable();
            // $table->unsignedInteger("idContacto");
            // $table->unsignedInteger("idCliente");

            $table->foreign("idContacto")->references("id")->on("contacts");
            $table->foreign("idTipoMora")->references("id")->on("types");
//            $table->foreign("idNacionalidad")->references("id")->on("nationalities");
//            $table->foreign("idMoneda")->references("id")->on("coins");
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
