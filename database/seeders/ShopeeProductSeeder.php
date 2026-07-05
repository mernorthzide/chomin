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

/**
 * Imports the real CHO.MIN catalogue mirrored from the Shopee shop (chomin640).
 * Source data + images are produced by scripts/shopee_extract.py into
 * database/seeders/data/shopee.json. Idempotent — safe to re-run.
 */
class ShopeeProductSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/shopee.json');
        if (! is_file($path)) {
            $this->command?->warn("ShopeeProductSeeder skipped: {$path} not found. Run scripts/shopee_extract.py first.");

            return;
        }

        $families = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $activeSlugs = array_column($families, 'slug');

        DB::transaction(function () use ($families, $activeSlugs) {
            $category = Category::updateOrCreate(
                ['slug' => 'shirt'],
                ['name' => 'เสื้อเชิ้ต', 'is_active' => true, 'sort_order' => 1],
            );
            $category->translations()->updateOrCreate(['locale' => 'th'], ['name' => 'เสื้อเชิ้ต']);
            $category->translations()->updateOrCreate(['locale' => 'en'], ['name' => 'Shirts']);

            // Demo/other catalogue stays out of the way — Shopee set is the real one.
            Collection::query()->whereNotIn('slug', $activeSlugs)->update(['is_active' => false]);
            Product::query()->whereNotIn('slug', $activeSlugs)->update(['is_active' => false, 'is_featured' => false]);

            foreach (array_values($families) as $sortIndex => $fam) {
                $slug = $fam['slug'];
                $colorCount = count($fam['colors']);
                $gallery = $fam['gallery'];

                $collection = Collection::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $fam['family_en'],
                        'description' => $this->descEn($fam, $colorCount),
                        'image' => $gallery[0] ?? null,
                        'banner_image' => $gallery[0] ?? null,
                        'layout_type' => 'side-hero',
                        'is_active' => true,
                        'sort_order' => $sortIndex + 1,
                    ],
                );
                $collection->translations()->updateOrCreate(['locale' => 'th'], [
                    'name' => $fam['family_en'],
                    'description' => $this->descTh($fam, $colorCount),
                ]);
                $collection->translations()->updateOrCreate(['locale' => 'en'], [
                    'name' => $fam['family_en'],
                    'description' => $this->descEn($fam, $colorCount),
                ]);

                $product = Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => "{$fam['family_en']} Slim Shirt",
                        'description' => $this->descTh($fam, $colorCount),
                        'price' => $fam['price'],
                        'sale_price' => null,
                        'sale_starts_at' => null,
                        'sale_ends_at' => null,
                        'collection_id' => $collection->id,
                        'category_id' => $category->id,
                        'is_active' => true,
                        'is_featured' => true,
                        'sort_order' => $sortIndex + 1,
                    ],
                );
                $product->translations()->updateOrCreate(['locale' => 'th'], [
                    'name' => "เสื้อเชิ้ตทรงสลิม สี {$fam['family']}",
                    'description' => $this->descTh($fam, $colorCount),
                    'seo_title' => "เสื้อเชิ้ตทรงสลิม สี {$fam['family']} | CHO.MIN",
                    'seo_description' => "เสื้อเชิ้ต CHO.MIN สี {$fam['family']} ผ้า Premium Japanese Cotton เลือกได้ {$colorCount} เฉด ปรับดีเทลได้ ราคา ".number_format((float) $fam['price']).' บาท',
                ]);
                $product->translations()->updateOrCreate(['locale' => 'en'], [
                    'name' => "{$fam['family_en']} Slim Shirt",
                    'description' => $this->descEn($fam, $colorCount),
                    'seo_title' => "{$fam['family_en']} Slim Shirt | CHO.MIN",
                    'seo_description' => "CHO.MIN {$fam['family_en']} shirt in Premium Japanese Cotton, {$colorCount} shades, customisable details.",
                ]);

                $firstColorId = $this->syncColors($product, $fam);
                $this->syncGallery($product, $gallery, $firstColorId);
            }

            $this->command?->info('ShopeeProductSeeder complete: '.count($families).' shirt families imported from Shopee.');
        });
    }

    /** @return int|null first color id (used as gallery owner) */
    private function syncColors(Product $product, array $fam): ?int
    {
        $sizes = $fam['sizes'];
        $firstColorId = null;

        foreach (array_values($fam['colors']) as $index => $color) {
            $code = $color['code'];
            $productColor = ProductColor::updateOrCreate(
                ['product_id' => $product->id, 'slug' => Str::lower($code)],
                [
                    'name' => $color['name_en'],
                    'color_code' => $color['hex'],
                    'sort_order' => $index + 1,
                ],
            );
            $productColor->translations()->updateOrCreate(['locale' => 'th'], ['name' => $color['name_th']]);
            $productColor->translations()->updateOrCreate(['locale' => 'en'], ['name' => $color['name_en']]);

            $firstColorId ??= $productColor->id;

            if (! empty($color['swatch'])) {
                ProductImage::updateOrCreate(
                    ['product_id' => $product->id, 'product_color_id' => $productColor->id],
                    ['image_path' => $color['swatch'], 'is_primary' => false, 'sort_order' => 100 + $index],
                );
            }

            foreach ($sizes as $size) {
                ProductVariant::updateOrCreate(
                    ['sku' => "CHO-{$code}-{$size}"],
                    [
                        'product_id' => $product->id,
                        'product_color_id' => $productColor->id,
                        'size' => $size,
                        'stock' => 100,
                    ],
                );
            }
        }

        return $firstColorId;
    }

    private function syncGallery(Product $product, array $gallery, ?int $firstColorId): void
    {
        // product_color_id is NOT NULL, so gallery images hang off the first colour.
        ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);

        foreach ($gallery as $index => $imagePath) {
            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'image_path' => $imagePath],
                [
                    'product_color_id' => $firstColorId,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ],
            );
        }
    }

    private function descTh(array $fam, int $colorCount): string
    {
        return "เสื้อเชิ้ต CHO.MIN ทรงสลิม สี {$fam['family']}\n\n"
            ."ผลิตจากผ้า Premium Japanese Cotton เนื้อผ้าสัมผัสนุ่ม เบา ใส่สบาย ระบายอากาศดี ไม่ยับง่าย\n\n"
            ."เลือกได้ {$colorCount} เฉดในชุดสีนี้ พร้อมปรับดีเทล ปกคอ สาบหน้า กระเป๋า และปลายแขนได้ตามสไตล์คุณ ไซส์ S–XL";
    }

    private function descEn(array $fam, int $colorCount): string
    {
        return "CHO.MIN slim-fit shirt in {$fam['family_en']}.\n\n"
            ."Crafted from Premium Japanese Cotton — soft, light, breathable, and wrinkle-resistant.\n\n"
            ."Choose from {$colorCount} shades in this palette and customise the collar, placket, pocket, and cuff to your style. Sizes S–XL.";
    }
}
