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
        $this->truncateTables(["boxes", "countries", "states", "cities", "types", "days", "permissions", "roles", "entities", "permission_role"]);
        $this->call(BoxSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(StateSeeder::class);
        $this->call(TypeSeeder::class);
        $this->call(LoansettingSeeder::class);
        $this->call(DaySeeder::class);
        $this->call(EntitySeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);
    }

    public function truncateTables(array $tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach($tables as $t){
            DB::table($t)->truncate();
        }
    }
}
