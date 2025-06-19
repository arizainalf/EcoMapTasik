<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_price', 15, 2);
            $table->foreignId('bank_account_id')->nullable()->constrained()->onDelete('set null')->nullable(); // Asumsi nama tabelnya bank_accounts
            $table->string('payment_proof')->nullable(); // Path atau URL bukti bayar
            $table->timestamp('paid_at')->nullable();
            $table->string('courier')->nullable();
            $table->foreignId('address_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number')->nullable();
            $table->string('status')->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}

