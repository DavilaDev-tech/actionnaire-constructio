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
                'nom'       => 'TSAYO',
                'prenom'    => 'Davila',
                'email'     => 'admin@actionnaire.com',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'telephone' => '699000001',
                'actif'     => true,
            ],
            [
                'nom'       => 'Eyango',
                'prenom'    => 'Suzie',
                'email'     => 'vendeur@actionnaire.com',
                'password'  => Hash::make('password'),
                'role'      => 'vendeur',
                'telephone' => '699000002',
                'actif'     => true,
            ],
            [
                'nom'       => 'DAGAH',
                'prenom'    => 'Arnaud',
                'email'     => 'magasinier@actionnaire.com',
                'password'  => Hash::make('password'),
                'role'      => 'magasinier',
                'telephone' => '699000003',
                'actif'     => true,
            ],
            [
                'nom'       => 'TSAYO',
                'prenom'    => 'Elodie',
                'email'     => 'comptable@actionnaire.com',
                'password'  => Hash::make('password'),
                'role'      => 'comptable',
                'telephone' => '688890809',
                'actif'     => true,
            ],
        ];

       foreach ($users as $user) {
        // ✅ firstOrCreate évite les doublons
        User::firstOrCreate(
            ['email' => $user['email']], // cherche par email
            $user                        // crée avec ces données si pas trouvé
        );
    }
}
}