<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('idEmpresa')->references('id')->on('companies');
            $table->foreign('idSucursal')->references('id')->on('branchoffices');
            $table->foreign('idRol')->references('id')->on('roles');
            $table->foreign('idContacto')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['idEmpresa', 'idSucursal', 'idRol', 'idContacto']);
        });
    }
}
