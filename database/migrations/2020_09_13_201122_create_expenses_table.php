<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->date("date");
            $table->string("concept");
            $table->double("amount", 20);
            $table->text("commentary")->nullable();
            $table->unsignedBigInteger("idBox");
            $table->unsignedInteger("idType"); //Tipo categoria
            $table->unsignedInteger("idUser");
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
        Schema::dropIfExists('expenses');
    }
}
