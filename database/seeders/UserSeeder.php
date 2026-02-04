<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Petugas TU',
            'email' => 'petugas@example.com',
            'password' => Hash::make('password'),
            'role' => 'petugas'
        ]);

        User::create([
            'name' => 'Kepala Sekolah',
            'email' => 'kepalasekolah@example.com',
            'password' => Hash::make('password'),
            'role' => 'kepala_sekolah'
        ]);
    }
}