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
        \App\Day::updateOrCreate(["day" => "Lunes", "weekday" => 1]);
        \App\Day::updateOrCreate(["day" => "Martes", "weekday" => 2]);
        \App\Day::updateOrCreate(["day" => "Miercoles", "weekday" => 3]);
        \App\Day::updateOrCreate(["day" => "Jueves", "weekday" => 4]);
        \App\Day::updateOrCreate(["day" => "Viernes", "weekday" => 5]);
        \App\Day::updateOrCreate(["day" => "Sabado", "weekday" => 6]);
        \App\Day::updateOrCreate(["day" => "Domingo", "weekday" => 0]);
    }
}
