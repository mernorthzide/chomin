<?php
namespace Database\Seeders;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'CHOMIN',
            'site_phone' => '02-xxx-xxxx',
            'site_email' => 'contact@chomin.com',
            'site_address' => 'กรุงเทพมหานคร',
            'promptpay_id' => '0812345678',
            'promptpay_name' => 'CHOMIN CO., LTD.',
            'promptpay_qr' => null,
            'points_per_baht' => '100',
            'points_to_baht' => '10',
            'about_content' => 'CHOMIN — แฟชั่นที่เหนือกาลเวลา',
            'footer_quote' => '"สไตล์ไม่ใช่การอวดอ้างความมั่งคั่ง แต่เป็นการแสดงออกถึงอัตลักษณ์ทางสถาปัตยกรรมที่กล้าหาญกล้า"',
        ];
        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
