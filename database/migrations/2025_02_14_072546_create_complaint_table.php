<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->integer('age');
            $table->string('complaint'); // Ubah ke TEXT agar lebih fleksibel
            $table->string('diagnosis'); // Ubah ke TEXT agar lebih fleksibel
            $table->enum('ruangan', ['rawat inap', 'icu', 'bersalin', 'hcu', 'nicu', 'picu', 'isolasi', 'rehabilitas']);
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');

    }
};
