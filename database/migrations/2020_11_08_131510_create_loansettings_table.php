<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loansettings', function (Blueprint $table) {
            $table->increments("id");
            $table->boolean("gasto")->default(0);
            $table->boolean("garantia")->default(0);
            $table->boolean("desembolso")->default(0);
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
        Schema::dropIfExists('loansettings');
    }
}
