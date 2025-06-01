<?php
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Toode 1: Nutikas Termos',
                'description' => 'Hoiab joogi soojana või külmana tundideks. Digitaalne temperatuurinäidik.',
                'price' => 24.99,
                'image_url' => 'https://placehold.co/300x200/EBF4FF/76A9FA?text=Toode+1',
            ],
            [
                'name' => 'Toode 2: Juhtmevabad Kõrvaklapid',
                'description' => 'Kvaliteetne heli ja mürasummutus. Pikk aku kestvus.',
                'price' => 79.50,
                'image_url' => 'https://placehold.co/300x200/E0F2F7/76D7FA?text=Toode+2',
            ],
            [
                'name' => 'Toode 3: Orgaaniline Kohv',
                'description' => 'Fairtrade sertifikaadiga, keskmise röstiga araabika oad.',
                'price' => 9.90,
                'image_url' => 'https://placehold.co/300x200/FFF9C4/FFEB3B?text=Toode+3',
            ],
            [
                'name' => 'Toode 4: Käsitsi Valmistatud Seep',
                'description' => 'Naturaalsetest koostisosadest, lavendli lõhnaga.',
                'price' => 6.75,
                'image_url' => 'https://placehold.co/300x200/F3E5F5/CE93D8?text=Toode+4',
            ],
            [
                'name' => 'Toode 5: Joogamatt',
                'description' => 'Libisemiskindel ja keskkonnasõbralik materjal.',
                'price' => 32.00,
                'image_url' => 'https://placehold.co/300x200/E8F5E9/A5D6A7?text=Toode+5',
            ],
            [
                'name' => 'Toode 6: Nutikell',
                'description' => 'Jälgib aktiivsust, und ja pulssi. Ühildub telefoniga.',
                'price' => 129.00,
                'image_url' => 'https://placehold.co/300x200/D1C4E9/B39DDB?text=Toode+6',
            ],
            [
                'name' => 'Toode 7: Lauamäng "Katani Asustajad"',
                'description' => 'Populaarne strateegiamäng kogu perele.',
                'price' => 45.99,
                'image_url' => 'https://placehold.co/300x200/FFCCBC/FF8A65?text=Toode+7',
            ],
            [
                'name' => 'Toode 8: Kvaliteetne Seljakott',
                'description' => 'Veekindel materjal, palju taskuid, sobib sülearvutile.',
                'price' => 55.00,
                'image_url' => 'https://placehold.co/300x200/CFD8DC/B0BEC5?text=Toode+8',
            ],
            [
                'name' => 'Toode 9: Akupank 20000mAh',
                'description' => 'Kiire laadimisega ja mitme seadme toetusega.',
                'price' => 39.99,
                'image_url' => 'https://placehold.co/300x200/B2EBF2/80DEEA?text=Toode+9',
            ],
            [
                'name' => 'Toode 10: Kokaraamat "Eesti Maitsed"',
                'description' => 'Traditsioonilised ja modernsed Eesti retseptid.',
                'price' => 19.95,
                'image_url' => 'https://placehold.co/300x200/FFE0B2/FFCC80?text=Toode+10',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
