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
            'site_email' => 'chomin.ecommer@gmail.com',
            'site_address' => 'กรุงเทพมหานคร',
            'promptpay_id' => '0812345678',
            'promptpay_name' => 'CHOMIN CO., LTD.',
            'promptpay_qr' => null,
            'points_per_baht' => '100',
            'points_to_baht' => '10',
            'about_title' => 'Design Your Own Shirt',
            'about_content' => 'CHO.MIN ทำเชิ้ตให้เลือกได้ตามสไตล์ของคุณ ตั้งแต่สี ไซส์ คอเสื้อ ปลายแขน ไปจนถึงกระเป๋า เพื่อให้เชิ้ตตัวเดียวใส่ได้มั่นใจในทุกวัน',
            'footer_quote' => 'Simple. Comfortable. Your Style.',
            'homepage_quote' => 'Define Your Elegance In Every Movement',
            'line_chat_url' => 'https://line.me/R/ti/p/@chomin.th',
        ];
        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value);
        }
    }
}
