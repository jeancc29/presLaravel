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
            $table->unsignedInteger("idType");
            $table->double("percent", 5, 2);
            $table->decimal("amount", 15, 2);
            $table->foreign("idType")->references("id")->on("types");
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
