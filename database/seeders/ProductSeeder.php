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
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Data Arrays ───────────────────────────────────────────────

        $collars = [
            'FR' => 'French Collar',
            'IT' => 'Italian Collar',
            'MD' => 'Mandarin Collar',
            'BD' => 'Button Down',
            'BC' => 'Band Collar',
        ];

        $cuffs = [
            'C1' => '1 Button',
            'C2' => '2 Button',
            'CF' => 'French Cuff',
        ];

        $pockets = [
            'NP' => 'No Pocket',
            'YP' => 'Yes Pocket',
        ];

        $colors = [
            // Group 1: factory codes 7-88
            ['code' => '7',     'abbr' => 'WLG', 'name' => 'Warm Light Greige',  'hex' => '#D8D5C5'],
            ['code' => '13',    'abbr' => 'VGY', 'name' => 'Vivid Golden Yellow','hex' => '#F5D300'],
            ['code' => '21',    'abbr' => 'LKH', 'name' => 'Light Khaki',        'hex' => '#D9D2B4'],
            ['code' => '26',    'abbr' => 'OLG', 'name' => 'Olive Green',        'hex' => '#6F6A47'],
            ['code' => '33',    'abbr' => 'DTG', 'name' => 'Deep Teal Green',    'hex' => '#24524F'],
            ['code' => '39',    'abbr' => 'MSB', 'name' => 'Muted Slate Blue',   'hex' => '#505E84'],
            ['code' => '40',    'abbr' => 'DIN', 'name' => 'Deep Indigo',        'hex' => '#3C3F5A'],
            ['code' => '42',    'abbr' => 'SLG', 'name' => 'Soft Lavender Gray', 'hex' => '#C7C2D8'],
            ['code' => '43',    'abbr' => 'DVI', 'name' => 'Dusty Violet',       'hex' => '#7C72A3'],
            ['code' => '48',    'abbr' => 'NBN', 'name' => 'Near Black Navy',    'hex' => '#070A12'],
            ['code' => '55',    'abbr' => 'MTL', 'name' => 'Muted Teal',         'hex' => '#3F8C84'],
            ['code' => '60',    'abbr' => 'CHP', 'name' => 'Charcoal Plum',      'hex' => '#564D57'],
            ['code' => '61',    'abbr' => 'DGR', 'name' => 'Dark Graphite',      'hex' => '#3E404A'],
            ['code' => '62',    'abbr' => 'DNG', 'name' => 'Deep Navy Gray',     'hex' => '#243447'],
            ['code' => '65',    'abbr' => 'BLT', 'name' => 'Blush Taupe',        'hex' => '#DCC9CF'],
            ['code' => '73',    'abbr' => 'SCP', 'name' => 'Soft Coral Pink',    'hex' => '#D9817E'],
            ['code' => '79',    'abbr' => 'BOR', 'name' => 'Burnt Orange',       'hex' => '#EB6529'],
            ['code' => '81',    'abbr' => 'VRE', 'name' => 'Vivid Red',          'hex' => '#C81D14'],
            ['code' => '88',    'abbr' => 'DCR', 'name' => 'Deep Crimson',       'hex' => '#8F1620'],
            // Group 2: factory codes 11002-11055
            ['code' => '11002', 'abbr' => 'FBL',  'name' => 'Floral Blush',         'hex' => '#FBECE8'],
            ['code' => '11003', 'abbr' => 'PAC',  'name' => 'Pastel Aqua',          'hex' => '#C2E2EB'],
            ['code' => '11004', 'abbr' => 'BPW',  'name' => 'Baby Powder',          'hex' => '#E1E8F9'],
            ['code' => '11007', 'abbr' => 'CFS',  'name' => 'Creamy Sun',           'hex' => '#FFF7D0'],
            ['code' => '11009', 'abbr' => 'LBL',  'name' => 'Light Baby Blue',      'hex' => '#D8EFFF'],
            ['code' => '11010', 'abbr' => 'SKY',  'name' => 'Sky Blue',             'hex' => '#8CC6F8'],
            ['code' => '11011', 'abbr' => 'PBZ',  'name' => 'Pink Breeze',          'hex' => '#F2C3E6'],
            ['code' => '11012', 'abbr' => 'LPL',  'name' => 'Lilac Petal',          'hex' => '#E6C4EB'],
            ['code' => '11013', 'abbr' => 'WLV',  'name' => 'White Lavender',       'hex' => '#E2D9F4'],
            ['code' => '11014', 'abbr' => 'CLB',  'name' => 'Cool Light Blue',      'hex' => '#A4BEF0'],
            ['code' => '11015', 'abbr' => 'DGP',  'name' => 'Deep Grape',           'hex' => '#4F3C6F'],
            ['code' => '11016', 'abbr' => 'DCB',  'name' => 'Dark Cocoa Brown',     'hex' => '#3E3342'],
            ['code' => '11021', 'abbr' => 'FUC',  'name' => 'Fuchsia',              'hex' => '#B832A6'],
            ['code' => '11022', 'abbr' => 'CFS2', 'name' => 'Calm Soft Sand',       'hex' => '#E8E6D2'],
            ['code' => '11023', 'abbr' => 'OFF',  'name' => 'Off White',            'hex' => '#F7F6EF'],
            ['code' => '11024', 'abbr' => 'CSH',  'name' => 'Creamy Shell',         'hex' => '#F7E1B5'],
            ['code' => '11025', 'abbr' => 'MCB',  'name' => 'Mocha Brown',          'hex' => '#6D4E3F'],
            ['code' => '11026', 'abbr' => 'CRL',  'name' => 'Coral Rose',           'hex' => '#EB9C8E'],
            ['code' => '11027', 'abbr' => 'OLV',  'name' => 'Olive Verde',          'hex' => '#5E633D'],
            ['code' => '11031', 'abbr' => 'BUR',  'name' => 'Burgundy',             'hex' => '#602E35'],
            ['code' => '11033', 'abbr' => 'ASK',  'name' => 'Aqua Sky',             'hex' => '#67C1CF'],
            ['code' => '11036', 'abbr' => 'RAZ',  'name' => 'Royal Azure',          'hex' => '#2F5D9F'],
            ['code' => '11042', 'abbr' => 'DSP',  'name' => 'Deep Sapphire',        'hex' => '#315DA8'],
            ['code' => '11044', 'abbr' => 'SSM',  'name' => 'Soft Silver Mist',     'hex' => '#D9D9D9'],
            ['code' => '11045', 'abbr' => 'CLG',  'name' => 'Cool Lavender Grey',   'hex' => '#B7B7C2'],
            ['code' => '11046', 'abbr' => 'STG',  'name' => 'Steel Grey',           'hex' => '#9EA3AD'],
            ['code' => '11047', 'abbr' => 'SLG',  'name' => 'Slate Grey',           'hex' => '#7C818C'],
            ['code' => '11048', 'abbr' => 'GPG',  'name' => 'Graphite Grey',        'hex' => '#6B707A'],
            ['code' => '11049', 'abbr' => 'ESB',  'name' => 'Espresso Brown',       'hex' => '#3B3432'],
            ['code' => '11051', 'abbr' => 'MNN',  'name' => 'Midnight Navy',        'hex' => '#2B2F4A'],
            ['code' => '11055', 'abbr' => 'CHN',  'name' => 'Charcoal Navy',        'hex' => '#2F334F'],
        ];

        $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

        // Hero images — product photos (shared across all products)
        $heroImages = [
            'products/hero/model-brown1.png',
            'products/hero/model-brown2.png',
            'products/hero/model-brown3.png',
            'products/hero/model-brown4.png',
            'products/hero/model-brown5.png',
            'products/hero/model-brown6.png',
            'products/hero/model-black.png',
            'products/hero/black-full.png',
            'products/hero/black2.png',
            'products/hero/black6.png',
            'products/hero/white.png',
            'products/hero/couple-bw.png',
            'products/hero/001A4302-copy.jpg',
            'products/hero/05.jpg',
            'products/hero/IMG_7872.JPG',
            'products/hero/IMG_7873-copy.png',
        ];

        // ─── Seeding ───────────────────────────────────────────────────

        DB::transaction(function () use ($collars, $cuffs, $pockets, $colors, $sizes, $heroImages) {

            // 1. Collection
            $collection = Collection::updateOrCreate(
                ['slug' => 'cm-classic'],
                ['name' => 'CM Classic', 'is_active' => true, 'sort_order' => 1]
            );

            // 2. Category
            $category = Category::updateOrCreate(
                ['slug' => 'shirt'],
                ['name' => 'เสื้อเชิ้ต', 'is_active' => true, 'sort_order' => 1]
            );

            $productCount   = 0;
            $colorCount     = 0;
            $imageCount     = 0;
            $variantCount   = 0;

            // 3. Loop: 5 collars × 3 cuffs × 2 pockets = 30 products
            foreach ($collars as $collarCode => $collarName) {
                foreach ($cuffs as $cuffCode => $cuffName) {
                    foreach ($pockets as $pocketCode => $pocketName) {

                        $productName = "CM Classic - {$collarName} - {$cuffName} - {$pocketName}";
                        $productSlug = Str::slug($productName);
                        $description = "เสื้อเชิ้ต CHO.MIN รุ่น CM Classic คอ{$collarName} แขน{$cuffName} {$pocketName}";

                        $product = Product::updateOrCreate(
                            ['slug' => $productSlug],
                            [
                                'name'          => $productName,
                                'description'   => $description,
                                'price'         => 590.00,
                                'collection_id' => $collection->id,
                                'category_id'   => $category->id,
                                'is_active'     => true,
                                'is_featured'   => false,
                                'sort_order'    => $productCount + 1,
                            ]
                        );

                        $productCount++;
                        $firstColorId = null;

                        // 5. Loop: 50 colors per product
                        foreach ($colors as $colorIndex => $color) {

                            $productColor = ProductColor::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'color_code' => $color['hex'],
                                ],
                                [
                                    'name'       => $color['name'],
                                    'sort_order' => $colorIndex + 1,
                                ]
                            );

                            if ($colorIndex === 0) {
                                $firstColorId = $productColor->id;
                            }

                            $colorCount++;

                            // 5. ProductImage (swatch) — 1 per color
                            ProductImage::updateOrCreate(
                                [
                                    'product_id'       => $product->id,
                                    'product_color_id' => $productColor->id,
                                ],
                                [
                                    'image_path' => "products/colors/{$color['code']}.png",
                                    'is_primary' => false,
                                    'sort_order' => count($heroImages) + $colorIndex + 1,
                                ]
                            );

                            $imageCount++;

                            // 6. ProductVariants — 9 sizes per color
                            foreach ($sizes as $size) {
                                $sku = "CHO-CLS-{$collarCode}-{$cuffCode}-{$pocketCode}-{$color['code']}-{$size}";

                                ProductVariant::updateOrCreate(
                                    ['sku' => $sku],
                                    [
                                        'product_id'       => $product->id,
                                        'product_color_id' => $productColor->id,
                                        'size'             => $size,
                                        'stock'            => 100,
                                    ]
                                );

                                $variantCount++;
                            }
                        }

                        // Hero images — product photos tied to first color
                        foreach ($heroImages as $heroIndex => $heroPath) {
                            ProductImage::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'image_path' => $heroPath,
                                ],
                                [
                                    'product_color_id' => $firstColorId,
                                    'is_primary'       => ($heroIndex === 0),
                                    'sort_order'       => $heroIndex,
                                ]
                            );
                            $imageCount++;
                        }

                        $this->command->info(
                            "[{$productCount}/30] {$productName} — {$colorCount} colors, {$imageCount} images, {$variantCount} variants (running total)"
                        );
                    }
                }
            }

            // 7. Final counts
            $this->command->info('');
            $this->command->info('=== ProductSeeder complete ===');
            $this->command->info("Products : {$productCount}");
            $this->command->info("Colors   : {$colorCount}");
            $this->command->info("Images   : {$imageCount}");
            $this->command->info("Variants : {$variantCount}");
        });
    }
}
