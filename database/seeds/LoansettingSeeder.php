<?php

use Illuminate\Database\Seeder;

class LoansettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loanSetting = \App\Loansetting::first();
        if($loanSetting != null)
            return;

        \App\Loansetting::create([
            "guarantee" => 0,
            "expense" => 0,
            "disbursement" => 0,
        ]);
    }
}
