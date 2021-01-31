<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments("id");
            $table->string("descripcion");
            $table->unsignedInteger("idBanco");
            $table->unsignedInteger("idEmpresa");
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("idBanco")->references("id")->on("banks");

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
        $table->dropForeign(["idBanco"]);
        Schema::dropIfExists('accounts');
    }
}
