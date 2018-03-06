<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('category')->delete();

        $item = array(

            array(
                'icon' => 'public',
                'description' => 'Tutti',   
                'color' => 'grey',
                'colorIcon' => 'grey darken-4',  
            ),

            array(
                'icon' => 'account_balance',
                'description' => 'Politica',   
                'color' => 'blue-grey',
                'colorIcon' => 'blue-grey darken-4',    
            ),

            array(
                'icon' => 'gamepad',
                'description' => 'Giochi',   
                'color' => 'red',
                'colorIcon' => 'red darken-4',   
            ),

            array(
                'icon' => 'movie_filter',
                'description' => 'Film',   
                'color' => 'brown',
                'colorIcon' => 'brown darken-4',   
            ),

            array(
                'icon' => 'headset',
                'description' => 'Musica',   
                'color' => 'indigo',
                'colorIcon' => 'indigo darken-4',   
            ),

            array(
                'icon' => 'sentiment_satisfied',
                'description' => 'Divertenti',   
                'color' => 'yellow',
                'colorIcon' => 'yellow darken-4',   
            ),

            array(
                'icon' => 'favorite',
                'description' => 'Amore',   
                'color' => 'pink',
                'colorIcon' => 'pink darken-4',   
            ),

            array(
                'icon' => 'people',
                'description' => 'Amicizia',   
                'color' => 'deep-purple',
                'colorIcon' => 'deep-purple darken-4',   
            ),

            array(
                'icon' => 'cake',
                'description' => 'Cibo',   
                'color' => 'light-blue',
                'colorIcon' => 'light-blue darken-4',   
            ),

            array(
                'icon' => 'local_florist',
                'description' => 'Ambiente',   
                'color' => 'green',
                'colorIcon' => 'green darken-4',   
            ),

            array(
                'icon' => 'sentiment_neutral',
                'description' => 'Sfogo',   
                'color' => 'teal',
                'colorIcon' => 'teal darken-4',   
            ),

            array(
                'icon' => 'mood_bad',
                'description' => 'Figuracce',   
                'color' => 'lime',
                'colorIcon' => 'lime darken-4',   
            ),

            array(
                'icon' => 'important_devices',
                'description' => 'Tecnologia',   
                'color' => 'orange',
                'colorIcon' => 'orange darken-4',   
            ),
        );

        DB::table('category')->insert($item);
    }
}
