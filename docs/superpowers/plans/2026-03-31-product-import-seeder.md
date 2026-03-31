# Product Import Seeder Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Seed 30 CHO.MIN shirt products (5 collars × 3 cuffs × 2 pockets) with 50 colors and 9 sizes each, totaling 13,500 variants.

**Architecture:** Single Laravel Seeder class with hardcoded product data arrays. Color swatch PNG images copied from Google Drive download to Laravel storage. Uses Eloquent models with DB transactions for data integrity.

**Tech Stack:** Laravel 13, Eloquent ORM, PHP 8.3+

---

## File Structure

| Action | File | Responsibility |
|--------|------|----------------|
| Create | `database/seeders/ProductSeeder.php` | Main seeder — creates collection, category, 30 products, 1500 colors, 1500 images, 13500 variants |
| Create | `storage/app/public/products/colors/*.png` | 50 color swatch images copied from Drive download |

---

### Task 1: Copy Color Swatch Images to Storage

**Files:**
- Create: `storage/app/public/products/colors/` (directory + 50 PNG files)

- [ ] **Step 1: Create the storage directory**

```bash
mkdir -p /Users/jumpondumkham/Desktop/GitHub/chomin/storage/app/public/products/colors
```

- [ ] **Step 2: Copy swatch images from Drive download**

```bash
cp /tmp/chomin-drive/Work/icon/sku/7-88_color19/*.png /Users/jumpondumkham/Desktop/GitHub/chomin/storage/app/public/products/colors/
cp /tmp/chomin-drive/Work/icon/sku/11002-11055_color31/*.png /Users/jumpondumkham/Desktop/GitHub/chomin/storage/app/public/products/colors/
```

- [ ] **Step 3: Verify 50 images copied**

```bash
ls /Users/jumpondumkham/Desktop/GitHub/chomin/storage/app/public/products/colors/ | wc -l
```

Expected: `50`

- [ ] **Step 4: Ensure storage symlink exists**

```bash
cd /Users/jumpondumkham/Desktop/GitHub/chomin && php artisan storage:link 2>/dev/null || echo "Link already exists"
```

---

### Task 2: Create ProductSeeder

**Files:**
- Create: `database/seeders/ProductSeeder.php`

- [ ] **Step 1: Create the seeder file**

Create `database/seeders/ProductSeeder.php` with the complete seeder code below.

The seeder must:
1. Wrap everything in a DB transaction
2. Create 1 Collection ("CM Classic")
3. Create 1 Category ("เสื้อเชิ้ต")
4. Loop through 5 collars × 3 cuffs × 2 pockets = 30 products
5. For each product, create 50 ProductColors
6. For each color, create 1 ProductImage (swatch)
7. For each color, create 9 ProductVariants (one per size)
8. Use `Str::slug()` for product slugs
9. Generate SKU as `CHO-CLS-{collar}-{cuff}-{pocket}-{colorCode}-{size}`

