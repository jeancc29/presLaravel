<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBusinessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name")->nullable();
            $table->string("type")->nullable();
            $table->string("timeInResidence")->nullable();
            $table->unsignedInteger("idAddress");
            // $table->unsignedInteger("idContacto");
            // $table->unsignedInteger("idCliente");

            $table->foreign("idAddress")->references("id")->on("addresses");
            // $table->foreign("idCliente")->references("id")->on("customers");
            // $table->foreign("idContacto")->references("id")->on("contacts");
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
        $table->dropForeign(['idCustomer', 'idAddress', 'idContact']);
        Schema::dropIfExists('businesses');
    }
}
