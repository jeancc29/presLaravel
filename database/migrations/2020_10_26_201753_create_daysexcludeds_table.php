<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaysexcludedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daysexcludeds', function (Blueprint $table) {
            $table->increments("id");
            $table->unsignedBigInteger("idPrestamo");
            $table->unsignedInteger("idDia");
            $table->foreign("idPrestamo")->references("id")->on("loans");
            $table->foreign("idDia")->references("id")->on("days");
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
        $table->dropForeign(["idPrestamo"]);
        $table->dropForeign(["idDia"]);
        Schema::dropIfExists('daysexcludeds');
    }
}
