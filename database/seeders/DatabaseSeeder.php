<?php
namespace Database\Seeders;

use App\Models\Setting;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Ari Zainal Fauziah',
            'email'    => 'admin@gmail.com',
            'password' => bcrypt('123123123'),
            'role'     => 'admin',
        ]);
        User::create([
            'name'     => 'Ari Zainal Fauziah',
            'email'    => 'arizainalf@gmail.com',
            'password' => bcrypt('123123123'),
            'role'     => 'user',
        ]);

        Setting::create([
            'app_name'        => 'Bumi Karpet',
            'app_description' => 'Aplikasi Bumi Karpet ini merupakan aplikasi penjualan karpet tekstil online.',
            'province'        => 'DKI JAKARTA',
            'city'            => 'JAKARTA PUSAT',
            'address'         => 'GAMBIR, GAMBIR, JAKARTA PUSAT, DKI JAKARTA, 10110',
            'district'        => 'GAMBIR',
            'subdistrict'     => 'GAMBIR',
            'postal_code'     => '17601',
            'phone_number'    => '081234567890',
            'email'           => 'info@aplikasi-saya.com',
            'logo'            => 'logos/default-logo.png',
            'slider_1'        => 'sliders/slider1.jpg',
            'slider_2'        => 'sliders/slider2.jpg',
            'kata_slider_1'   => 'Selamat Datang di Aplikasi Bumi Karpet',
            'kata_slider_2'   => 'Aplikasi Bumi Karpet ini merupakan aplikasi penjualan karpet tekstil online.',
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
