<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boxes', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string("descripcion");
            $table->decimal("balanceInicial", 20, 2)->default(0);
            $table->decimal("balance", 20, 2)->default(0);
            $table->boolean("validarDesgloseEfectivo")->default(0);
            $table->boolean("validarDesgloseCheques")->default(0);
            $table->boolean("validarDesgloseTarjetas")->default(0);
            $table->boolean("validarDesgloseTransferencias")->default(0);
            $table->unsignedInteger("idEmpresa");
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropSoftDeletes();
        Schema::dropIfExists('boxes');
    }
}
