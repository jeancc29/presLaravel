<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchofficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branchoffices', function (Blueprint $table) {
            $table->increments("id");
            $table->string("nombre");
            $table->string("direccion");
            $table->string("ciudad");
            $table->string("telefono1")->nullable();
            $table->string("telefono2")->nullable();
            $table->string("gerenteSucursal")->nullable();
            $table->string("gerenteCobro")->nullable();
            $table->boolean("status")->default(1);
            $table->string("foto")->nullable();
            $table->unsignedInteger("idEmpresa");
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
        Schema::dropIfExists('branchoffices');
    }
}
