<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('serapan_sampahs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tempat_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('total');
            $table->integer('organic');
            $table->integer('anorganic');
            $table->integer('residu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serapan_sampahs');
    }
};
