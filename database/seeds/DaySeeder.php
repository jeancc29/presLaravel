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
        \App\Day::updateOrCreate(["dia" => "Lunes", "weekday" => 1]);
        \App\Day::updateOrCreate(["dia" => "Martes", "weekday" => 2]);
        \App\Day::updateOrCreate(["dia" => "Miercoles", "weekday" => 3]);
        \App\Day::updateOrCreate(["dia" => "Jueves", "weekday" => 4]);
        \App\Day::updateOrCreate(["dia" => "Viernes", "weekday" => 5]);
        \App\Day::updateOrCreate(["dia" => "Sabado", "weekday" => 6]);
        \App\Day::updateOrCreate(["dia" => "Domingo", "weekday" => 0]);
    }
}
