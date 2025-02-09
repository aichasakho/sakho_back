<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BiensTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $imageFiles = [
            'apart1.jpg',
            'apart2.jpg',
            'apart3.jpg',
            'apart4.jpg',
            'apart5.jpg',
        ];
        foreach (range(1, 10) as $index) {
            DB::table('biens')->insert([
                'titre' => $faker->sentence(3),
                'description' => $faker->paragraph,
                'prix' => $faker->numberBetween(80000, 90000000),
                'superficie' => $faker->numberBetween(20, 2000),
                'nombre_chambres' => $faker->numberBetween(1, 12),
                'nombre_douches' => $faker->numberBetween(1, 6),
                'disponible' => $faker->boolean,
                'type_annonce' => $faker->randomElement(['vente', 'location']),
                //'imagePath' => "images/image.jpg", // le chemin n'existait pas raison pour laquelle il etait impossible d'enregistrer des images valide dans la base
                //'imagePath' => "https://picsum.photos/640/480?random=" . $index, // permet de generer des images sur internet mais pas des image d'appartement
                'imagePath' => $faker->randomElement($imageFiles),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
