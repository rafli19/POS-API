<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Cash, QRIS, BCA, Mandiri, dll
            $table->string('code')->unique(); // cash, qris, bca, mandiri
            $table->string('type')->default('cash'); // cash, digital, bank
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default payment methods
        DB::table('payment_methods')->insert([
            ['name' => 'Cash', 'code' => 'cash', 'type' => 'cash', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'QRIS', 'code' => 'qris', 'type' => 'digital', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BCA', 'code' => 'bca', 'type' => 'bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mandiri', 'code' => 'mandiri', 'type' => 'bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BRI', 'code' => 'bri', 'type' => 'bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BNI', 'code' => 'bni', 'type' => 'bank', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'DANA', 'code' => 'dana', 'type' => 'digital', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'OVO', 'code' => 'ovo', 'type' => 'digital', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('payment_methods');
    }
};