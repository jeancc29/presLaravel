<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments("id");
            $table->string("name");
            $table->string("occupation");
            $table->decimal("income", 20, 2);
            $table->decimal("otherIncome", 20, 2);
            $table->date("admissionDate");
            $table->unsignedInteger("idAddress");
            // $table->unsignedInteger("idCliente");
            $table->unsignedInteger("idContact");

            $table->foreign("idAddress")->references("id")->on("addresses");
            // $table->foreign("idCliente")->references("id")->on("customers");
            $table->foreign("idContact")->references("id")->on("contacts");
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
        Schema::dropIfExists('jobs');
    }
}
