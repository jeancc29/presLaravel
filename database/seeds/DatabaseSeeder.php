<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables(["boxes", "countries", "states", "cities", "types", "days", "permissions", "roles", "entities", "permission_role", "coins", "branchoffices", "companies", "nationalities"]);
        $this->call(BoxSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(LoansettingSeeder::class);
        $this->call(DaySeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(CoinSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(BranchofficeSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(NationalitySeeder::class);
    }

    public function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach($tables as $t){
            DB::table($t)->truncate();
        }
    }
}
