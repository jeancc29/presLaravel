<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOthersettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('othersettings', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("idEmpresa");
            $table->boolean("ocultarInteresAmortizacion");
            $table->boolean("requirirSeleccionarCaja");
            $table->boolean("calcularComisionACuota");
            $table->boolean("mostrarCentabosRecibidos");
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
        Schema::dropIfExists('othersettings');
    }
}
