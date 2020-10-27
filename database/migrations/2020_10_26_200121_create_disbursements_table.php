<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("idTipo");
            $table->integer("idBanco")->nullable();
            $table->string("numeroCheque")->nullable();
            $table->integer("idBancoDestino")->nullable();
            $table->integer("idCuentaDestino")->nullable();
            $table->double("montoBruto", 20, 2);
            $table->double("montoNeto", 20, 2);
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
        Schema::dropIfExists('disbursements');
    }
}
