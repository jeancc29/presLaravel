<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments("id");
            $table->string("nombre");
            $table->string("ocupacion");
            $table->decimal("ingresos", 20, 2);
            $table->decimal("otrosIngresos", 20, 2);
            $table->date("fechaIngreso");
            $table->unsignedInteger("idDireccion")->nullable();
            // $table->unsignedInteger("idCliente");
            $table->unsignedInteger("idContacto");

            $table->foreign("idDireccion")->references("id")->on("addresses");
            // $table->foreign("idCliente")->references("id")->on("customers");
            $table->foreign("idContacto")->references("id")->on("contacts");
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
        Schema::dropIfExists('jobs');
    }
}
