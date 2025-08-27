<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = env('ADMIN_NAME', 'Administrador');
        $email = env('ADMIN_EMAIL', 'admin@wonn.ar');
        $password = env('ADMIN_PASSWORD', 'admin123!'); // cámbialo en .env

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'remember_token' => Str::random(10),
            ]
        );

        $this->command->info('Usuario admin preparado: ' . $user->email);
    }
}
