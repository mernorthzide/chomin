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
            'about_content' => 'CHO.MIN เชื่อว่า “สไตล์ที่ดี เริ่มจากความเรียบง่าย” เราจึงทำเสื้อเชิ้ตที่ออกแบบได้เองตามสไตล์คุณ เลือกได้ทั้งสีกว่า 50 เฉด คอเสื้อ ปลายแขน และกระเป๋า ตัดเย็บจากผ้า Premium Japanese Cotton เนื้อนุ่ม เบา ระบายอากาศดี ทรงสวยคงรูปหลังซัก ดีไซน์ unisex ใส่ได้ทุกเพศทุกวัย ครบไซส์ XS–6XL พร้อมบริการตัดไซส์พิเศษ ใส่ได้มั่นใจตั้งแต่วันทำงานจนถึงโอกาสพิเศษ',
            'footer_quote' => 'Simple. Comfortable. Your Style.',
            'homepage_quote' => 'สไตล์ที่ดี เริ่มจากความเรียบง่าย',
            'line_chat_url' => 'https://line.me/R/ti/p/@chomin.th',
        ];
        foreach ($settings as $key => $value) {
            SiteSetting::set($key, $value);
        }
    }
}