```php
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
        $collars = [
            ['code' => 'FR', 'name' => 'French Collar'],
            ['code' => 'IT', 'name' => 'Italian Collar'],
            ['code' => 'MD', 'name' => 'Mandarin Collar'],
            ['code' => 'BD', 'name' => 'Button Down'],
            ['code' => 'BC', 'name' => 'Band Collar'],
        ];

        $cuffs = [
            ['code' => 'C1', 'name' => '1 Button'],
            ['code' => 'C2', 'name' => '2 Button'],
            ['code' => 'CF', 'name' => 'French Cuff'],
        ];

        $pockets = [
            ['code' => 'NP', 'name' => 'No Pocket'],
            ['code' => 'YP', 'name' => 'Yes Pocket'],
        ];

        $colors = [
            ['factory_code' => '7', 'name' => 'Warm Light Greige', 'hex' => '#D8D5C5'],
            ['factory_code' => '13', 'name' => 'Vivid Golden Yellow', 'hex' => '#F5D300'],
            ['factory_code' => '21', 'name' => 'Light Khaki', 'hex' => '#D9D2B4'],
            ['factory_code' => '26', 'name' => 'Olive Green', 'hex' => '#6F6A47'],
            ['factory_code' => '33', 'name' => 'Deep Teal Green', 'hex' => '#24524F'],
            ['factory_code' => '39', 'name' => 'Muted Slate Blue', 'hex' => '#505E84'],
            ['factory_code' => '40', 'name' => 'Deep Indigo', 'hex' => '#3C3F5A'],
            ['factory_code' => '42', 'name' => 'Soft Lavender Gray', 'hex' => '#C7C2D8'],
            ['factory_code' => '43', 'name' => 'Dusty Violet', 'hex' => '#7C72A3'],
            ['factory_code' => '48', 'name' => 'Near Black Navy', 'hex' => '#070A12'],
            ['factory_code' => '55', 'name' => 'Muted Teal', 'hex' => '#3F8C84'],
            ['factory_code' => '60', 'name' => 'Charcoal Plum', 'hex' => '#564D57'],
            ['factory_code' => '61', 'name' => 'Dark Graphite', 'hex' => '#3E404A'],
            ['factory_code' => '62', 'name' => 'Deep Navy Gray', 'hex' => '#243447'],
            ['factory_code' => '65', 'name' => 'Blush Taupe', 'hex' => '#DCC9CF'],
            ['factory_code' => '73', 'name' => 'Soft Coral Pink', 'hex' => '#D9817E'],
            ['factory_code' => '79', 'name' => 'Burnt Orange', 'hex' => '#EB6529'],
            ['factory_code' => '81', 'name' => 'Vivid Red', 'hex' => '#C81D14'],
            ['factory_code' => '88', 'name' => 'Deep Crimson', 'hex' => '#8F1620'],
            ['factory_code' => '11002', 'name' => 'Floral Blush', 'hex' => '#FBECE8'],
            ['factory_code' => '11003', 'name' => 'Pastel Aqua', 'hex' => '#C2E2EB'],
            ['factory_code' => '11004', 'name' => 'Baby Powder', 'hex' => '#E1E8F9'],
            ['factory_code' => '11007', 'name' => 'Creamy Sun', 'hex' => '#FFF7D0'],
            ['factory_code' => '11009', 'name' => 'Light Baby Blue', 'hex' => '#D8EFFF'],
            ['factory_code' => '11010', 'name' => 'Sky Blue', 'hex' => '#8CC6F8'],
            ['factory_code' => '11011', 'name' => 'Pink Breeze', 'hex' => '#F2C3E6'],
            ['factory_code' => '11012', 'name' => 'Lilac Petal', 'hex' => '#E6C4EB'],
            ['factory_code' => '11013', 'name' => 'White Lavender', 'hex' => '#E2D9F4'],
            ['factory_code' => '11014', 'name' => 'Cool Light Blue', 'hex' => '#A4BEF0'],
            ['factory_code' => '11015', 'name' => 'Deep Grape', 'hex' => '#4F3C6F'],
            ['factory_code' => '11016', 'name' => 'Dark Cocoa Brown', 'hex' => '#3E3342'],
            ['factory_code' => '11021', 'name' => 'Fuchsia', 'hex' => '#B832A6'],
            ['factory_code' => '11022', 'name' => 'Calm Soft Sand', 'hex' => '#E8E6D2'],
            ['factory_code' => '11023', 'name' => 'Off White', 'hex' => '#F7F6EF'],
            ['factory_code' => '11024', 'name' => 'Creamy Shell', 'hex' => '#F7E1B5'],
            ['factory_code' => '11025', 'name' => 'Mocha Brown', 'hex' => '#6D4E3F'],
            ['factory_code' => '11026', 'name' => 'Coral Rose', 'hex' => '#EB9C8E'],
            ['factory_code' => '11027', 'name' => 'Olive Verde', 'hex' => '#5E633D'],
            ['factory_code' => '11031', 'name' => 'Burgundy', 'hex' => '#602E35'],
            ['factory_code' => '11033', 'name' => 'Aqua Sky', 'hex' => '#67C1CF'],
            ['factory_code' => '11036', 'name' => 'Royal Azure', 'hex' => '#2F5D9F'],
            ['factory_code' => '11042', 'name' => 'Deep Sapphire', 'hex' => '#315DA8'],
            ['factory_code' => '11044', 'name' => 'Soft Silver Mist', 'hex' => '#D9D9D9'],
            ['factory_code' => '11045', 'name' => 'Cool Lavender Grey', 'hex' => '#B7B7C2'],
            ['factory_code' => '11046', 'name' => 'Steel Grey', 'hex' => '#9EA3AD'],
            ['factory_code' => '11047', 'name' => 'Slate Grey', 'hex' => '#7C818C'],
            ['factory_code' => '11048', 'name' => 'Graphite Grey', 'hex' => '#6B707A'],
            ['factory_code' => '11049', 'name' => 'Espresso Brown', 'hex' => '#3B3432'],
            ['factory_code' => '11051', 'name' => 'Midnight Navy', 'hex' => '#2B2F4A'],
            ['factory_code' => '11055', 'name' => 'Charcoal Navy', 'hex' => '#2F334F'],
        ];

        $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

        DB::transaction(function () use ($collars, $cuffs, $pockets, $colors, $sizes) {
            $collection = Collection::updateOrCreate(
                ['slug' => 'cm-classic'],
                ['name' => 'CM Classic', 'is_active' => true, 'sort_order' => 0]
            );

            $category = Category::updateOrCreate(
                ['slug' => 'shirt'],
                ['name' => 'เสื้อเชิ้ต', 'is_active' => true, 'sort_order' => 0]
            );

            $productOrder = 0;

            foreach ($collars as $collar) {
                foreach ($cuffs as $cuff) {
                    foreach ($pockets as $pocket) {
                        $productName = "CM Classic - {$collar['name']} - {$cuff['name']} - {$pocket['name']}";
                        $productSlug = Str::slug($productName);

                        $product = Product::updateOrCreate(
                            ['slug' => $productSlug],
                            [
                                'name' => $productName,
                                'description' => "เสื้อเชิ้ต CHO.MIN รุ่น CM Classic คอ{$collar['name']} แขน{$cuff['name']} {$pocket['name']}",
                                'price' => 590.00,
                                'collection_id' => $collection->id,
                                'category_id' => $category->id,
                                'is_active' => true,
                                'is_featured' => false,
                                'sort_order' => $productOrder++,
                            ]
                        );

                        foreach ($colors as $colorIndex => $color) {
                            $productColor = ProductColor::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'name' => $color['name'],
                                ],
                                [
                                    'color_code' => $color['hex'],
                                    'sort_order' => $colorIndex,
                                ]
                            );

                            ProductImage::updateOrCreate(
                                [
                                    'product_id' => $product->id,
                                    'product_color_id' => $productColor->id,
                                ],
                                [
                                    'image_path' => "products/colors/{$color['factory_code']}.png",
                                    'is_primary' => $colorIndex === 0,
                                    'sort_order' => $colorIndex,
                                ]
                            );

                            foreach ($sizes as $size) {
                                $sku = "CHO-CLS-{$collar['code']}-{$cuff['code']}-{$pocket['code']}-{$color['factory_code']}-{$size}";

                                ProductVariant::updateOrCreate(
                                    ['sku' => $sku],
                                    [
                                        'product_id' => $product->id,
                                        'product_color_id' => $productColor->id,
                                        'size' => $size,
                                        'stock' => 100,
                                    ]
                                );
                            }
                        }

                        $this->command->info("Created: {$productName}");
                    }
                }
            }

            $this->command->info('');
            $this->command->info('=== Product Seeding Complete ===');
            $this->command->info("Products: " . Product::count());
            $this->command->info("Colors: " . ProductColor::count());
            $this->command->info("Images: " . ProductImage::count());
            $this->command->info("Variants: " . ProductVariant::count());
        });
    }
}
```

