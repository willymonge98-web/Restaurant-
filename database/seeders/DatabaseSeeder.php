<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use App\Models\RestaurantSetting;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'label_fr' => 'Administrateur'],
            ['name' => 'Manager', 'slug' => 'manager', 'label_fr' => 'Gérant'],
            ['name' => 'Cashier', 'slug' => 'cashier', 'label_fr' => 'Caissier'],
            ['name' => 'Waiter', 'slug' => 'waiter', 'label_fr' => 'Serveur'],
            ['name' => 'Cook', 'slug' => 'cook', 'label_fr' => 'Cuisinier'],
            ['name' => 'Storekeeper', 'slug' => 'storekeeper', 'label_fr' => 'Magasinier'],
            ['name' => 'Accountant', 'slug' => 'accountant', 'label_fr' => 'Comptable'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        RestaurantSetting::query()->updateOrCreate(['id' => 1], [
            'name' => 'LÉNA RESTAURANT',
            'address' => 'Avenue de la Paix, Kinshasa',
            'phone' => '+243 900 000 000',
            'email' => 'contact@lena.restaurant',
            'currency_primary' => 'USD',
            'currency_secondary' => 'CDF',
            'exchange_rate' => 2800,
            'ticket_footer' => "Merci d'avoir choisi LÉNA RESTAURANT !\nÀ bientôt.",
            'tax_label' => 'TVA',
            'tax_rate' => 0,
        ]);

        $adminRole = Role::where('slug', 'admin')->first();
        User::updateOrCreate(
            ['email' => 'admin@lena.restaurant'],
            [
                'name' => 'Admin LÉNA',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'phone' => '+243 900 000 001',
                'is_active' => true,
            ]
        );

        foreach ([
            ['name' => 'Marie Caissière', 'email' => 'caissier@lena.restaurant', 'slug' => 'cashier'],
            ['name' => 'Jean Serveur', 'email' => 'serveur@lena.restaurant', 'slug' => 'waiter'],
            ['name' => 'Paul Cuisinier', 'email' => 'cuisine@lena.restaurant', 'slug' => 'cook'],
            ['name' => 'Claire Magasin', 'email' => 'stock@lena.restaurant', 'slug' => 'storekeeper'],
        ] as $demo) {
            User::updateOrCreate(
                ['email' => $demo['email']],
                [
                    'name' => $demo['name'],
                    'password' => Hash::make('password'),
                    'role_id' => Role::where('slug', $demo['slug'])->value('id'),
                    'is_active' => true,
                ]
            );
        }

        $categories = [
            ['name' => 'Entrées', 'type' => 'food', 'sort_order' => 1],
            ['name' => 'Plats principaux', 'type' => 'food', 'sort_order' => 2],
            ['name' => 'Accompagnements', 'type' => 'food', 'sort_order' => 3],
            ['name' => 'Desserts', 'type' => 'food', 'sort_order' => 4],
            ['name' => 'Boissons chaudes', 'type' => 'drink', 'sort_order' => 5],
            ['name' => 'Boissons froides', 'type' => 'drink', 'sort_order' => 6],
            ['name' => 'Bières & vins', 'type' => 'drink', 'sort_order' => 7],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [...$cat, 'is_active' => true]
            );
        }

        $ingredients = [
            ['name' => 'Poulet', 'unit' => 'g', 'stock_qty' => 20000, 'stock_alert' => 2000, 'unit_cost' => 0.008],
            ['name' => 'Riz', 'unit' => 'g', 'stock_qty' => 50000, 'stock_alert' => 5000, 'unit_cost' => 0.0015],
            ['name' => 'Huile', 'unit' => 'ml', 'stock_qty' => 10000, 'stock_alert' => 1000, 'unit_cost' => 0.002],
            ['name' => 'Oignon', 'unit' => 'g', 'stock_qty' => 8000, 'stock_alert' => 1000, 'unit_cost' => 0.001],
            ['name' => 'Tomate', 'unit' => 'g', 'stock_qty' => 8000, 'stock_alert' => 1000, 'unit_cost' => 0.0012],
            ['name' => 'Farine', 'unit' => 'g', 'stock_qty' => 15000, 'stock_alert' => 2000, 'unit_cost' => 0.001],
            ['name' => 'Sucre', 'unit' => 'g', 'stock_qty' => 10000, 'stock_alert' => 1000, 'unit_cost' => 0.001],
            ['name' => 'Café grains', 'unit' => 'g', 'stock_qty' => 5000, 'stock_alert' => 500, 'unit_cost' => 0.02],
            ['name' => 'Lait', 'unit' => 'ml', 'stock_qty' => 12000, 'stock_alert' => 1500, 'unit_cost' => 0.0015],
        ];

        $ingredientModels = [];
        foreach ($ingredients as $ing) {
            $ingredientModels[$ing['name']] = Ingredient::updateOrCreate(
                ['name' => $ing['name']],
                [...$ing, 'sku' => 'ING-'.Str::upper(Str::substr(Str::slug($ing['name']), 0, 8)), 'is_active' => true]
            );
        }

        $products = [
            ['name' => 'Salade maison', 'category' => 'Entrées', 'type' => 'food', 'price' => 5.00, 'recipe' => [
                ['Oignon', 50], ['Tomate', 80], ['Huile', 15],
            ]],
            ['name' => 'Poulet grillé', 'category' => 'Plats principaux', 'type' => 'food', 'price' => 12.00, 'recipe' => [
                ['Poulet', 350], ['Huile', 20], ['Oignon', 40],
            ]],
            ['name' => 'Riz sauce tomate', 'category' => 'Plats principaux', 'type' => 'food', 'price' => 8.50, 'recipe' => [
                ['Riz', 250], ['Tomate', 120], ['Huile', 25], ['Oignon', 40],
            ]],
            ['name' => 'Frites', 'category' => 'Accompagnements', 'type' => 'food', 'price' => 3.50, 'recipe' => [
                ['Huile', 80],
            ]],
            ['name' => 'Gâteau maison', 'category' => 'Desserts', 'type' => 'food', 'price' => 4.00, 'recipe' => [
                ['Farine', 80], ['Sucre', 40], ['Lait', 50],
            ]],
            ['name' => 'Café', 'category' => 'Boissons chaudes', 'type' => 'drink', 'price' => 2.00, 'recipe' => [
                ['Café grains', 18],
            ]],
            ['name' => 'Jus naturel', 'category' => 'Boissons froides', 'type' => 'drink', 'price' => 2.50, 'track_stock' => true, 'stock_qty' => 40],
            ['name' => 'Bière locale', 'category' => 'Bières & vins', 'type' => 'drink', 'price' => 2.00, 'track_stock' => true, 'stock_qty' => 96],
        ];

        foreach ($products as $p) {
            $product = Product::updateOrCreate(
                ['name' => $p['name']],
                [
                    'category_id' => $categoryModels[$p['category']]->id,
                    'sku' => 'PRD-'.Str::upper(Str::substr(Str::slug($p['name']), 0, 10)),
                    'type' => $p['type'],
                    'price' => $p['price'],
                    'cost_price' => 0,
                    'track_stock' => $p['track_stock'] ?? false,
                    'stock_qty' => $p['stock_qty'] ?? 0,
                    'stock_alert' => 10,
                    'unit' => 'unit',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            if (! empty($p['recipe'])) {
                $recipe = Recipe::updateOrCreate(
                    ['product_id' => $product->id],
                    ['name' => 'Recette '.$product->name, 'is_active' => true]
                );
                $recipe->items()->delete();
                foreach ($p['recipe'] as [$ingName, $qty]) {
                    $recipe->items()->create([
                        'ingredient_id' => $ingredientModels[$ingName]->id,
                        'quantity' => $qty,
                    ]);
                }
                $product->update(['cost_price' => $recipe->fresh()->computeCost()]);
            }
        }

        $tables = [
            ['name' => 'Table 1', 'code' => 'T1', 'capacity' => 2, 'zone' => 'Salle'],
            ['name' => 'Table 2', 'code' => 'T2', 'capacity' => 4, 'zone' => 'Salle'],
            ['name' => 'Table 3', 'code' => 'T3', 'capacity' => 4, 'zone' => 'Salle'],
            ['name' => 'Table 4', 'code' => 'T4', 'capacity' => 6, 'zone' => 'Terrasse'],
            ['name' => 'Table 5', 'code' => 'T5', 'capacity' => 2, 'zone' => 'Terrasse'],
            ['name' => 'VIP 1', 'code' => 'VIP1', 'capacity' => 8, 'zone' => 'VIP'],
        ];

        foreach ($tables as $table) {
            DiningTable::updateOrCreate(
                ['code' => $table['code']],
                [...$table, 'status' => DiningTable::STATUS_LIBRE, 'is_active' => true]
            );
        }

        $this->call(CongoleseDishesSeeder::class);
    }
}
