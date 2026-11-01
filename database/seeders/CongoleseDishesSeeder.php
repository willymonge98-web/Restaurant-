<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CongoleseDishesSeeder extends Seeder
{
    public function run(): void
    {
        $cuisine = Category::updateOrCreate(
            ['slug' => 'cuisine-congolaise'],
            [
                'name' => 'Cuisine congolaise',
                'type' => 'food',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        $jus = Category::updateOrCreate(
            ['slug' => 'jus-naturels'],
            [
                'name' => 'Jus naturels',
                'type' => 'drink',
                'sort_order' => 8,
                'is_active' => true,
            ]
        );

        // Prices in USD (CDF ≈ rate × USD; default seed rate 2800)
        $dishes = [
            [
                'name' => 'Poulet à la moambe',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 12.00,
                'image' => 'poulet-moambe.jpg',
                'description' => 'Poulet mijoté à la sauce moambe (huile de palme), servi avec riz ou fufu.',
            ],
            [
                'name' => 'Pondu (feuilles de manioc)',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 7.50,
                'image' => 'pondu.jpg',
                'description' => 'Feuilles de manioc pilées, mijotées à la congolaise.',
            ],
            [
                'name' => 'Fufu',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 3.00,
                'image' => 'fufu.jpg',
                'description' => 'Pâte de manioc / maïs, accompagnement classique.',
            ],
            [
                'name' => 'Chikwangue',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 2.50,
                'image' => 'chikwangue.jpg',
                'description' => 'Bâton de manioc fermenté enveloppé en feuille.',
            ],
            [
                'name' => 'Makayabu',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 9.00,
                'image' => 'makayabu.jpg',
                'description' => 'Poisson salé séché, préparé à la mode congolaise.',
            ],
            [
                'name' => 'Poisson salé / grillé',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 11.00,
                'image' => 'poisson-grille.jpg',
                'description' => 'Poisson frais grillé, assaisonné aux épices locales.',
            ],
            [
                'name' => 'Saka-saka',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 7.00,
                'image' => 'saka-saka.jpg',
                'description' => 'Variante de feuilles de manioc, souvent avec arachide.',
            ],
            [
                'name' => 'Maboké',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 10.50,
                'image' => 'maboke.jpg',
                'description' => 'Plat cuit en feuille de bananier (maboké).',
            ],
            [
                'name' => 'Liboké de poisson',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 12.50,
                'image' => 'liboke-poisson.jpg',
                'description' => 'Poisson mariné cuit en papillote de feuille de bananier.',
            ],
            [
                'name' => 'Ngombe (bœuf)',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 11.50,
                'image' => 'ngombe.jpg',
                'description' => 'Ragoût de bœuf aux oignons et épices.',
            ],
            [
                'name' => 'Brochettes',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 6.00,
                'image' => 'brochettes.jpg',
                'description' => 'Brochettes de viande grillées, street-food congolaise.',
            ],
            [
                'name' => 'Riz sauce arachide',
                'category' => $cuisine,
                'type' => 'food',
                'price' => 8.00,
                'image' => 'riz-arachide.jpg',
                'description' => 'Riz servi avec une sauce d’arachide onctueuse.',
            ],
            [
                'name' => 'Jus de bissap',
                'category' => $jus,
                'type' => 'drink',
                'price' => 2.00,
                'image' => 'jus-bissap.jpg',
                'description' => 'Boisson à l’hibiscus (bissap), fraîche et acidulée.',
                'track_stock' => true,
                'stock_qty' => 40,
            ],
            [
                'name' => 'Jus de gingembre',
                'category' => $jus,
                'type' => 'drink',
                'price' => 2.00,
                'image' => 'jus-gingembre.jpg',
                'description' => 'Jus de gingembre maison, piquant et rafraîchissant.',
                'track_stock' => true,
                'stock_qty' => 40,
            ],
        ];

        foreach ($dishes as $dish) {
            $filename = $dish['image'];
            $publicPath = public_path('images/products/'.$filename);
            $storagePath = storage_path('app/public/products/'.$filename);
            $imagePath = null;

            if (File::exists($publicPath) && File::size($publicPath) > 5000) {
                $imagePath = 'images/products/'.$filename;
            } elseif (File::exists($storagePath) && File::size($storagePath) > 5000) {
                File::ensureDirectoryExists(public_path('images/products'));
                File::copy($storagePath, $publicPath);
                $imagePath = 'images/products/'.$filename;
            }

            Product::updateOrCreate(
                ['name' => $dish['name']],
                [
                    'category_id' => $dish['category']->id,
                    'sku' => 'CG-'.Str::upper(Str::substr(Str::slug($dish['name']), 0, 12)),
                    'type' => $dish['type'],
                    'description' => $dish['description'],
                    'image_path' => $imagePath,
                    'price' => $dish['price'],
                    'cost_price' => round($dish['price'] * 0.45, 2),
                    'track_stock' => $dish['track_stock'] ?? false,
                    'stock_qty' => $dish['stock_qty'] ?? 0,
                    'stock_alert' => 10,
                    'unit' => 'unit',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );
        }
    }
}
