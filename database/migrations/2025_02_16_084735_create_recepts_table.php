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
        Schema::create('recepts', function (Blueprint $table) {
            $table->id();
            $table->string('dokter');
            $table->text('obat'); // Kolom untuk menyimpan JSON
            $table->text('bentuk'); // Kolom untuk menyimpan JSON
            $table->text('jumlah'); // Kolom untuk menyimpan JSON
            $table->text('pemakaian'); // Kolom untuk menyimpan JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepts');
    }
};
