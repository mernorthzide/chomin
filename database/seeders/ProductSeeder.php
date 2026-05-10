<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $productLines = $this->productLines();
            $activeSlugs = array_column($productLines, 'slug');
            $activeCollectionSlugs = array_map(fn (array $line): string => $line['collection']['slug'], $productLines);

            Collection::query()
                ->whereNotIn('slug', $activeCollectionSlugs)
                ->update(['is_active' => false]);

            $collections = [];

            foreach ($productLines as $line) {
                $collectionData = $line['collection'];
                $collection = Collection::updateOrCreate(
                    ['slug' => $collectionData['slug']],
                    [
                        'name' => $collectionData['name'],
                        'description' => $collectionData['description'],
                        'image' => $collectionData['image'],
                        'banner_image' => $collectionData['banner_image'],
                        'layout_type' => 'side-hero',
                        'is_active' => true,
                        'sort_order' => $line['sort_order'],
                    ],
                );
                $collection->translations()->updateOrCreate(['locale' => 'th'], [
                    'name' => $collectionData['name'],
                    'description' => $collectionData['description_th'],
                ]);
                $collection->translations()->updateOrCreate(['locale' => 'en'], [
                    'name' => $collectionData['name'],
                    'description' => $collectionData['description_en'],
                ]);

                $collections[$collectionData['slug']] = $collection;
            }

            $category = Category::updateOrCreate(
                ['slug' => 'shirt'],
                [
                    'name' => 'เสื้อเชิ้ต',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
            );
            $category->translations()->updateOrCreate(['locale' => 'th'], ['name' => 'เสื้อเชิ้ต']);
            $category->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Shirts']);

            $colors = $this->colors();
            $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'];

            Product::query()
                ->whereNotIn('slug', $activeSlugs)
                ->update(['is_active' => false, 'is_featured' => false]);

            foreach ($productLines as $line) {
                $product = Product::updateOrCreate(
                    ['slug' => $line['slug']],
                    [
                        'name' => $line['name'],
                        'description' => $line['description'],
                        'price' => 1790.00,
                        'sale_price' => 999.00,
                        'sale_starts_at' => null,
                        'sale_ends_at' => null,
                        'collection_id' => $collections[$line['collection']['slug']]->id,
                        'category_id' => $category->id,
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => $line['sort_order'],
                    ],
                );
                $product->translations()->updateOrCreate(['locale' => 'th'], $line['translations']['th']);
                $product->translations()->updateOrCreate(['locale' => 'en'], $line['translations']['en']);

                ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);

                $lineColors = $this->colorsForLine($colors, $line['color_codes']);
                $firstColorId = null;

                foreach ($lineColors as $index => $color) {
                    $productColor = ProductColor::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'color_code' => $color['hex'],
                        ],
                        [
                            'name' => $color['name'],
                            'slug' => null,
                            'sort_order' => $index + 1,
                        ],
                    );
                    $productColor->translations()->updateOrCreate(['locale' => 'th'], ['name' => $color['name']]);
                    $productColor->translations()->updateOrCreate(['locale' => 'en'], ['name' => $color['name']]);

                    $firstColorId ??= $productColor->id;

                    ProductImage::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'product_color_id' => $productColor->id,
                        ],
                        [
                            'image_path' => "products/colors/{$color['code']}.png",
                            'is_primary' => false,
                            'sort_order' => 100 + $index,
                        ],
                    );

                    foreach ($sizes as $size) {
                        ProductVariant::updateOrCreate(
                            ['sku' => "{$line['sku_prefix']}-{$color['code']}-{$size}"],
                            [
                                'product_id' => $product->id,
                                'product_color_id' => $productColor->id,
                                'size' => $size,
                                'stock' => 100,
                            ],
                        );
                    }
                }

                foreach ($line['gallery'] as $index => $imagePath) {
                    ProductImage::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'image_path' => $imagePath,
                        ],
                        [
                            'product_color_id' => $firstColorId,
                            'is_primary' => $index === 0,
                            'sort_order' => $index,
                        ],
                    );
                }

                ProductImage::query()
                    ->where('product_id', $product->id)
                    ->where('image_path', 'not like', 'products/colors/%')
                    ->whereNotIn('image_path', $line['gallery'])
                    ->delete();
            }

            $this->command?->info('ProductSeeder complete: 5 active custom shirt lines with Imagen-enhanced campaign styling.');
        });
    }

    private function productLines(): array
    {
        return [
            [
                'slug' => 'cm-classic-custom-shirt',
                'name' => 'CM Classic Custom Shirt',
                'sku_prefix' => 'CHO-CM',
                'sort_order' => 1,
                'color_codes' => 'all',
                'collection' => [
                    'slug' => 'cm-classic',
                    'name' => 'CM Classic',
                    'description' => 'Design your own everyday shirt line with 50 colors, inclusive sizing, and personal details.',
                    'description_th' => 'เชิ้ตสั่งเลือกได้สำหรับทุกวัน เลือกสี ไซส์ คอเสื้อ ปลายแขน และกระเป๋าให้เข้ากับตัวคุณ',
                    'description_en' => 'Design your own everyday shirt with 50 colors, inclusive sizing, and personal details.',
                    'image' => 'products/chomin-imagen/cm-classic.jpg',
                    'banner_image' => 'collections/banners/cm-classic-imagen-hero.jpg',
                ],
                'description' => "Design Your Own Shirt\n\nเลือกสีได้ 50+ สี ไซส์ XS-6XL และปรับรายละเอียดคอเสื้อ ปลายแขน กระเป๋า ให้เข้ากับการใช้งานจริง\n\nSimple. Comfortable. Your Style.",
                'gallery' => [
                    'products/chomin-imagen/cm-classic.jpg',
                    'products/chomin-imagen/custom-details.jpg',
                    'products/chomin-imagen/duo-box.jpg',
                    'products/chomin-imagen/care-studio.jpg',
                    'products/chomin-imagen/lifestyle-editorial.jpg',
                ],
                'translations' => [
                    'th' => [
                        'name' => 'CM Classic Custom Shirt',
                        'description' => "Design Your Own Shirt\n\nเชิ้ต CHO.MIN รุ่นหลักที่เลือกได้ครบ: 50+ สี, ไซส์ XS-6XL, คอเสื้อ, ปลายแขน และกระเป๋า\n\nโปรพิเศษ 999 บาท และ DuoDeal 2 ตัว 1,850 บาท สำหรับวันที่อยากเริ่มจากเชิ้ตดี ๆ สักตัว",
                        'seo_title' => 'CM Classic Custom Shirt',
                        'seo_description' => 'CHO.MIN เสื้อเชิ้ตเลือกสี ไซส์ คอเสื้อ ปลายแขน และกระเป๋าได้ โปรพิเศษ 999 บาท',
                    ],
                    'en' => [
                        'name' => 'CM Classic Custom Shirt',
                        'description' => "Design Your Own Shirt\n\nChoose from 50+ colors, XS-6XL sizing, collar, cuff, and pocket details.\n\nSimple. Comfortable. Your Style.",
                        'seo_title' => 'CM Classic Custom Shirt',
                        'seo_description' => 'CHO.MIN custom shirt with 50+ colors, XS-6XL sizing, collar, cuff, and pocket options.',
                    ],
                ],
            ],
            [
                'slug' => 'cm-workday-shirt',
                'name' => 'CM Workday Shirt',
                'sku_prefix' => 'CHO-WD',
                'sort_order' => 2,
                'color_codes' => ['7', '21', '26', '33', '39', '40', '48', '55', '61', '62', '11022', '11023', '11025', '11044', '11046', '11047', '11048', '11051'],
                'collection' => [
                    'slug' => 'cm-workday',
                    'name' => 'CM Workday',
                    'description' => 'Quiet neutral and deep-tone shirts for office days.',
                    'description_th' => 'โทนสุภาพสำหรับวันทำงาน สี neutral และ deep tone ที่แต่งง่าย ใส่ได้บ่อย',
                    'description_en' => 'Quiet neutral and deep-tone shirts for office days.',
                    'image' => 'products/chomin-imagen/cm-workday.jpg',
                    'banner_image' => 'products/chomin-imagen/cm-workday.jpg',
                ],
                'description' => "Workday ready shirt\n\nเชิ้ตโทนสุภาพสำหรับวันทำงาน เลือกสี neutral และ deep tone ที่แมตช์กับกางเกงหรือสูทได้ง่าย พร้อมปรับคอเสื้อ ปลายแขน และกระเป๋าได้",
                'gallery' => [
                    'products/chomin-imagen/cm-workday.jpg',
                    'products/chomin-imagen/custom-details.jpg',
                    'products/chomin-imagen/care-studio.jpg',
                ],
                'translations' => [
                    'th' => [
                        'name' => 'CM Workday Shirt',
                        'description' => "Workday ready shirt\n\nเชิ้ตโทนสุภาพสำหรับวันทำงาน เลือกสี neutral และ deep tone ที่แมตช์ง่าย ใส่สบายทั้งวัน พร้อมปรับรายละเอียดให้เข้ากับออฟฟิศของคุณ",
                        'seo_title' => 'CM Workday Shirt',
                        'seo_description' => 'CHO.MIN เชิ้ตทำงานผู้หญิง โทนสุภาพ ปรับคอเสื้อ ปลายแขน และกระเป๋าได้ ราคาโปร 999 บาท',
                    ],
                    'en' => [
                        'name' => 'CM Workday Shirt',
                        'description' => "Workday ready shirt\n\nQuiet neutral and deep-tone shirts for office days, with adjustable collar, cuff, and pocket details.",
                        'seo_title' => 'CM Workday Shirt',
                        'seo_description' => 'CHO.MIN workday shirt in neutral tones with custom collar, cuff, and pocket options.',
                    ],
                ],
            ],
            [
                'slug' => 'cm-soft-pastel-shirt',
                'name' => 'CM Soft Pastel Shirt',
                'sku_prefix' => 'CHO-PA',
                'sort_order' => 3,
                'color_codes' => ['65', '73', '11002', '11003', '11004', '11007', '11009', '11010', '11011', '11012', '11013', '11014', '11022', '11023', '11024'],
                'collection' => [
                    'slug' => 'cm-soft-pastel',
                    'name' => 'CM Soft Pastel',
                    'description' => 'Soft pastel shirts for lighter everyday styling.',
                    'description_th' => 'เชิ้ตสีอ่อนละมุนสำหรับวันที่อยากให้ลุคดูสบายและแต่งง่าย',
                    'description_en' => 'Soft pastel shirts for lighter everyday styling.',
                    'image' => 'products/chomin-imagen/cm-soft-pastel.jpg',
                    'banner_image' => 'products/chomin-imagen/cm-soft-pastel.jpg',
                ],
                'description' => "Soft pastel mood\n\nเชิ้ตสีอ่อนที่ให้ภาพลักษณ์นุ่ม สบาย และแต่งง่าย เหมาะกับวันสบาย ๆ หรือวันที่อยากให้ลุคดูละมุนขึ้น",
                'gallery' => [
                    'products/chomin-imagen/cm-soft-pastel.jpg',
                    'products/chomin-imagen/duo-box.jpg',
                    'products/chomin-imagen/care-studio.jpg',
                ],
                'translations' => [
                    'th' => [
                        'name' => 'CM Soft Pastel Shirt',
                        'description' => "Soft pastel mood\n\nเชิ้ตสีอ่อนที่ให้ภาพลักษณ์นุ่ม สบาย และแต่งง่าย เลือกคอเสื้อ ปลายแขน และกระเป๋าให้เป็นลุคประจำวันที่ละมุนขึ้น",
                        'seo_title' => 'CM Soft Pastel Shirt',
                        'seo_description' => 'CHO.MIN เชิ้ตสีพาสเทล สีอ่อน ใส่สบาย ปรับรายละเอียดได้ ราคาโปร 999 บาท',
                    ],
                    'en' => [
                        'name' => 'CM Soft Pastel Shirt',
                        'description' => "Soft pastel mood\n\nEasy pastel shirts for softer everyday styling, with adjustable collar, cuff, and pocket details.",
                        'seo_title' => 'CM Soft Pastel Shirt',
                        'seo_description' => 'CHO.MIN pastel custom shirt with soft colors and adjustable details.',
                    ],
                ],
            ],
            [
                'slug' => 'cm-statement-color-shirt',
                'name' => 'CM Statement Color Shirt',
                'sku_prefix' => 'CHO-ST',
                'sort_order' => 4,
                'color_codes' => ['13', '43', '55', '73', '79', '81', '88', '11010', '11015', '11021', '11031', '11033', '11036', '11042'],
                'collection' => [
                    'slug' => 'cm-statement-color',
                    'name' => 'CM Statement Color',
                    'description' => 'Campaign colors for expressive shirt styling.',
                    'description_th' => 'เชิ้ตสีชัดสำหรับวันที่อยากให้ลุคเด่นขึ้น แต่ยังคุมความเนี้ยบแบบ CHOMIN',
                    'description_en' => 'Campaign colors for expressive shirt styling.',
                    'image' => 'products/chomin-imagen/cm-statement-color.jpg',
                    'banner_image' => 'products/chomin-imagen/lifestyle-editorial.jpg',
                ],
                'description' => "Make Your Own Style\n\nเชิ้ตสีชัดสำหรับวันที่อยากให้ลุคเด่นขึ้น เลือกสี campaign จาก CHO.MIN แล้วปรับรายละเอียดให้เข้ากับสไตล์ของคุณ",
                'gallery' => [
                    'products/chomin-imagen/cm-statement-color.jpg',
                    'products/chomin-imagen/lifestyle-editorial.jpg',
                    'products/chomin-imagen/cm-classic.jpg',
                ],
                'translations' => [
                    'th' => [
                        'name' => 'CM Statement Color Shirt',
                        'description' => "Make Your Own Style\n\nเชิ้ตสีชัดสำหรับวันที่อยากให้ลุคเด่นขึ้น เลือกสี campaign จาก CHO.MIN แล้วปรับคอเสื้อ ปลายแขน และกระเป๋าให้เป็นของคุณ",
                        'seo_title' => 'CM Statement Color Shirt',
                        'seo_description' => 'CHO.MIN เชิ้ตสีสด สีเด่น ปรับดีเทลได้ ราคาโปร 999 บาท',
                    ],
                    'en' => [
                        'name' => 'CM Statement Color Shirt',
                        'description' => "Make Your Own Style\n\nStronger campaign colors for expressive styling, with adjustable collar, cuff, and pocket details.",
                        'seo_title' => 'CM Statement Color Shirt',
                        'seo_description' => 'CHO.MIN statement color custom shirt with bold campaign shades.',
                    ],
                ],
            ],
            [
                'slug' => 'cm-mandarin-minimal-shirt',
                'name' => 'CM Mandarin Minimal Shirt',
                'sku_prefix' => 'CHO-MN',
                'sort_order' => 5,
                'color_codes' => ['7', '48', '61', '62', '11016', '11023', '11025', '11027', '11031', '11044', '11045', '11049', '11051', '11055'],
                'collection' => [
                    'slug' => 'cm-mandarin-minimal',
                    'name' => 'CM Mandarin Minimal',
                    'description' => 'Clean Mandarin-collar starting points for minimal everyday looks.',
                    'description_th' => 'ลุคมินิมอลจากคอ Mandarin ที่ดูสะอาด เนี้ยบ และต่างจากเชิ้ตทำงานทั่วไป',
                    'description_en' => 'Clean Mandarin-collar starting points for minimal everyday looks.',
                    'image' => 'products/chomin-imagen/cm-mandarin-minimal.jpg',
                    'banner_image' => 'products/chomin-imagen/cm-mandarin-minimal.jpg',
                ],
                'description' => "Define Your Elegance In Every Movement\n\nเชิ้ตลุคมินิมอลที่ตั้งต้นจากคอ Mandarin ให้ภาพรวมสะอาด เนี้ยบ และต่างจากเชิ้ตทำงานทั่วไป",
                'gallery' => [
                    'products/chomin-imagen/cm-mandarin-minimal.jpg',
                    'products/chomin-imagen/custom-details.jpg',
                    'products/chomin-imagen/care-studio.jpg',
                ],
                'translations' => [
                    'th' => [
                        'name' => 'CM Mandarin Minimal Shirt',
                        'description' => "Define Your Elegance In Every Movement\n\nเชิ้ตลุคมินิมอลที่ตั้งต้นจากคอ Mandarin ให้ภาพรวมสะอาด เนี้ยบ และต่างจากเชิ้ตทำงานทั่วไป ยังปรับปลายแขนและกระเป๋าได้ตามต้องการ",
                        'seo_title' => 'CM Mandarin Minimal Shirt',
                        'seo_description' => 'CHO.MIN เชิ้ตคอ Mandarin มินิมอล ปรับรายละเอียดได้ ราคาโปร 999 บาท',
                    ],
                    'en' => [
                        'name' => 'CM Mandarin Minimal Shirt',
                        'description' => "Define Your Elegance In Every Movement\n\nA minimal Mandarin-collar starting point with clean, refined everyday styling.",
                        'seo_title' => 'CM Mandarin Minimal Shirt',
                        'seo_description' => 'CHO.MIN minimal Mandarin collar custom shirt with adjustable details.',
                    ],
                ],
            ],
        ];
    }

    private function colorsForLine(array $colors, array|string $codes): array
    {
        if ($codes === 'all') {
            return $colors;
        }

        return array_values(array_filter($colors, fn (array $color): bool => in_array($color['code'], $codes, true)));
    }

    private function colors(): array
    {
        return [
            ['code' => '7', 'name' => 'Warm Light Greige', 'hex' => '#D8D5C5'],
            ['code' => '13', 'name' => 'Vivid Golden Yellow', 'hex' => '#F5D300'],
            ['code' => '21', 'name' => 'Light Khaki', 'hex' => '#D9D2B4'],
            ['code' => '26', 'name' => 'Olive Green', 'hex' => '#6F6A47'],
            ['code' => '33', 'name' => 'Deep Teal Green', 'hex' => '#24524F'],
            ['code' => '39', 'name' => 'Muted Slate Blue', 'hex' => '#505E84'],
            ['code' => '40', 'name' => 'Deep Indigo', 'hex' => '#3C3F5A'],
            ['code' => '42', 'name' => 'Soft Lavender Gray', 'hex' => '#C7C2D8'],
            ['code' => '43', 'name' => 'Dusty Violet', 'hex' => '#7C72A3'],
            ['code' => '48', 'name' => 'Near Black Navy', 'hex' => '#070A12'],
            ['code' => '55', 'name' => 'Muted Teal', 'hex' => '#3F8C84'],
            ['code' => '60', 'name' => 'Charcoal Plum', 'hex' => '#564D57'],
            ['code' => '61', 'name' => 'Dark Graphite', 'hex' => '#3E404A'],
            ['code' => '62', 'name' => 'Deep Navy Gray', 'hex' => '#243447'],
            ['code' => '65', 'name' => 'Blush Taupe', 'hex' => '#DCC9CF'],
            ['code' => '73', 'name' => 'Soft Coral Pink', 'hex' => '#D9817E'],
            ['code' => '79', 'name' => 'Burnt Orange', 'hex' => '#EB6529'],
            ['code' => '81', 'name' => 'Vivid Red', 'hex' => '#C81D14'],
            ['code' => '88', 'name' => 'Deep Crimson', 'hex' => '#8F1620'],
            ['code' => '11002', 'name' => 'Floral Blush', 'hex' => '#FBECE8'],
            ['code' => '11003', 'name' => 'Pastel Aqua', 'hex' => '#C2E2EB'],
            ['code' => '11004', 'name' => 'Baby Powder', 'hex' => '#E1E8F9'],
            ['code' => '11007', 'name' => 'Creamy Sun', 'hex' => '#FFF7D0'],
            ['code' => '11009', 'name' => 'Light Baby Blue', 'hex' => '#D8EFFF'],
            ['code' => '11010', 'name' => 'Sky Blue', 'hex' => '#8CC6F8'],
            ['code' => '11011', 'name' => 'Pink Breeze', 'hex' => '#F2C3E6'],
            ['code' => '11012', 'name' => 'Lilac Petal', 'hex' => '#E6C4EB'],
            ['code' => '11013', 'name' => 'White Lavender', 'hex' => '#E2D9F4'],
            ['code' => '11014', 'name' => 'Cool Light Blue', 'hex' => '#A4BEF0'],
            ['code' => '11015', 'name' => 'Deep Grape', 'hex' => '#4F3C6F'],
            ['code' => '11016', 'name' => 'Dark Cocoa Brown', 'hex' => '#3E3342'],
            ['code' => '11021', 'name' => 'Fuchsia', 'hex' => '#B832A6'],
            ['code' => '11022', 'name' => 'Calm Soft Sand', 'hex' => '#E8E6D2'],
            ['code' => '11023', 'name' => 'Off White', 'hex' => '#F7F6EF'],
            ['code' => '11024', 'name' => 'Creamy Shell', 'hex' => '#F7E1B5'],
            ['code' => '11025', 'name' => 'Mocha Brown', 'hex' => '#6D4E3F'],
            ['code' => '11026', 'name' => 'Coral Rose', 'hex' => '#EB9C8E'],
            ['code' => '11027', 'name' => 'Olive Verde', 'hex' => '#5E633D'],
            ['code' => '11031', 'name' => 'Burgundy', 'hex' => '#602E35'],
            ['code' => '11033', 'name' => 'Aqua Sky', 'hex' => '#67C1CF'],
            ['code' => '11036', 'name' => 'Royal Azure', 'hex' => '#2F5D9F'],
            ['code' => '11042', 'name' => 'Deep Sapphire', 'hex' => '#315DA8'],
            ['code' => '11044', 'name' => 'Soft Silver Mist', 'hex' => '#D9D9D9'],
            ['code' => '11045', 'name' => 'Cool Lavender Grey', 'hex' => '#B7B7C2'],
            ['code' => '11046', 'name' => 'Steel Grey', 'hex' => '#9EA3AD'],
            ['code' => '11047', 'name' => 'Slate Grey', 'hex' => '#7C818C'],
            ['code' => '11048', 'name' => 'Graphite Grey', 'hex' => '#6B707A'],
            ['code' => '11049', 'name' => 'Espresso Brown', 'hex' => '#3B3432'],
            ['code' => '11051', 'name' => 'Midnight Navy', 'hex' => '#2B2F4A'],
            ['code' => '11055', 'name' => 'Charcoal Navy', 'hex' => '#2F334F'],
        ];
    }
}
