<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollResultsTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pegawai_id');
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->json('hasil_perhitungan'); // Simpan hasil perhitungan dalam bentuk JSON
            $table->timestamps();

            $table->unique(['pegawai_id', 'tahun', 'bulan']);
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_results');
    }
}
