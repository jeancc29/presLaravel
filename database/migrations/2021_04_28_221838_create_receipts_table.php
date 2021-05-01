<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedInteger("idEmpresa");
            $table->boolean("copia");
            $table->boolean("capital");
            $table->boolean("mora");
            $table->boolean("interes");
            $table->boolean("descuento");
            $table->boolean("capitalPendiente");
            $table->boolean("balancePendiente");
            $table->boolean("fechaProximoPago");
            $table->boolean("formaPago");
            $table->boolean("firma");
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
        Schema::dropIfExists('receipts');
    }
}
