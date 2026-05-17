<?php

use App\Models\ContentPage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Production was 404'ing on /privacy, /terms, /member, etc. because the
        // content_pages table was empty. Only seed pages that don't exist yet —
        // never overwrite content the admin may have edited via Filament.
        $pages = [
            'privacy' => ['th' => 'นโยบายความเป็นส่วนตัว', 'en' => 'Privacy Policy', 'template' => 'legal'],
            'terms' => ['th' => 'ข้อกำหนดและเงื่อนไข', 'en' => 'Terms of Use', 'template' => 'legal'],
            'shipping' => ['th' => 'นโยบายการจัดส่ง', 'en' => 'Shipping Policy', 'template' => 'policy'],
            'returns' => ['th' => 'นโยบายเปลี่ยนและคืนสินค้า', 'en' => 'Returns and Exchange', 'template' => 'policy'],
            'size-guide' => ['th' => 'คู่มือไซส์', 'en' => 'Size Guide', 'template' => 'size-guide'],
            'member' => ['th' => 'CHOMIN Member', 'en' => 'CHOMIN Member', 'template' => 'member'],
            'contact' => ['th' => 'ติดต่อเรา', 'en' => 'Contact Us', 'template' => 'form'],
            'careers' => ['th' => 'ร่วมงานกับเรา', 'en' => 'Careers', 'template' => 'form'],
            'partnerships' => ['th' => 'พาร์ตเนอร์และแคมเปญ', 'en' => 'Partnerships', 'template' => 'form'],
            'wholesale' => ['th' => 'ขายส่ง', 'en' => 'Wholesale', 'template' => 'form'],
        ];

        $thaiBody = [
            'privacy' => "CHOMIN ให้ความสำคัญกับข้อมูลส่วนบุคคลของลูกค้า ข้อมูลที่เก็บอาจรวมถึงชื่อ อีเมล เบอร์โทร ที่อยู่จัดส่ง ประวัติการสั่งซื้อ และข้อมูลการใช้งานเว็บไซต์\n\nเราใช้ข้อมูลเพื่อดำเนินคำสั่งซื้อ ให้บริการลูกค้า ปรับปรุงประสบการณ์เว็บไซต์ และสื่อสารข่าวสารเมื่อได้รับความยินยอม ลูกค้าสามารถติดต่อเราเพื่อขอเข้าถึง แก้ไข หรือลบข้อมูลได้",
            'terms' => "การใช้งานเว็บไซต์ CHOMIN ถือว่าผู้ใช้ยอมรับข้อกำหนดนี้ ราคาสินค้า โปรโมชั่น และสต็อกอาจเปลี่ยนแปลงได้ตามความเหมาะสม\n\nลูกค้าต้องให้ข้อมูลที่ถูกต้องในการสั่งซื้อ การชำระเงิน และการจัดส่ง CHOMIN ขอสงวนสิทธิ์ในการยกเลิกคำสั่งซื้อที่มีข้อมูลผิดปกติหรือไม่สามารถตรวจสอบได้",
            'shipping' => "จัดส่งฟรีทั่วประเทศตามเงื่อนไขที่ร้านกำหนด คำสั่งซื้อจะเริ่มจัดเตรียมหลังยืนยันการชำระเงินแล้ว\n\nเลขติดตามพัสดุจะแสดงในหน้าคำสั่งซื้อและอีเมลแจ้งจัดส่งเมื่อทีมงานส่งสินค้าแล้ว",
            'returns' => "สามารถแจ้งเปลี่ยนหรือคืนสินค้าได้ภายใน 30 วัน เมื่อสินค้าอยู่ในสภาพสมบูรณ์ ไม่ผ่านการใช้งาน และมีหลักฐานการสั่งซื้อ\n\nสินค้าที่ผลิตเฉพาะหรือปรับแต่งพิเศษอาจมีเงื่อนไขเพิ่มเติม",
            'size-guide' => "CHOMIN รองรับไซส์ XS ถึง 6XL\n\nคำแนะนำเบื้องต้น: วัดรอบอก ไหล่ และความยาวเสื้อจากเสื้อที่ใส่สบาย แล้วเทียบกับตารางไซส์ในหน้าสินค้า หากลังเลระหว่างสองไซส์ ให้เลือกตามทรงที่ต้องการ",
            'member' => "สมาชิก CHOMIN ได้รับแต้มสะสมจากคำสั่งซื้อที่สำเร็จ สามารถใช้แต้มเป็นส่วนลดในการสั่งซื้อครั้งถัดไป และติดตามประวัติแต้มได้ในหน้าโปรไฟล์",
            'contact' => 'ส่งข้อความถึงทีม CHOMIN ผ่านแบบฟอร์มด้านล่าง เราจะตอบกลับตามช่องทางที่ให้ไว้',
            'careers' => 'หากอยากร่วมสร้างแบรนด์แฟชั่นไทยกับ CHOMIN ส่งประวัติและความสนใจของคุณผ่านแบบฟอร์มนี้',
            'partnerships' => 'สำหรับแคมเปญ คอลแลบ หรือสื่อ โปรดส่งรายละเอียดเบื้องต้นเพื่อให้ทีมงานติดต่อกลับ',
            'wholesale' => 'สำหรับคำสั่งซื้อจำนวนมากหรือขายส่ง โปรดแจ้งจำนวน รุ่น สี และช่องทางติดต่อ',
        ];

        $englishBody = [
            'privacy' => 'CHOMIN respects customer privacy. We may collect names, email addresses, phone numbers, shipping addresses, order history, and website usage information. We use this data to process orders, support customers, improve the website, and send marketing communications only when consent is given.',
            'terms' => 'By using CHOMIN, customers accept these terms. Product prices, promotions, and availability may change. Customers are responsible for providing accurate order, payment, and shipping information.',
            'shipping' => 'Orders are prepared after payment confirmation. Tracking information appears on the order page and shipping email when available.',
            'returns' => 'Returns or exchanges can be requested within 30 days when items are unused, complete, and accompanied by proof of purchase.',
            'size-guide' => 'CHOMIN supports XS through 6XL. Measure a shirt that fits well, then compare chest, shoulder, and length with the product size chart.',
            'member' => 'CHOMIN members earn points from completed orders and can redeem points on future purchases.',
            'contact' => 'Send the CHOMIN team a message using the form below.',
            'careers' => 'If you want to help build a Thai fashion brand with CHOMIN, send your background and area of interest through this form.',
            'partnerships' => 'For campaigns, collaborations, press, or media requests, share a short brief so our team can follow up.',
            'wholesale' => 'For bulk or wholesale enquiries, share the quantity, preferred styles, colors, and contact details.',
        ];

        foreach ($pages as $slug => $meta) {
            $record = ContentPage::firstOrCreate(
                ['slug' => $slug],
                ['template' => $meta['template'], 'is_published' => true],
            );

            if (! $record->translations()->where('locale', 'th')->exists()) {
                $record->translations()->create([
                    'locale' => 'th',
                    'title' => $meta['th'],
                    'body' => $thaiBody[$slug] ?? $meta['th'],
                    'seo_title' => "{$meta['th']} | CHOMIN",
                ]);
            }

            if (! $record->translations()->where('locale', 'en')->exists()) {
                $record->translations()->create([
                    'locale' => 'en',
                    'title' => $meta['en'],
                    'body' => $englishBody[$slug] ?? $meta['en'],
                    'seo_title' => "{$meta['en']} | CHOMIN",
                ]);
            }
        }
    }

    public function down(): void
    {
        // Intentional no-op — never delete content from a destructive rollback.
    }
};
