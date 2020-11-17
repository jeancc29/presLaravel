<?php

use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Day::updateOrCreate(["dia" => "Lunes"]);
        \App\Day::updateOrCreate(["dia" => "Martes"]);
        \App\Day::updateOrCreate(["dia" => "Miercoles"]);
        \App\Day::updateOrCreate(["dia" => "Jueves"]);
        \App\Day::updateOrCreate(["dia" => "Viernes"]);
        \App\Day::updateOrCreate(["dia" => "Sabado"]);
        \App\Day::updateOrCreate(["dia" => "Domingo"]);
    }
}
