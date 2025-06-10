<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('potongan_tetap', function (Blueprint $table) {
            $table->id();
            $table->string('nama_potongan');
            $table->enum('tipe', ['tetap', 'persen']);
            $table->decimal('jumlah', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('potongan_tetap');
    }
};
