<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah dari VARCHAR(255) atau VARCHAR yang kecil menjadi VARCHAR(255)
            $table->string('payment_method', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('payment_method', 50)->nullable()->change();
        });
    }
};