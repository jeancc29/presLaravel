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
            $table->unsignedBigInteger("idLoan");
            $table->unsignedInteger("idDay");
            $table->foreign("idLoan")->references("id")->on("loans");
            $table->foreign("idDay")->references("id")->on("days");
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
        $table->dropForeign(["idLoan"]);
        $table->dropForeign(["idDay"]);
        Schema::dropIfExists('daysexcludeds');
    }
}
