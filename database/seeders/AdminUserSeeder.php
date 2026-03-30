<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@chomin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '0812345678',
                'email_verified_at' => now(),
            ]
        );
    }
}
