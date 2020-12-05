<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuaranteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guarantees', function (Blueprint $table) {
            $table->increments("id");
            $table->decimal("appraisal", 20, 2);
            $table->string("description");
            $table->string("vehicleRegistrationNumber");
            $table->string("brand");
            $table->string("chassis");
            $table->integer("status");
            $table->string("vehiclePlate", 30);
            $table->date("yearOfProduction");
            $table->string("motorOrSeries", 50);
            $table->integer("cylinders");
            $table->string("color", 50);
            $table->integer("numberOfPassengers");
            $table->integer("numberOfDoors");
            $table->integer("motorForce");
            $table->integer("loadCarryingCapacity");
            $table->string("previousVehiclePlate", 30);
            $table->date("expeditionDate", 30);
            $table->string("photo")->nullable();
            $table->string("vehicleRegistrationNumberPhoto")->nullable();
            $table->string("licensePhoto")->nullable();
            $table->unsignedBigInteger("idLoan");
            $table->unsignedInteger("idType");
            // $table->unsignedInteger("idTypeEmision");
            $table->unsignedInteger("idTypeCondition");
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
        Schema::dropIfExists('guarantees');
    }
}
