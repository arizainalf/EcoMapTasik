<?php
namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('products');
        $faker = Faker::create();

        foreach (Category::all() as $category) {
            foreach (range(1, 5) as $i) {
                Product::create([
                    'category_id' => $category->id,
                    'name'        => $faker->words(3, true),
                    'description' => $faker->sentence(),
                    'price'       => $faker->randomFloat(2, 10000, 1000000),
                    'weight'      => $faker->randomFloat(2, 0.5, 5),
                    'stock'       => $faker->numberBetween(10, 100),
                    'image'       => getUiAvatar($faker->words(3, true)),
                ]);
            }
        }
    }
}
