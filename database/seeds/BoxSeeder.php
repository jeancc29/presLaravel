<?php

use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Box::updateOrCreate(
            ["description" => "Ninguna"],
            ["description" => "Ninguna", "balanceInicial" => 0],
        );
    }
}
