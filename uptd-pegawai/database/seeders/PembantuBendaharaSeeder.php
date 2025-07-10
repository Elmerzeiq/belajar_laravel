<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PembantuBendaharaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pb@gmail.com'],
            [
                'name' => 'Pembantu Bendahara',
                'password' => Hash::make('admin123'),
                'role' => 'Pembantu Bendahara',
            ]
        );
    }
}
