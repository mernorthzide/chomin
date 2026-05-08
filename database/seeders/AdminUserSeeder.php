<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');
        $phone = env('ADMIN_PHONE', '0812345678');

        if (app()->isProduction() && (blank($email) || blank($password))) {
            $this->command?->warn('Skipping admin user seeder in production: ADMIN_EMAIL and ADMIN_PASSWORD are required.');

            return;
        }

        User::firstOrCreate(
            ['email' => $email ?: 'admin@chomin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make($password ?: 'password'),
                'phone' => $phone,
                'email_verified_at' => now(),
            ]
        );
    }
}
