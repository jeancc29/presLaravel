<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->decimal("amount", 20, 2);
            $table->double("interestPercent", 5, 2);
            $table->double("annualInterestPercent", 5, 2);
            $table->integer("quotas");
            $table->date("date");
            $table->date("firstPaymentDate");
            $table->string("uniqueCode");
            $table->double("penaltyPercent", 5, 2);
            $table->integer("daysOfGrace");
            $table->unsignedBigInteger("idUser");
            $table->unsignedBigInteger("idCustomer");
            $table->unsignedInteger("idTypeTerm");
            $table->unsignedInteger("idTypeAmortization");
            $table->unsignedBigInteger("idBox");
            $table->unsignedInteger("idCollector");
            $table->unsignedInteger("idExpense");
            $table->unsignedInteger("idDisbursement");
            //la llave foranea del idUser se agregara despues de crear la tabla usuario
            $table->foreign("idCustomer")->references("id")->on("customers");
            $table->foreign("idTypeTerm")->references("id")->on("types");
            $table->foreign("idTypeAmortization")->references("id")->on("types");
            $table->foreign("idBox")->references("id")->on("boxes");
            // $table->foreign("idCollector")->references("id")->on("boxes");
            //la llave foranea del idExpense se agregara despues de crear la tabla Loanexpenses
            $table->foreign("idDisbursement")->references("id")->on("disbursements");
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
        $table->dropForeign(['idDisbursement', 'idTypeAmortization', 'idTypeTerm', 'idCustomer', 'idBox']);
        Schema::dropIfExists('loans');
    }
}
