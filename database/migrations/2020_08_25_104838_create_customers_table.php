<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("foto")->nullable();
            $table->string("nombres");
            $table->string("apellidos");
            $table->string("apodo")->nullable();
            $table->date("fechaNacimiento");
            $table->integer("numeroDependientes")->nullable();
            $table->string("sexo");
            $table->string("estadoCivil");
            $table->string("tipoVivienda");
            $table->string("tiempoEnVivienda")->nullable();
            $table->string("referidoPor")->nullable();
            $table->integer("estado")->default(1);
            $table->unsignedInteger('idEmpresa');
            $table->unsignedInteger('idContacto');
            $table->unsignedInteger('idDireccion');
            $table->unsignedInteger('idDocumento');
            $table->unsignedInteger('idTrabajo')->nullable();
            $table->unsignedInteger('idNegocio')->nullable();
            $table->unsignedInteger('idTipoSituacionLaboral')->nullable();
            $table->unsignedInteger('idRuta')->nullable();
            
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
        Schema::dropIfExists('customers');
    }
}
