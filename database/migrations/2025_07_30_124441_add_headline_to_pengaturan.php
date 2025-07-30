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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('headline')->nullable()->after('app_description');
            $table->text('subheadline')->nullable()->after('headline');
            $table->dropColumn('kata_slider_1');
            $table->dropColumn('kata_slider_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('headline');
            $table->dropColumn('subheadline');
        });
    }
};
