<?php

use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reports')->delete();

        $item = array(

            array(
                'code' => 'sexContent',
                'description' => 'Sexual Content',     
            ),

            array(
                'code' => 'violentContent',
                'description' => 'Violent or repulsive content',     
            ),

            array(
                'code' => 'AbuseContent',
                'description' => 'Hateful or abusive content',     
            ),

            array(
                'code' => 'dangerusAct',
                'description' => 'Harmful dangerous acts',     
            ),

            array(
                'code' => 'terrorisContent',
                'description' => 'Promotes terrorism',     
            ),

            array(
                'code' => 'spamContent',
                'description' => 'Spam or misleading',     
            ),
        );

        DB::table('reports')->insert($item);
    }
}
