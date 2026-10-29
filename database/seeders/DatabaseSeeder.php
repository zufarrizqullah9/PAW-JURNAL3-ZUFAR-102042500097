<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Pengguna LMS (1 Dosen & 1 Mahasiswa)

        // TODO 1: Buat akun awal untuk Dosen.
        User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name'        => 'Dosen CeLOE',
                'password'    => Hash::make('password'),
                'role'        => 'dosen',
                'nomor_induk' => '198001012005011001',
            ]
        );

        // Akun Mahasiswa
        User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name'        => 'Mahasiswa CeLOE',
                'password'    => Hash::make('password'),
                'role'        => 'mahasiswa',
                'nomor_induk' => '102042500097',
            ]
        );
    }
}