<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanexpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loanexpenses', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idTipo");
            $table->double("porcentaje", 5, 2);
            $table->decimal("importe", 15, 2);
            $table->boolean("incluirEnElFinanciamiento")->default(0);
            $table->foreign("idTipo")->references("id")->on("types");
            $table->foreign("idPrestamo")->references("id")->on("loans");
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
        $table->dropForeign(["idTipo"]);
        Schema::dropIfExists('loanexpenses');
    }
}
