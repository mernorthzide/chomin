<?php

namespace Database\Seeders;

use App\Models\ContentPage;
use App\Models\FaqItem;
use App\Models\Story;
use App\Models\StoreLocation;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'privacy' => ['th' => 'นโยบายความเป็นส่วนตัว', 'en' => 'Privacy Policy', 'template' => 'legal'],
            'terms' => ['th' => 'ข้อกำหนดและเงื่อนไข', 'en' => 'Terms of Use', 'template' => 'legal'],
            'shipping' => ['th' => 'นโยบายการจัดส่ง', 'en' => 'Shipping Policy', 'template' => 'policy'],
            'returns' => ['th' => 'นโยบายเปลี่ยนและคืนสินค้า', 'en' => 'Returns and Exchange', 'template' => 'policy'],
            'size-guide' => ['th' => 'คู่มือไซส์', 'en' => 'Size Guide', 'template' => 'size-guide'],
            'member' => ['th' => 'CHOMIN Member', 'en' => 'CHOMIN Member', 'template' => 'member'],
            'gift-cards' => ['th' => 'บัตรของขวัญ', 'en' => 'Gift Cards', 'template' => 'gift-cards'],
            'contact' => ['th' => 'ติดต่อเรา', 'en' => 'Contact Us', 'template' => 'form'],
            'careers' => ['th' => 'ร่วมงานกับเรา', 'en' => 'Careers', 'template' => 'form'],
            'partnerships' => ['th' => 'พาร์ตเนอร์และแคมเปญ', 'en' => 'Partnerships', 'template' => 'form'],
            'wholesale' => ['th' => 'ขายส่ง', 'en' => 'Wholesale', 'template' => 'form'],
        ];

        foreach ($pages as $slug => $page) {
            $record = ContentPage::updateOrCreate(
                ['slug' => $slug],
                ['template' => $page['template'], 'is_published' => true],
            );

            $record->translations()->updateOrCreate(['locale' => 'th'], [
                'title' => $page['th'],
                'excerpt' => 'เนื้อหานี้สามารถแก้ไขได้จาก Filament admin',
                'body' => $this->thaiBody($page['th'], $slug),
                'seo_title' => "{$page['th']} | CHOMIN",
                'seo_description' => "ข้อมูล{$page['th']}ของ CHOMIN",
            ]);

            $record->translations()->updateOrCreate(['locale' => 'en'], [
                'title' => $page['en'],
                'excerpt' => 'This content is editable in Filament admin.',
                'body' => $this->englishBody($page['en'], $slug),
                'seo_title' => "{$page['en']} | CHOMIN",
                'seo_description' => "CHOMIN {$page['en']} information.",
            ]);
        }

        $faq = FaqItem::updateOrCreate(['category' => 'orders', 'sort_order' => 1], ['is_published' => true]);
        $faq->translations()->updateOrCreate(['locale' => 'th'], [
            'question' => 'จัดส่งใช้เวลากี่วัน',
            'answer' => 'โดยทั่วไปจัดส่งภายใน 1-3 วันทำการหลังยืนยันการชำระเงิน',
        ]);
        $faq->translations()->updateOrCreate(['locale' => 'en'], [
            'question' => 'How long does shipping take?',
            'answer' => 'Orders usually ship within 1-3 business days after payment confirmation.',
        ]);

        $story = Story::updateOrCreate(['slug' => 'how-to-style-shirts'], [
            'is_published' => true,
            'published_at' => now(),
            'sort_order' => 1,
        ]);
        $story->translations()->updateOrCreate(['locale' => 'th'], [
            'title' => 'วิธีเลือกเชิ้ตให้เข้ากับทุกวัน',
            'excerpt' => 'ไอเดีย styling สำหรับเชิ้ต CHOMIN',
            'body' => "เชิ้ตที่ดีควรทำงานได้หลายบทบาทในตู้เสื้อผ้า\n\nเลือกสีที่เข้ากับอารมณ์ของวัน แล้วจับคู่กับกางเกงหรือกระโปรงทรงเรียบเพื่อให้สีของเชิ้ตเป็นตัวเล่าเรื่อง",
        ]);
        $story->translations()->updateOrCreate(['locale' => 'en'], [
            'title' => 'How to style shirts for every day',
            'excerpt' => 'Styling ideas for CHOMIN shirts.',
            'body' => "A good shirt should work hard across your wardrobe.\n\nChoose the color that matches the day, then pair it with simple silhouettes.",
        ]);

        $location = StoreLocation::updateOrCreate(['sort_order' => 1], [
            'phone' => '0812345678',
            'email' => 'contact@chomin.com',
            'is_active' => true,
        ]);
        $location->translations()->updateOrCreate(['locale' => 'th'], [
            'name' => 'CHOMIN Online Studio',
            'address' => 'กรุงเทพมหานคร',
            'hours' => 'จันทร์-ศุกร์ 10:00-18:00',
        ]);
        $location->translations()->updateOrCreate(['locale' => 'en'], [
            'name' => 'CHOMIN Online Studio',
            'address' => 'Bangkok, Thailand',
            'hours' => 'Monday-Friday 10:00-18:00',
        ]);
    }

    private function thaiBody(string $title, string $slug): string
    {
        return match ($slug) {
            'privacy' => "CHOMIN ให้ความสำคัญกับข้อมูลส่วนบุคคลของลูกค้า ข้อมูลที่เก็บอาจรวมถึงชื่อ อีเมล เบอร์โทร ที่อยู่จัดส่ง ประวัติการสั่งซื้อ และข้อมูลการใช้งานเว็บไซต์\n\nเราใช้ข้อมูลเพื่อดำเนินคำสั่งซื้อ ให้บริการลูกค้า ปรับปรุงประสบการณ์เว็บไซต์ และสื่อสารข่าวสารเมื่อได้รับความยินยอม ลูกค้าสามารถติดต่อเราเพื่อขอเข้าถึง แก้ไข หรือลบข้อมูลได้\n\nข้อความนี้เป็นฉบับเริ่มต้นและควรได้รับการตรวจทานทางกฎหมายก่อนเปิดใช้งานจริง",
            'terms' => "การใช้งานเว็บไซต์ CHOMIN ถือว่าผู้ใช้ยอมรับข้อกำหนดนี้ ราคาสินค้า โปรโมชั่น และสต็อกอาจเปลี่ยนแปลงได้ตามความเหมาะสม\n\nลูกค้าต้องให้ข้อมูลที่ถูกต้องในการสั่งซื้อ การชำระเงิน และการจัดส่ง CHOMIN ขอสงวนสิทธิ์ในการยกเลิกคำสั่งซื้อที่มีข้อมูลผิดปกติหรือไม่สามารถตรวจสอบได้",
            'shipping' => "จัดส่งฟรีทั่วประเทศตามเงื่อนไขที่ร้านกำหนด คำสั่งซื้อจะเริ่มจัดเตรียมหลังยืนยันการชำระเงินแล้ว\n\nเลขติดตามพัสดุจะแสดงในหน้าคำสั่งซื้อและอีเมลแจ้งจัดส่งเมื่อทีมงานส่งสินค้าแล้ว",
            'returns' => "สามารถแจ้งเปลี่ยนหรือคืนสินค้าได้ภายใน 30 วัน เมื่อสินค้าอยู่ในสภาพสมบูรณ์ ไม่ผ่านการใช้งาน และมีหลักฐานการสั่งซื้อ\n\nสินค้าที่ผลิตเฉพาะหรือปรับแต่งพิเศษอาจมีเงื่อนไขเพิ่มเติม",
            'size-guide' => "CHOMIN รองรับไซส์ XS ถึง 6XL\n\nคำแนะนำเบื้องต้น: วัดรอบอก ไหล่ และความยาวเสื้อจากเสื้อที่ใส่สบาย แล้วเทียบกับตารางไซส์ในหน้าสินค้า หากลังเลระหว่างสองไซส์ ให้เลือกตามทรงที่ต้องการ",
            'member' => "สมาชิก CHOMIN ได้รับแต้มสะสมจากคำสั่งซื้อที่สำเร็จ สามารถใช้แต้มเป็นส่วนลดในการสั่งซื้อครั้งถัดไป และติดตามประวัติแต้มได้ในหน้าโปรไฟล์",
            'gift-cards' => "บัตรของขวัญ CHOMIN ออกโดยทีมงานและใช้เป็นยอดคงเหลือใน checkout ได้ รหัสสามารถใช้บางส่วนจนกว่ายอดคงเหลือจะหมด",
            'contact' => "ส่งข้อความถึงทีม CHOMIN ผ่านแบบฟอร์มด้านล่าง เราจะตอบกลับตามช่องทางที่ให้ไว้",
            'careers' => "หากอยากร่วมสร้างแบรนด์แฟชั่นไทยกับ CHOMIN ส่งประวัติและความสนใจของคุณผ่านแบบฟอร์มนี้",
            'partnerships' => "สำหรับแคมเปญ คอลแลบ หรือสื่อ โปรดส่งรายละเอียดเบื้องต้นเพื่อให้ทีมงานติดต่อกลับ",
            'wholesale' => "สำหรับคำสั่งซื้อจำนวนมากหรือขายส่ง โปรดแจ้งจำนวน รุ่น สี และช่องทางติดต่อ",
            default => "{$title}\n\nเนื้อหานี้แก้ไขได้จากหลังบ้าน",
        };
    }

    private function englishBody(string $title, string $slug): string
    {
        return match ($slug) {
            'privacy' => "CHOMIN respects customer privacy. We may collect names, email addresses, phone numbers, shipping addresses, order history, and website usage information.\n\nWe use this data to process orders, support customers, improve the website, and send marketing communications only when consent is given. This starter text should be reviewed before production use.",
            'terms' => "By using CHOMIN, customers accept these terms. Product prices, promotions, and availability may change.\n\nCustomers are responsible for providing accurate order, payment, and shipping information.",
            'shipping' => "Orders are prepared after payment confirmation. Tracking information appears on the order page and shipping email when available.",
            'returns' => "Returns or exchanges can be requested within 30 days when items are unused, complete, and accompanied by proof of purchase.",
            'size-guide' => "CHOMIN supports XS through 6XL. Measure a shirt that fits well, then compare chest, shoulder, and length with the product size chart.",
            'member' => "CHOMIN members earn points from completed orders and can redeem points on future purchases.",
            'gift-cards' => "CHOMIN gift cards are issued by the team and can be redeemed at checkout until the stored balance is used.",
            default => "{$title}\n\nThis content is editable in the admin panel.",
        };
    }
}
