<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Admin POS',
                'email'    => 'admin@postoko.com',
                'role'     => 'admin',
                'password' => Hash::make('password123'),
            ],
            [
                'name'     => 'Kasir 1',
                'email'    => 'kasir1@postoko.com',
                'role'     => 'kasir',
                'password' => Hash::make('password123'),
            ],
            // User contoh sesuai modul praktikum BKPM Acara 17 & 18
            [
                'name'     => 'John Doe',
                'email'    => 'johndoe@example.com',
                'role'     => 'user',
                'password' => Hash::make('password123'),
            ],
            [
                'name'     => 'Jane Doe',
                'email'    => 'janedoe@example.com',
                'role'     => 'user',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
