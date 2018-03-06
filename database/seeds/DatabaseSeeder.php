<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $mood = factory(App\Mood::class, 5) -> create()->each(function($f) {
            $f->likes()->save(factory(App\User::class)->make());
        });;

        //$this->call(CitiesTableSeeder::class);
        $this->command->info('Seeded the cities table ...'); 

        $this->call(ReportsTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
    }
}
