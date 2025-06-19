<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Kolom id (primary key)
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Relasi ke tabel categories
            $table->string('name'); // Nama produk
            $table->text('description')->nullable(); // Deskripsi produk
            $table->decimal('price', 15, 2); // Harga produk
            $table->decimal('weight', 8, 2)->default(0); // Berat produk (dalam gram atau kg tergantung satuan)
            $table->integer('stock')->default(0); // Stok produk
            $table->string('image')->nullable(); // Path atau URL gambar produk
            $table->timestamps(); // created_at dan updated_at
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}

