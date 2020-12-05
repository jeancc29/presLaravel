<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisbursementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("idType");
            $table->integer("idBank")->nullable();
            $table->string("checkNumber")->nullable();
            $table->integer("idBankDestination")->nullable();
            $table->integer("idAccountDestination")->nullable();
            $table->double("grossAmount", 20, 2);
            $table->double("netAmount", 20, 2);
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
        Schema::dropIfExists('disbursements');
    }
}
