<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BendaharaKepalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'bk@gmail.com'],
            [
                'name' => 'Bendahara Kepala',
                'password' => Hash::make('admin123'),
                'role' => 'Bendahara Kepala',
            ]
        );
    }
}
