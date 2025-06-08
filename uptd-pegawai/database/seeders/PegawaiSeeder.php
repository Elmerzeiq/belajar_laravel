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
            'nip' => '123456789',
            'jabatan' => 'Manager',
            'gaji_pokok' => '1000000',
            'insentif_kotor' => '500000',
        ]);
        //
    }
}
