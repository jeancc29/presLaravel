<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string('foto')->nullable();
            $table->string('usuario')->nullable();
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedInteger('idEmpresa');
            $table->unsignedInteger('idRol');
            $table->unsignedInteger('idSucursal');
            $table->unsignedInteger('idContacto');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
