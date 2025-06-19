<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'app_name' => 'Bumi Karpet',
            'app_description' => 'Aplikasi penjualan karpet',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'address' => 'Jl. Pajak No. 123',
            'phone_number' => '08123456789',
            'email' => 'info@pajakjabar.go.id',
            'logo' => 'logo.png',
            'slider_1' => 'slider1.jpg',
            'slider_2' => 'slider2.jpg',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
