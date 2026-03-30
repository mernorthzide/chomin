<?php
namespace Database\Seeders;
use App\Models\ShippingSetting;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    public function run(): void
    {
        ShippingSetting::firstOrCreate([], [
            'shipping_fee' => 50.00,
            'free_shipping_min_amount' => 1500.00,
        ]);
    }
}
