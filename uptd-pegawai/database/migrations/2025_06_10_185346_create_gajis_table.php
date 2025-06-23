<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGajisTable extends Migration
{
    public function up()
    {
        Schema::create('gajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais');
            $table->integer('bulan'); // 1-12
            $table->integer('tahun');
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('total_potongan', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);
            $table->decimal('insentif_tetap', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->json('rincian_potongan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gajis');
    }
}
