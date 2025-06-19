<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('categories');

        foreach (['Elektronik', 'Fashion', 'Makanan', 'Buku', 'Olahraga'] as $name) {

            Category::create([
                'name'  => $name,
                'image' => getUiAvatar($name),
            ]);
        }
    }
}
