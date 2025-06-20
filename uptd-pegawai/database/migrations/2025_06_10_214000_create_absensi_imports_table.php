<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiImportsTable extends Migration
{
    public function up()
    {
        Schema::create('absensi_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->date('tanggal');
            $table->string('jam_masuk')->nullable();
            $table->string('jam_pulang')->nullable();
            $table->string('scan_masuk')->nullable();
            $table->string('scan_keluar')->nullable();
            $table->string('terlambat')->nullable();
            $table->string('plg_cpt')->nullable();
            $table->string('lembur')->nullable();
            $table->string('jml_hadir')->nullable();
            $table->string('pengecualian')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi_imports');
    }
}
