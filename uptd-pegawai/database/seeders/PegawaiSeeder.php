<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pegawai::create([
            'nama' => 'John Doe',
            'jabatan' => 'Manager',
            'alamat' => '123 Main St, Anytown, USA',
            'tanggal_lahir' => '1980-01-01',
        ]);
        //
    }
}