- [ ] **Step 2: Verify the seeder file was created correctly**

```bash
php -l /Users/jumpondumkham/Desktop/GitHub/chomin/database/seeders/ProductSeeder.php
```

Expected: `No syntax errors detected in database/seeders/ProductSeeder.php`

---

### Task 3: Run the Seeder

**Files:**
- Uses: `database/seeders/ProductSeeder.php`

- [ ] **Step 1: Run the product seeder**

```bash
cd /Users/jumpondumkham/Desktop/GitHub/chomin && php artisan db:seed --class=ProductSeeder
```

Expected output (30 product lines followed by summary):
```
Created: CM Classic - French Collar - 1 Button - No Pocket
Created: CM Classic - French Collar - 1 Button - Yes Pocket
...
Created: CM Classic - Band Collar - French Cuff - Yes Pocket

=== Product Seeding Complete ===
Products: 30
Colors: 1500
Images: 1500
Variants: 13500
```

- [ ] **Step 2: Verify data in database**

```bash
cd /Users/jumpondumkham/Desktop/GitHub/chomin && php artisan tinker --execute="
echo 'Collections: ' . \App\Models\Collection::count() . PHP_EOL;
echo 'Categories: ' . \App\Models\Category::count() . PHP_EOL;
echo 'Products: ' . \App\Models\Product::count() . PHP_EOL;
echo 'Colors: ' . \App\Models\ProductColor::count() . PHP_EOL;
echo 'Images: ' . \App\Models\ProductImage::count() . PHP_EOL;
echo 'Variants: ' . \App\Models\ProductVariant::count() . PHP_EOL;
echo PHP_EOL;
echo 'Sample SKU: ' . \App\Models\ProductVariant::first()->sku . PHP_EOL;
echo 'Sample Product: ' . \App\Models\Product::first()->name . PHP_EOL;
"
```

Expected:
```
Collections: 1
Categories: 1
Products: 30
Colors: 1500
Images: 1500
Variants: 13500

Sample SKU: CHO-CLS-FR-C1-NP-7-XS
Sample Product: CM Classic - French Collar - 1 Button - No Pocket
```

- [ ] **Step 3: Verify swatch images accessible**

```bash
ls /Users/jumpondumkham/Desktop/GitHub/chomin/storage/app/public/products/colors/ | wc -l
```

Expected: `50`

---

### Task 4: Commit

- [ ] **Step 1: Stage and commit all changes**

```bash
cd /Users/jumpondumkham/Desktop/GitHub/chomin
git add database/seeders/ProductSeeder.php
git add docs/superpowers/specs/2026-03-31-product-import-seeder-design.md
git add docs/superpowers/plans/2026-03-31-product-import-seeder.md
git commit -m "feat: add product seeder with 30 CHO.MIN shirt products (50 colors × 9 sizes)"
```

Note: `storage/app/public/products/colors/*.png` should NOT be committed to git — these are runtime assets. Add to `.gitignore` if not already excluded.
