# Chomin Ecommerce Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a premium Thai fashion ecommerce site with Laravel 11, Blade/Tailwind frontend, Filament 3 admin, PromptPay payment, and points system.

**Architecture:** Laravel 11 monolith with Blade + Tailwind CSS + Alpine.js for the storefront, Filament 3 for the admin panel, MySQL 8 for data, Laravel Queue for background email. All in one deployable app.

**Tech Stack:** PHP 8.3, Laravel 11, Tailwind CSS 3, Alpine.js, Filament 3, Filament Shield, MySQL 8, Laravel Breeze, Laravel Mail

**Spec:** `docs/superpowers/specs/2026-03-30-chomin-ecommerce-design.md`

---

## Phase 1: Foundation (Setup + DB + Models + Auth)

> Deliverable: Laravel project with all migrations, models with relationships, seeders, and user authentication working.

---

### Task 1: Scaffold Laravel Project

**Files:**
- Create: entire Laravel project structure via `laravel new`

- [ ] **Step 1: Create Laravel project**

```bash
cd /Users/jumpondumkham/Desktop/GitHub/chomin
composer create-project laravel/laravel . --no-interaction
```

Note: Since `.gitattributes` already exists, composer will merge. If it complains, remove `.gitattributes` first and re-run.

- [ ] **Step 2: Verify the project runs**

```bash
php artisan serve &
curl -s http://127.0.0.1:8000 | head -20
kill %1
```

Expected: HTML output from Laravel welcome page.

- [ ] **Step 3: Configure `.env` for MySQL**

Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chomin
DB_USERNAME=root
DB_PASSWORD=
```

- [ ] **Step 4: Create the database**

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS chomin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

- [ ] **Step 5: Run default migrations to verify DB connection**

```bash
php artisan migrate
```

Expected: Migration table created, default Laravel migrations run successfully.

- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "feat: scaffold Laravel 11 project with MySQL config"
```

---

### Task 2: Install Core Dependencies

**Files:**
- Modify: `composer.json`, `package.json`, `tailwind.config.js`, `vite.config.js`

- [ ] **Step 1: Install Laravel Breeze (Blade stack)**

```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
```

- [ ] **Step 2: Install Filament 3**

```bash
composer require filament/filament:"^3.2"
php artisan filament:install --panels
```

When prompted for the panel ID, enter: `admin`

- [ ] **Step 3: Install Filament Shield**

```bash
composer require bezhansalleh/filament-shield
php artisan vendor:publish --tag=filament-shield-config
```

- [ ] **Step 4: Install Excel export package**

```bash
composer require maatwebsite/excel
```

- [ ] **Step 5: Install npm dependencies and build**

```bash
npm install
npm run build
```

Expected: Vite build completes without errors.

- [ ] **Step 6: Verify Breeze auth works**

```bash
php artisan migrate
php artisan serve &
curl -s http://127.0.0.1:8000/register | grep -o "Register"
kill %1
```

Expected: "Register" found in output.

- [ ] **Step 7: Commit**

```bash
git add -A
git commit -m "feat: install Breeze, Filament 3, Shield, and Maatwebsite Excel"
```

---

### Task 3: Configure Tailwind with Chomin Design Tokens

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Update Tailwind config with brand colors and fonts**

Replace `tailwind.config.js`:
```js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    black: '#000000',
                    white: '#FFFFFF',
                    brown: '#3C2415',
                    gray: '#F5F5F5',
                    'gray-dark': '#333333',
                    'gray-medium': '#666666',
                    'gray-light': '#999999',
                    'gray-border': '#E5E5E5',
                },
            },
            fontFamily: {
                sans: ['IBM Plex Sans Thai', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
        },
    },
    plugins: [forms],
};
```

- [ ] **Step 2: Add Google Fonts import to app.css**

Add at the top of `resources/css/app.css`:
```css
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap');

@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
    body {
        @apply font-sans text-brand-black antialiased;
    }
}
```

- [ ] **Step 3: Build and verify**

```bash
npm run build
```

Expected: Build succeeds.

- [ ] **Step 4: Commit**

```bash
git add tailwind.config.js resources/css/app.css
git commit -m "feat: configure Tailwind with Chomin brand tokens and Thai fonts"
```

---

### Task 4: User Model Enhancement + Migration

**Files:**
- Modify: `app/Models/User.php`
- Modify: `database/migrations/0001_01_01_000000_create_users_table.php`

- [ ] **Step 1: Add phone and points columns to users migration**

Edit `database/migrations/0001_01_01_000000_create_users_table.php`, inside the `users` table schema, add after `password`:

```php
$table->string('phone')->nullable();
$table->integer('points')->default(0);
```

- [ ] **Step 2: Update User model fillable and casts**

Edit `app/Models/User.php`:
```php
protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'points',
];

protected $hidden = [
    'password',
    'remember_token',
];

protected function casts(): array
{
    return [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'points' => 'integer',
    ];
}
```

- [ ] **Step 3: Add relationships to User model**

Add to `app/Models/User.php`:
```php
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

public function addresses(): HasMany
{
    return $this->hasMany(Address::class);
}

public function defaultAddress(): HasOne
{
    return $this->hasOne(Address::class)->where('is_default', true);
}

public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}

public function wishlists(): HasMany
{
    return $this->hasMany(Wishlist::class);
}

public function pointTransactions(): HasMany
{
    return $this->hasMany(PointTransaction::class);
}

public function cart(): HasOne
{
    return $this->hasOne(Cart::class);
}
```

- [ ] **Step 4: Commit**

```bash
git add app/Models/User.php database/migrations/0001_01_01_000000_create_users_table.php
git commit -m "feat: add phone, points columns and relationships to User model"
```

---

### Task 5: Create Catalog Migrations (collections, categories, products, colors, images, variants)

**Files:**
- Create: `database/migrations/xxxx_create_collections_table.php`
- Create: `database/migrations/xxxx_create_categories_table.php`
- Create: `database/migrations/xxxx_create_products_table.php`
- Create: `database/migrations/xxxx_create_product_colors_table.php`
- Create: `database/migrations/xxxx_create_product_images_table.php`
- Create: `database/migrations/xxxx_create_product_variants_table.php`

- [ ] **Step 1: Create collections migration**

```bash
php artisan make:migration create_collections_table
```

Edit the migration:
```php
public function up(): void
{
    Schema::create('collections', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('image')->nullable();
        $table->string('banner_image')->nullable();
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('collections');
}
```

- [ ] **Step 2: Create categories migration**

```bash
php artisan make:migration create_categories_table
```

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->string('image')->nullable();
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('categories');
}
```

- [ ] **Step 3: Create products migration**

```bash
php artisan make:migration create_products_table
```

```php
public function up(): void
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
        $table->foreignId('category_id')->constrained()->cascadeOnDelete();
        $table->boolean('is_active')->default(true);
        $table->boolean('is_featured')->default(false);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('products');
}
```

- [ ] **Step 4: Create product_colors migration**

```bash
php artisan make:migration create_product_colors_table
```

```php
public function up(): void
{
    Schema::create('product_colors', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->string('name');
        $table->string('color_code');
        $table->integer('sort_order')->default(0);
    });
}

public function down(): void
{
    Schema::dropIfExists('product_colors');
}
```

- [ ] **Step 5: Create product_images migration**

```bash
php artisan make:migration create_product_images_table
```

```php
public function up(): void
{
    Schema::create('product_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_color_id')->constrained()->cascadeOnDelete();
        $table->string('image_path');
        $table->boolean('is_primary')->default(false);
        $table->integer('sort_order')->default(0);
    });
}

public function down(): void
{
    Schema::dropIfExists('product_images');
}
```

- [ ] **Step 6: Create product_variants migration**

```bash
php artisan make:migration create_product_variants_table
```

```php
public function up(): void
{
    Schema::create('product_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_color_id')->constrained()->cascadeOnDelete();
        $table->string('size');
        $table->integer('stock')->default(0);
        $table->string('sku')->unique()->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('product_variants');
}
```

- [ ] **Step 7: Run migrations**

```bash
php artisan migrate
```

Expected: All 6 new tables created successfully.

- [ ] **Step 8: Commit**

```bash
git add database/migrations/
git commit -m "feat: create catalog migrations (collections, categories, products, colors, images, variants)"
```

---

### Task 6: Create Commerce Migrations (addresses, carts, wishlists, orders, coupons, payments, points)

**Files:**
- Create: 9 migration files

- [ ] **Step 1: Create addresses migration**

```bash
php artisan make:migration create_addresses_table
```

```php
public function up(): void
{
    Schema::create('addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('name');
        $table->string('phone');
        $table->text('address');
        $table->string('district');
        $table->string('province');
        $table->string('postal_code');
        $table->boolean('is_default')->default(false);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('addresses');
}
```

- [ ] **Step 2: Create carts and cart_items migrations**

```bash
php artisan make:migration create_carts_table
php artisan make:migration create_cart_items_table
```

carts:
```php
public function up(): void
{
    Schema::create('carts', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
        $table->string('session_id')->nullable();
        $table->timestamps();

        $table->index('session_id');
    });
}

public function down(): void
{
    Schema::dropIfExists('carts');
}
```

cart_items:
```php
public function up(): void
{
    Schema::create('cart_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
        $table->integer('quantity')->default(1);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('cart_items');
}
```

- [ ] **Step 3: Create wishlists migration**

```bash
php artisan make:migration create_wishlists_table
```

```php
public function up(): void
{
    Schema::create('wishlists', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->timestamps();

        $table->unique(['user_id', 'product_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('wishlists');
}
```

- [ ] **Step 4: Create coupons migration**

```bash
php artisan make:migration create_coupons_table
```

```php
public function up(): void
{
    Schema::create('coupons', function (Blueprint $table) {
        $table->id();
        $table->string('code')->unique();
        $table->enum('type', ['fixed', 'percent']);
        $table->decimal('value', 10, 2);
        $table->decimal('max_discount', 10, 2)->nullable();
        $table->decimal('min_order_amount', 10, 2)->default(0);
        $table->integer('max_uses')->nullable();
        $table->integer('used_count')->default(0);
        $table->timestamp('starts_at')->nullable();
        $table->timestamp('expires_at')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('coupons');
}
```

- [ ] **Step 5: Create orders and order_items migrations**

```bash
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
```

orders:
```php
public function up(): void
{
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->string('order_number')->unique();
        $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'shipping', 'completed', 'cancelled'])->default('pending');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('shipping_fee', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('total', 10, 2);
        $table->integer('points_earned')->default(0);
        $table->integer('points_used')->default(0);
        $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
        $table->string('shipping_name');
        $table->string('shipping_phone');
        $table->text('shipping_address');
        $table->string('shipping_district');
        $table->string('shipping_province');
        $table->string('shipping_postal_code');
        $table->string('tracking_number')->nullable();
        $table->string('carrier_name')->nullable();
        $table->timestamp('shipped_at')->nullable();
        $table->timestamp('completed_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();

        $table->index('status');
        $table->index('order_number');
    });
}

public function down(): void
{
    Schema::dropIfExists('orders');
}
```

order_items:
```php
public function up(): void
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_id')->constrained()->cascadeOnDelete();
        $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
        $table->string('product_name');
        $table->string('color_name');
        $table->string('size');
        $table->decimal('price', 10, 2);
        $table->integer('quantity');
    });
}

public function down(): void
{
    Schema::dropIfExists('order_items');
}
```

- [ ] **Step 6: Create payment_slips migration**

```bash
php artisan make:migration create_payment_slips_table
```

```php
public function up(): void
{
    Schema::create('payment_slips', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->cascadeOnDelete();
        $table->string('image_path');
        $table->timestamp('uploaded_at');
        $table->timestamp('confirmed_at')->nullable();
        $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->string('rejection_reason')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('payment_slips');
}
```

- [ ] **Step 7: Create point_transactions migration**

```bash
php artisan make:migration create_point_transactions_table
```

```php
public function up(): void
{
    Schema::create('point_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
        $table->integer('points');
        $table->enum('type', ['earn', 'redeem', 'adjust']);
        $table->string('description');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('point_transactions');
}
```

- [ ] **Step 8: Create banners, shipping_settings, site_settings migrations**

```bash
php artisan make:migration create_banners_table
php artisan make:migration create_shipping_settings_table
php artisan make:migration create_site_settings_table
```

banners:
```php
public function up(): void
{
    Schema::create('banners', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();
        $table->string('subtitle')->nullable();
        $table->string('image');
        $table->string('link')->nullable();
        $table->boolean('is_active')->default(true);
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('banners');
}
```

shipping_settings:
```php
public function up(): void
{
    Schema::create('shipping_settings', function (Blueprint $table) {
        $table->id();
        $table->decimal('shipping_fee', 10, 2)->default(50.00);
        $table->decimal('free_shipping_min_amount', 10, 2)->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('shipping_settings');
}
```

site_settings:
```php
public function up(): void
{
    Schema::create('site_settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
    });
}

public function down(): void
{
    Schema::dropIfExists('site_settings');
}
```

- [ ] **Step 9: Run all migrations**

```bash
php artisan migrate
```

Expected: All tables created successfully. No errors.

- [ ] **Step 10: Commit**

```bash
git add database/migrations/
git commit -m "feat: create commerce migrations (addresses, carts, orders, coupons, payments, points, settings)"
```

---

### Task 7: Create All Eloquent Models

**Files:**
- Create: `app/Models/Collection.php`
- Create: `app/Models/Category.php`
- Create: `app/Models/Product.php`
- Create: `app/Models/ProductColor.php`
- Create: `app/Models/ProductImage.php`
- Create: `app/Models/ProductVariant.php`
- Create: `app/Models/Address.php`
- Create: `app/Models/Cart.php`
- Create: `app/Models/CartItem.php`
- Create: `app/Models/Wishlist.php`
- Create: `app/Models/Order.php`
- Create: `app/Models/OrderItem.php`
- Create: `app/Models/PaymentSlip.php`
- Create: `app/Models/Coupon.php`
- Create: `app/Models/PointTransaction.php`
- Create: `app/Models/Banner.php`
- Create: `app/Models/ShippingSetting.php`
- Create: `app/Models/SiteSetting.php`

- [ ] **Step 1: Create Collection model**

```bash
php artisan make:model Collection
```

`app/Models/Collection.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'image', 'banner_image', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
```

- [ ] **Step 2: Create Category model**

```bash
php artisan make:model Category
```

`app/Models/Category.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'image', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
```

- [ ] **Step 3: Create Product model**

```bash
php artisan make:model Product
```

`app/Models/Product.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'collection_id', 'category_id',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }

    public function getMinPriceAttribute(): float
    {
        return $this->price;
    }
}
```

- [ ] **Step 4: Create ProductColor, ProductImage, ProductVariant models**

```bash
php artisan make:model ProductColor
php artisan make:model ProductImage
php artisan make:model ProductVariant
```

`app/Models/ProductColor.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductColor extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'name', 'color_code', 'sort_order'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
```

`app/Models/ProductImage.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'product_color_id', 'image_path', 'is_primary', 'sort_order'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }
}
```

`app/Models/ProductVariant.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'product_color_id', 'size', 'stock', 'sku'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
```

- [ ] **Step 5: Create Address model**

```bash
php artisan make:model Address
```

`app/Models/Address.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id', 'name', 'phone', 'address', 'district', 'province', 'postal_code', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->address} {$this->district} {$this->province} {$this->postal_code}";
    }
}
```

- [ ] **Step 6: Create Cart and CartItem models**

```bash
php artisan make:model Cart
php artisan make:model CartItem
```

`app/Models/Cart.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn (CartItem $item) => $item->product->price * $item->quantity);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
```

`app/Models/CartItem.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'product_variant_id', 'quantity'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getLineTotalAttribute(): float
    {
        return $this->product->price * $this->quantity;
    }
}
```

- [ ] **Step 7: Create Wishlist model**

```bash
php artisan make:model Wishlist
```

`app/Models/Wishlist.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = ['user_id', 'product_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

- [ ] **Step 8: Create Order and OrderItem models**

```bash
php artisan make:model Order
php artisan make:model OrderItem
```

`app/Models/Order.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'status', 'subtotal', 'shipping_fee', 'discount', 'total',
        'points_earned', 'points_used', 'coupon_id',
        'shipping_name', 'shipping_phone', 'shipping_address', 'shipping_district',
        'shipping_province', 'shipping_postal_code',
        'tracking_number', 'carrier_name', 'shipped_at', 'completed_at', 'cancelled_at', 'note',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function paymentSlip(): HasOne
    {
        return $this->hasOne(PaymentSlip::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $lastOrder = static::where('order_number', 'like', "CHO-{$date}-%")
            ->orderByDesc('order_number')
            ->first();

        $sequence = $lastOrder
            ? intval(substr($lastOrder->order_number, -4)) + 1
            : 1;

        return sprintf('CHO-%s-%04d', $date, $sequence);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'รอชำระเงิน',
            'awaiting_payment' => 'รอตรวจสอบ',
            'paid' => 'ชำระเงินแล้ว',
            'shipping' => 'กำลังจัดส่ง',
            'completed' => 'สำเร็จ',
            'cancelled' => 'ยกเลิก',
            default => $this->status,
        };
    }
}
```

`app/Models/OrderItem.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'product_variant_id',
        'product_name', 'color_name', 'size', 'price', 'quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getLineTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
```

- [ ] **Step 9: Create PaymentSlip model**

```bash
php artisan make:model PaymentSlip
```

`app/Models/PaymentSlip.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSlip extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id', 'image_path', 'uploaded_at', 'confirmed_at', 'confirmed_by', 'rejection_reason',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function confirmedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed_at !== null;
    }

    public function isRejected(): bool
    {
        return $this->rejection_reason !== null && $this->confirmed_at === null;
    }
}
```

- [ ] **Step 10: Create Coupon model**

```bash
php artisan make:model Coupon
```

`app/Models/Coupon.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'max_discount', 'min_order_amount',
        'max_uses', 'used_count', 'starts_at', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function isValid(float $orderAmount = 0): bool
    {
        if (! $this->is_active) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        if ($orderAmount < $this->min_order_amount) return false;

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'fixed') {
            return min($this->value, $subtotal);
        }

        $discount = $subtotal * ($this->value / 100);

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }
}
```

- [ ] **Step 11: Create PointTransaction model**

```bash
php artisan make:model PointTransaction
```

`app/Models/PointTransaction.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'points', 'type', 'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
```

- [ ] **Step 12: Create Banner, ShippingSetting, SiteSetting models**

```bash
php artisan make:model Banner
php artisan make:model ShippingSetting
php artisan make:model SiteSetting
```

`app/Models/Banner.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'image', 'link', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
```

`app/Models/ShippingSetting.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = ['shipping_fee', 'free_shipping_min_amount'];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
        'free_shipping_min_amount' => 'decimal:2',
    ];

    public static function current(): static
    {
        return static::first() ?? static::create(['shipping_fee' => 50.00]);
    }

    public function getShippingFeeFor(float $subtotal): float
    {
        if ($this->free_shipping_min_amount && $subtotal >= $this->free_shipping_min_amount) {
            return 0;
        }

        return (float) $this->shipping_fee;
    }
}
```

`app/Models/SiteSetting.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    public $timestamps = false;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null): ?string
    {
        return Cache::rememberForever("site_setting_{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting_{$key}");
    }
}
```

- [ ] **Step 13: Verify models compile**

```bash
php artisan tinker --execute="new App\Models\Product; echo 'OK';"
```

Expected: "OK" output.

- [ ] **Step 14: Commit**

```bash
git add app/Models/
git commit -m "feat: create all Eloquent models with relationships and business logic"
```

---

### Task 8: Create Database Seeders

**Files:**
- Create: `database/seeders/SiteSettingSeeder.php`
- Create: `database/seeders/ShippingSettingSeeder.php`
- Create: `database/seeders/AdminUserSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create SiteSettingSeeder**

```bash
php artisan make:seeder SiteSettingSeeder
```

`database/seeders/SiteSettingSeeder.php`:
```php
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
```

- [ ] **Step 2: Create ShippingSettingSeeder**

```bash
php artisan make:seeder ShippingSettingSeeder
```

`database/seeders/ShippingSettingSeeder.php`:
```php
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
```

- [ ] **Step 3: Create AdminUserSeeder**

```bash
php artisan make:seeder AdminUserSeeder
```

`database/seeders/AdminUserSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@chomin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'phone' => '0812345678',
                'email_verified_at' => now(),
            ]
        );
    }
}
```

- [ ] **Step 4: Update DatabaseSeeder**

`database/seeders/DatabaseSeeder.php`:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            ShippingSettingSeeder::class,
        ]);
    }
}
```

- [ ] **Step 5: Run seeders**

```bash
php artisan db:seed
```

Expected: Seeders run without errors. Admin user, site settings, and shipping settings created.

- [ ] **Step 6: Commit**

```bash
git add database/seeders/
git commit -m "feat: create seeders for admin user, site settings, and shipping"
```

---

### Task 9: Write Tests for Core Models

**Files:**
- Create: `tests/Unit/Models/CouponTest.php`
- Create: `tests/Unit/Models/OrderTest.php`
- Create: `tests/Unit/Models/SiteSettingTest.php`
- Create: `tests/Unit/Models/ShippingSettingTest.php`

- [ ] **Step 1: Create CouponTest**

```bash
php artisan make:test Models/CouponTest --unit
```

`tests/Unit/Models/CouponTest.php`:
```php
<?php

namespace Tests\Unit\Models;

use App\Models\Coupon;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    public function test_fixed_coupon_calculates_discount(): void
    {
        $coupon = new Coupon(['type' => 'fixed', 'value' => 100]);
        $this->assertEquals(100.00, $coupon->calculateDiscount(500));
    }

    public function test_fixed_coupon_does_not_exceed_subtotal(): void
    {
        $coupon = new Coupon(['type' => 'fixed', 'value' => 200]);
        $this->assertEquals(150.00, $coupon->calculateDiscount(150));
    }

    public function test_percent_coupon_calculates_discount(): void
    {
        $coupon = new Coupon(['type' => 'percent', 'value' => 10]);
        $this->assertEquals(50.00, $coupon->calculateDiscount(500));
    }

    public function test_percent_coupon_respects_max_discount(): void
    {
        $coupon = new Coupon(['type' => 'percent', 'value' => 50, 'max_discount' => 100]);
        $this->assertEquals(100.00, $coupon->calculateDiscount(500));
    }

    public function test_inactive_coupon_is_invalid(): void
    {
        $coupon = new Coupon(['is_active' => false]);
        $this->assertFalse($coupon->isValid());
    }

    public function test_coupon_below_min_order_is_invalid(): void
    {
        $coupon = new Coupon(['is_active' => true, 'min_order_amount' => 500]);
        $this->assertFalse($coupon->isValid(200));
    }
}
```

- [ ] **Step 2: Create OrderTest**

```bash
php artisan make:test Models/OrderTest --unit
```

`tests/Unit/Models/OrderTest.php`:
```php
<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_status_label_returns_thai_text(): void
    {
        $order = new Order(['status' => 'pending']);
        $this->assertEquals('รอชำระเงิน', $order->status_label);

        $order = new Order(['status' => 'completed']);
        $this->assertEquals('สำเร็จ', $order->status_label);

        $order = new Order(['status' => 'shipping']);
        $this->assertEquals('กำลังจัดส่ง', $order->status_label);
    }
}
```

- [ ] **Step 3: Run tests**

```bash
php artisan test tests/Unit/Models/
```

Expected: All tests pass.

- [ ] **Step 4: Commit**

```bash
git add tests/Unit/Models/
git commit -m "test: add unit tests for Coupon and Order model business logic"
```

---

### Task 10: Setup Filament Admin Panel and Shield

**Files:**
- Modify: `app/Models/User.php` (add FilamentUser, HasRoles)
- Create: `app/Providers/Filament/AdminPanelProvider.php` (already created by install)

- [ ] **Step 1: Add Filament traits to User model**

Add to `app/Models/User.php`:
```php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles, HasPanelShield;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['super_admin', 'staff']);
    }
```

- [ ] **Step 2: Setup Shield**

```bash
php artisan shield:install
```

When prompted, select the admin panel. This creates roles and permissions.

- [ ] **Step 3: Assign super_admin role to admin user**

```bash
php artisan shield:super-admin --user=admin@chomin.com
```

- [ ] **Step 4: Verify admin panel loads**

```bash
php artisan serve &
sleep 2
curl -s http://127.0.0.1:8000/admin/login | grep -o "Sign in"
kill %1
```

Expected: "Sign in" found — Filament login page renders.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "feat: configure Filament admin panel with Shield roles"
```

---

## Phase 2: Admin Panel (Filament Resources)

> Deliverable: Complete admin panel with all CRUD resources, dashboard widgets, settings page, and reports.

---

### Task 11: Create Collections Resource

**Files:**
- Create: `app/Filament/Resources/CollectionResource.php`
- Create: `app/Filament/Resources/CollectionResource/Pages/ListCollections.php`
- Create: `app/Filament/Resources/CollectionResource/Pages/CreateCollection.php`
- Create: `app/Filament/Resources/CollectionResource/Pages/EditCollection.php`

- [ ] **Step 1: Generate resource**

```bash
php artisan make:filament-resource Collection --generate
```

- [ ] **Step 2: Customize CollectionResource form and table**

`app/Filament/Resources/CollectionResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollectionResource\Pages;
use App\Models\Collection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CollectionResource extends Resource
{
    protected static ?string $model = Collection::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'สินค้า';
    protected static ?string $modelLabel = 'คอลเล็คชัน';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('ชื่อ')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label('รายละเอียด')
                    ->rows(3),
                Forms\Components\FileUpload::make('image')
                    ->label('รูปปก')
                    ->image()
                    ->directory('collections')
                    ->maxSize(5120),
                Forms\Components\FileUpload::make('banner_image')
                    ->label('รูป Banner')
                    ->image()
                    ->directory('collections/banners')
                    ->maxSize(5120),
                Forms\Components\Toggle::make('is_active')
                    ->label('เปิดใช้งาน')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('รูป')->circular(),
                Tables\Columns\TextColumn::make('name')->label('ชื่อ')->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('สินค้า')
                    ->counts('products'),
                Tables\Columns\IconColumn::make('is_active')->label('เปิด')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCollections::route('/'),
            'create' => Pages\CreateCollection::route('/create'),
            'edit' => Pages\EditCollection::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/CollectionResource* -A
git commit -m "feat: add Filament CollectionResource with CRUD"
```

---

### Task 12: Create Categories Resource

**Files:**
- Create: `app/Filament/Resources/CategoryResource.php` + pages

- [ ] **Step 1: Generate and customize**

```bash
php artisan make:filament-resource Category --generate
```

- [ ] **Step 2: Customize CategoryResource**

`app/Filament/Resources/CategoryResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'สินค้า';
    protected static ?string $modelLabel = 'หมวดหมู่';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label('ชื่อ')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\FileUpload::make('image')
                    ->label('รูป')
                    ->image()
                    ->directory('categories')
                    ->maxSize(5120),
                Forms\Components\Toggle::make('is_active')
                    ->label('เปิดใช้งาน')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('รูป')->circular(),
                Tables\Columns\TextColumn::make('name')->label('ชื่อ')->searchable(),
                Tables\Columns\TextColumn::make('products_count')
                    ->label('สินค้า')
                    ->counts('products'),
                Tables\Columns\IconColumn::make('is_active')->label('เปิด')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/CategoryResource* -A
git commit -m "feat: add Filament CategoryResource with CRUD"
```

---

### Task 13: Create Products Resource with Relation Managers

**Files:**
- Create: `app/Filament/Resources/ProductResource.php` + pages
- Create: `app/Filament/Resources/ProductResource/RelationManagers/ColorsRelationManager.php`
- Create: `app/Filament/Resources/ProductResource/RelationManagers/VariantsRelationManager.php`
- Create: `app/Filament/Resources/ProductResource/RelationManagers/ImagesRelationManager.php`

- [ ] **Step 1: Generate resource**

```bash
php artisan make:filament-resource Product --generate
```

- [ ] **Step 2: Customize ProductResource**

`app/Filament/Resources/ProductResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'สินค้า';
    protected static ?string $modelLabel = 'สินค้า';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('ข้อมูลสินค้า')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('ชื่อสินค้า')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('price')
                    ->label('ราคา (บาท)')
                    ->required()
                    ->numeric()
                    ->prefix('฿'),
                Forms\Components\Select::make('collection_id')
                    ->label('คอลเล็คชัน')
                    ->relationship('collection', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('category_id')
                    ->label('หมวดหมู่')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\RichEditor::make('description')
                    ->label('รายละเอียด')
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('ตั้งค่า')->schema([
                Forms\Components\Toggle::make('is_active')
                    ->label('เปิดขาย')
                    ->default(true),
                Forms\Components\Toggle::make('is_featured')
                    ->label('สินค้าแนะนำ'),
                Forms\Components\TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('primaryImage.image_path')
                    ->label('รูป')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อ')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('price')
                    ->label('ราคา')
                    ->money('THB')
                    ->sortable(),
                Tables\Columns\TextColumn::make('collection.name')
                    ->label('คอลเล็คชัน')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('หมวดหมู่')
                    ->sortable(),
                Tables\Columns\TextColumn::make('variants_sum_stock')
                    ->label('Stock')
                    ->sum('variants', 'stock')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('เปิด')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('แนะนำ')
                    ->boolean(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('collection_id')
                    ->label('คอลเล็คชัน')
                    ->relationship('collection', 'name'),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('หมวดหมู่')
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('สถานะ'),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('แนะนำ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ColorsRelationManager::class,
            RelationManagers\ImagesRelationManager::class,
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Create ColorsRelationManager**

```bash
php artisan make:filament-relation-manager ProductResource colors name
```

`app/Filament/Resources/ProductResource/RelationManagers/ColorsRelationManager.php`:
```php
<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ColorsRelationManager extends RelationManager
{
    protected static string $relationship = 'colors';
    protected static ?string $title = 'สี';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('ชื่อสี')
                ->required(),
            Forms\Components\ColorPicker::make('color_code')
                ->label('รหัสสี')
                ->required(),
            Forms\Components\TextInput::make('sort_order')
                ->label('ลำดับ')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ColorColumn::make('color_code')->label('สี'),
                Tables\Columns\TextColumn::make('name')->label('ชื่อ'),
                Tables\Columns\TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

- [ ] **Step 4: Create ImagesRelationManager**

```bash
php artisan make:filament-relation-manager ProductResource images image_path
```

`app/Filament/Resources/ProductResource/RelationManagers/ImagesRelationManager.php`:
```php
<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';
    protected static ?string $title = 'รูปภาพ';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_color_id')
                ->label('สี')
                ->relationship('color', 'name')
                ->required(),
            Forms\Components\FileUpload::make('image_path')
                ->label('รูป')
                ->image()
                ->directory('products')
                ->maxSize(5120)
                ->required(),
            Forms\Components\Toggle::make('is_primary')
                ->label('รูปหลัก'),
            Forms\Components\TextInput::make('sort_order')
                ->label('ลำดับ')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')->label('รูป'),
                Tables\Columns\TextColumn::make('color.name')->label('สี'),
                Tables\Columns\IconColumn::make('is_primary')->label('หลัก')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

- [ ] **Step 5: Create VariantsRelationManager**

```bash
php artisan make:filament-relation-manager ProductResource variants size
```

`app/Filament/Resources/ProductResource/RelationManagers/VariantsRelationManager.php`:
```php
<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';
    protected static ?string $title = 'ตัวเลือก (Size + Stock)';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('product_color_id')
                ->label('สี')
                ->relationship('color', 'name')
                ->required(),
            Forms\Components\Select::make('size')
                ->label('Size')
                ->options([
                    'XS' => 'XS',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                    'XXL' => 'XXL',
                    'Free Size' => 'Free Size',
                ])
                ->required(),
            Forms\Components\TextInput::make('stock')
                ->label('จำนวน Stock')
                ->numeric()
                ->required()
                ->default(0),
            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->unique(ignoreRecord: true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('color.name')->label('สี'),
                Tables\Columns\TextColumn::make('size')->label('Size'),
                Tables\Columns\TextColumn::make('stock')->label('Stock')->sortable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
```

- [ ] **Step 6: Commit**

```bash
git add app/Filament/Resources/ProductResource* -A
git commit -m "feat: add ProductResource with colors, images, variants relation managers"
```

---

### Task 14: Create Orders Resource with Actions

**Files:**
- Create: `app/Filament/Resources/OrderResource.php` + pages

- [ ] **Step 1: Generate resource**

```bash
php artisan make:filament-resource Order --generate --view
```

- [ ] **Step 2: Customize OrderResource**

`app/Filament/Resources/OrderResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'การขาย';
    protected static ?string $modelLabel = 'ออเดอร์';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::$model::where('status', 'awaiting_payment')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('จัดส่ง')->schema([
                Forms\Components\TextInput::make('tracking_number')
                    ->label('เลข Tracking'),
                Forms\Components\TextInput::make('carrier_name')
                    ->label('ชื่อขนส่ง'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('เลขออเดอร์')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('ลูกค้า')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('ยอดรวม')
                    ->money('THB')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('สถานะ')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'awaiting_payment',
                        'success' => 'paid',
                        'primary' => 'shipping',
                        'gray' => 'completed',
                        'danger' => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => (new Order(['status' => $state]))->status_label),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('วันที่สั่ง')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'pending' => 'รอชำระเงิน',
                        'awaiting_payment' => 'รอตรวจสอบ',
                        'paid' => 'ชำระเงินแล้ว',
                        'shipping' => 'กำลังจัดส่ง',
                        'completed' => 'สำเร็จ',
                        'cancelled' => 'ยกเลิก',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('อนุมัติ')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'awaiting_payment')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update(['status' => 'paid']);
                        if ($record->paymentSlip) {
                            $record->paymentSlip->update([
                                'confirmed_at' => now(),
                                'confirmed_by' => auth()->id(),
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('ปฏิเสธ')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Order $record) => $record->status === 'awaiting_payment')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('เหตุผล')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update(['status' => 'pending']);
                        if ($record->paymentSlip) {
                            $record->paymentSlip->update([
                                'rejection_reason' => $data['rejection_reason'],
                            ]);
                        }
                    }),
                Tables\Actions\Action::make('ship')
                    ->label('จัดส่ง')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (Order $record) => $record->status === 'paid')
                    ->form([
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('เลข Tracking')
                            ->required(),
                        Forms\Components\TextInput::make('carrier_name')
                            ->label('ชื่อขนส่ง')
                            ->required(),
                    ])
                    ->action(function (Order $record, array $data) {
                        $record->update([
                            'status' => 'shipping',
                            'tracking_number' => $data['tracking_number'],
                            'carrier_name' => $data['carrier_name'],
                            'shipped_at' => now(),
                        ]);
                    }),
                Tables\Actions\Action::make('complete')
                    ->label('สำเร็จ')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->visible(fn (Order $record) => $record->status === 'shipping')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                        // Points earning logic will be added in Phase 6
                    }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('ข้อมูลออเดอร์')->schema([
                Infolists\Components\TextEntry::make('order_number')->label('เลขออเดอร์'),
                Infolists\Components\TextEntry::make('status_label')->label('สถานะ'),
                Infolists\Components\TextEntry::make('created_at')->label('วันที่สั่ง')->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('user.name')->label('ลูกค้า'),
                Infolists\Components\TextEntry::make('user.email')->label('อีเมล'),
            ])->columns(3),

            Infolists\Components\Section::make('ที่อยู่จัดส่ง')->schema([
                Infolists\Components\TextEntry::make('shipping_name')->label('ชื่อ'),
                Infolists\Components\TextEntry::make('shipping_phone')->label('เบอร์โทร'),
                Infolists\Components\TextEntry::make('shipping_address')->label('ที่อยู่'),
                Infolists\Components\TextEntry::make('shipping_district')->label('เขต/อำเภอ'),
                Infolists\Components\TextEntry::make('shipping_province')->label('จังหวัด'),
                Infolists\Components\TextEntry::make('shipping_postal_code')->label('รหัสไปรษณีย์'),
            ])->columns(3),

            Infolists\Components\Section::make('สรุปยอด')->schema([
                Infolists\Components\TextEntry::make('subtotal')->label('ยอดสินค้า')->money('THB'),
                Infolists\Components\TextEntry::make('discount')->label('ส่วนลด')->money('THB'),
                Infolists\Components\TextEntry::make('shipping_fee')->label('ค่าส่ง')->money('THB'),
                Infolists\Components\TextEntry::make('total')->label('รวมทั้งหมด')->money('THB'),
                Infolists\Components\TextEntry::make('points_used')->label('แต้มที่ใช้'),
                Infolists\Components\TextEntry::make('points_earned')->label('แต้มที่ได้'),
            ])->columns(3),

            Infolists\Components\Section::make('การจัดส่ง')->schema([
                Infolists\Components\TextEntry::make('tracking_number')->label('Tracking'),
                Infolists\Components\TextEntry::make('carrier_name')->label('ขนส่ง'),
                Infolists\Components\TextEntry::make('shipped_at')->label('วันที่ส่ง')->dateTime('d/m/Y H:i'),
            ])->columns(3)->visible(fn (Order $record) => $record->tracking_number !== null),

            Infolists\Components\Section::make('สลิปชำระเงิน')->schema([
                Infolists\Components\ImageEntry::make('paymentSlip.image_path')->label('สลิป'),
                Infolists\Components\TextEntry::make('paymentSlip.uploaded_at')->label('อัปโหลดเมื่อ')->dateTime('d/m/Y H:i'),
                Infolists\Components\TextEntry::make('paymentSlip.rejection_reason')->label('เหตุผลปฏิเสธ'),
            ])->columns(3)->visible(fn (Order $record) => $record->paymentSlip !== null),

            Infolists\Components\TextEntry::make('note')->label('หมายเหตุ'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Resources/OrderResource* -A
git commit -m "feat: add OrderResource with approve/reject/ship/complete actions"
```

---

### Task 15: Create Remaining Admin Resources (Users, Coupons, Banners)

**Files:**
- Create: `app/Filament/Resources/UserResource.php` + pages
- Create: `app/Filament/Resources/CouponResource.php` + pages
- Create: `app/Filament/Resources/BannerResource.php` + pages

- [ ] **Step 1: Generate all three resources**

```bash
php artisan make:filament-resource User --generate
php artisan make:filament-resource Coupon --generate
php artisan make:filament-resource Banner --generate
```

- [ ] **Step 2: Customize UserResource**

`app/Filament/Resources/UserResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\PointTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'การขาย';
    protected static ?string $modelLabel = 'สมาชิก';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('ชื่อ')->required(),
            Forms\Components\TextInput::make('email')->label('อีเมล')->email()->required(),
            Forms\Components\TextInput::make('phone')->label('เบอร์โทร'),
            Forms\Components\TextInput::make('points')->label('แต้มสะสม')->numeric()->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('ชื่อ')->searchable(),
                Tables\Columns\TextColumn::make('email')->label('อีเมล')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('เบอร์โทร'),
                Tables\Columns\TextColumn::make('points')->label('แต้ม')->sortable(),
                Tables\Columns\TextColumn::make('orders_count')->label('ออเดอร์')->counts('orders'),
                Tables\Columns\TextColumn::make('created_at')->label('สมัครเมื่อ')->dateTime('d/m/Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('adjustPoints')
                    ->label('ปรับแต้ม')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\TextInput::make('points')
                            ->label('จำนวนแต้ม (บวก/ลบ)')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('description')
                            ->label('เหตุผล')
                            ->required(),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->increment('points', $data['points']);
                        PointTransaction::create([
                            'user_id' => $record->id,
                            'points' => $data['points'],
                            'type' => 'adjust',
                            'description' => $data['description'],
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
```

- [ ] **Step 3: Customize CouponResource**

`app/Filament/Resources/CouponResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'การขาย';
    protected static ?string $modelLabel = 'คูปอง';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('code')
                    ->label('รหัสคูปอง')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('type')
                    ->label('ประเภท')
                    ->options(['fixed' => 'ลดเป็นจำนวนเงิน', 'percent' => 'ลดเป็น %'])
                    ->required()
                    ->live(),
                Forms\Components\TextInput::make('value')
                    ->label('มูลค่า')
                    ->numeric()
                    ->required()
                    ->suffix(fn (Forms\Get $get) => $get('type') === 'percent' ? '%' : '฿'),
                Forms\Components\TextInput::make('max_discount')
                    ->label('ลดสูงสุด (บาท)')
                    ->numeric()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'percent'),
                Forms\Components\TextInput::make('min_order_amount')
                    ->label('ยอดขั้นต่ำ (บาท)')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('max_uses')
                    ->label('จำกัดจำนวนครั้ง')
                    ->numeric()
                    ->helperText('ไม่กรอก = ไม่จำกัด'),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('เริ่มใช้'),
                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('หมดอายุ'),
                Forms\Components\Toggle::make('is_active')
                    ->label('เปิดใช้งาน')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('รหัส')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('ประเภท')
                    ->formatStateUsing(fn ($state) => $state === 'fixed' ? 'จำนวนเงิน' : '%'),
                Tables\Columns\TextColumn::make('value')->label('มูลค่า'),
                Tables\Columns\TextColumn::make('used_count')->label('ใช้แล้ว'),
                Tables\Columns\TextColumn::make('max_uses')->label('จำกัด'),
                Tables\Columns\TextColumn::make('expires_at')->label('หมดอายุ')->dateTime('d/m/Y'),
                Tables\Columns\IconColumn::make('is_active')->label('เปิด')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 4: Customize BannerResource**

`app/Filament/Resources/BannerResource.php`:
```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'เนื้อหา';
    protected static ?string $modelLabel = 'แบนเนอร์';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('title')->label('หัวข้อ'),
                Forms\Components\TextInput::make('subtitle')->label('หัวข้อรอง'),
                Forms\Components\FileUpload::make('image')
                    ->label('รูป')
                    ->image()
                    ->directory('banners')
                    ->maxSize(5120)
                    ->required(),
                Forms\Components\TextInput::make('link')->label('ลิงก์')->url(),
                Forms\Components\Toggle::make('is_active')->label('เปิดใช้งาน')->default(true),
                Forms\Components\TextInput::make('sort_order')->label('ลำดับ')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('รูป'),
                Tables\Columns\TextColumn::make('title')->label('หัวข้อ'),
                Tables\Columns\IconColumn::make('is_active')->label('เปิด')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('ลำดับ')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
```

- [ ] **Step 5: Commit**

```bash
git add app/Filament/Resources/ -A
git commit -m "feat: add User, Coupon, Banner Filament resources"
```

---

### Task 16: Create Dashboard Widgets

**Files:**
- Create: `app/Filament/Widgets/SalesOverview.php`
- Create: `app/Filament/Widgets/PendingPayments.php`
- Create: `app/Filament/Widgets/LowStockProducts.php`
- Create: `app/Filament/Widgets/SalesChart.php`

- [ ] **Step 1: Create SalesOverview stat widget**

```bash
php artisan make:filament-widget SalesOverview --stats-overview
```

`app/Filament/Widgets/SalesOverview.php`:
```php
<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('ยอดขายวันนี้', '฿' . number_format(
                Order::where('status', 'completed')
                    ->whereDate('completed_at', today())
                    ->sum('total'), 2
            )),
            Stat::make('ยอดขายเดือนนี้', '฿' . number_format(
                Order::where('status', 'completed')
                    ->whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year)
                    ->sum('total'), 2
            )),
            Stat::make('รอตรวจสลิป', Order::where('status', 'awaiting_payment')->count())
                ->color('warning'),
            Stat::make('สมาชิกใหม่เดือนนี้', User::whereMonth('created_at', now()->month)->count()),
        ];
    }
}
```

- [ ] **Step 2: Create SalesChart widget**

```bash
php artisan make:filament-widget SalesChart --chart
```

`app/Filament/Widgets/SalesChart.php`:
```php
<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class SalesChart extends ChartWidget
{
    protected static ?string $heading = 'ยอดขายรายวัน (30 วันล่าสุด)';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::today()->subDays($daysAgo);
            return [
                'date' => $date->format('d/m'),
                'total' => Order::where('status', 'completed')
                    ->whereDate('completed_at', $date)
                    ->sum('total'),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'ยอดขาย (บาท)',
                    'data' => $data->pluck('total')->toArray(),
                    'borderColor' => '#000000',
                    'backgroundColor' => 'rgba(0,0,0,0.1)',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Widgets/
git commit -m "feat: add dashboard widgets (sales overview, sales chart)"
```

---

### Task 17: Create Settings Page

**Files:**
- Create: `app/Filament/Pages/SiteSettings.php`

- [ ] **Step 1: Create custom Filament page**

```bash
php artisan make:filament-page SiteSettings
```

`app/Filament/Pages/SiteSettings.php`:
```php
<?php

namespace App\Filament\Pages;

use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'ตั้งค่าร้าน';
    protected static ?string $title = 'ตั้งค่าร้าน';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $shipping = ShippingSetting::current();

        $this->form->fill([
            'site_name' => SiteSetting::get('site_name'),
            'site_phone' => SiteSetting::get('site_phone'),
            'site_email' => SiteSetting::get('site_email'),
            'site_address' => SiteSetting::get('site_address'),
            'promptpay_id' => SiteSetting::get('promptpay_id'),
            'promptpay_name' => SiteSetting::get('promptpay_name'),
            'promptpay_qr' => SiteSetting::get('promptpay_qr'),
            'points_per_baht' => SiteSetting::get('points_per_baht', '100'),
            'points_to_baht' => SiteSetting::get('points_to_baht', '10'),
            'shipping_fee' => $shipping->shipping_fee,
            'free_shipping_min_amount' => $shipping->free_shipping_min_amount,
            'about_content' => SiteSetting::get('about_content'),
            'footer_quote' => SiteSetting::get('footer_quote'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('ข้อมูลร้าน')->schema([
                Forms\Components\TextInput::make('site_name')->label('ชื่อร้าน'),
                Forms\Components\TextInput::make('site_phone')->label('เบอร์โทร'),
                Forms\Components\TextInput::make('site_email')->label('อีเมล')->email(),
                Forms\Components\Textarea::make('site_address')->label('ที่อยู่ร้าน'),
            ])->columns(2),

            Forms\Components\Section::make('PromptPay')->schema([
                Forms\Components\TextInput::make('promptpay_id')->label('หมายเลข PromptPay'),
                Forms\Components\TextInput::make('promptpay_name')->label('ชื่อบัญชี'),
                Forms\Components\FileUpload::make('promptpay_qr')
                    ->label('QR Code')
                    ->image()
                    ->directory('settings'),
            ])->columns(2),

            Forms\Components\Section::make('ค่าจัดส่ง')->schema([
                Forms\Components\TextInput::make('shipping_fee')
                    ->label('ค่าส่ง (บาท)')
                    ->numeric(),
                Forms\Components\TextInput::make('free_shipping_min_amount')
                    ->label('ฟรีค่าส่งเมื่อซื้อครบ (บาท)')
                    ->numeric(),
            ])->columns(2),

            Forms\Components\Section::make('แต้มสะสม')->schema([
                Forms\Components\TextInput::make('points_per_baht')
                    ->label('ทุกกี่บาทได้ 1 แต้ม')
                    ->numeric(),
                Forms\Components\TextInput::make('points_to_baht')
                    ->label('กี่แต้มแลก 1 บาท')
                    ->numeric(),
            ])->columns(2),

            Forms\Components\Section::make('เนื้อหา')->schema([
                Forms\Components\RichEditor::make('about_content')
                    ->label('หน้าเกี่ยวกับเรา'),
                Forms\Components\Textarea::make('footer_quote')
                    ->label('ข้อความ Quote หน้าแรก'),
            ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $siteKeys = [
            'site_name', 'site_phone', 'site_email', 'site_address',
            'promptpay_id', 'promptpay_name', 'promptpay_qr',
            'points_per_baht', 'points_to_baht',
            'about_content', 'footer_quote',
        ];

        foreach ($siteKeys as $key) {
            SiteSetting::set($key, $data[$key] ?? null);
        }

        $shipping = ShippingSetting::current();
        $shipping->update([
            'shipping_fee' => $data['shipping_fee'] ?? 50,
            'free_shipping_min_amount' => $data['free_shipping_min_amount'],
        ]);

        Notification::make()
            ->title('บันทึกตั้งค่าเรียบร้อย')
            ->success()
            ->send();
    }
}
```

- [ ] **Step 2: Create the Blade view for settings page**

Create `resources/views/filament/pages/site-settings.blade.php`:
```blade
<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit">
                บันทึก
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
```

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Pages/ resources/views/filament/
git commit -m "feat: add site settings page in admin panel"
```

---

## Phase 3: Frontend Storefront (Layout + Pages)

> Deliverable: Complete storefront with all public pages matching the premium design from the reference image.

---

### Task 18: Create Frontend Layout

**Files:**
- Create: `resources/views/layouts/app.blade.php` (replace Breeze default)
- Create: `resources/views/components/navbar.blade.php`
- Create: `resources/views/components/footer.blade.php`
- Create: `resources/views/components/product-card.blade.php`

- [ ] **Step 1: Create main layout**

`resources/views/layouts/app.blade.php`:
```blade
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CHOMIN' }} | {{ \App\Models\SiteSetting::get('site_name', 'CHOMIN') }}</title>
    @if(isset($meta_description))
    <meta name="description" content="{{ $meta_description }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-white text-brand-black" x-data="{ mobileMenu: false, cartOpen: false }">
    @include('components.navbar')

    <main>
        {{ $slot }}
    </main>

    @include('components.footer')

    @stack('scripts')
</body>
</html>
```

- [ ] **Step 2: Create navbar component**

`resources/views/components/navbar.blade.php`:
```blade
<nav class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-brand-gray-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            {{-- Logo --}}
            <a href="/" class="text-xl font-bold tracking-wider">CHOMIN</a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center space-x-8 text-sm">
                <a href="/" class="hover:text-brand-brown transition">หน้าแรก</a>
                <a href="{{ route('collections.index') }}" class="hover:text-brand-brown transition">คอลเล็คชัน</a>
                <a href="{{ route('shop.index') }}" class="hover:text-brand-brown transition">ร้าน</a>
                <a href="{{ route('about') }}" class="hover:text-brand-brown transition">เกี่ยวกับเรา</a>
            </div>

            {{-- Right Icons --}}
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('profile.index') }}" class="hover:text-brand-brown">
                        <x-heroicon-o-user class="w-5 h-5" />
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-brand-brown">
                        <x-heroicon-o-user class="w-5 h-5" />
                    </a>
                @endauth

                <a href="{{ route('cart.index') }}" class="relative hover:text-brand-brown">
                    <x-heroicon-o-shopping-bag class="w-5 h-5" />
                    <span id="cart-count"
                          class="absolute -top-2 -right-2 bg-brand-black text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"
                          style="display: {{ session('cart_count', 0) > 0 ? 'flex' : 'none' }}">
                        {{ session('cart_count', 0) }}
                    </span>
                </a>

                {{-- Mobile menu button --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenu" x-transition @click.away="mobileMenu = false"
         class="md:hidden bg-white border-t border-brand-gray-border">
        <div class="px-4 py-4 space-y-3">
            <a href="/" class="block">หน้าแรก</a>
            <a href="{{ route('collections.index') }}" class="block">คอลเล็คชัน</a>
            <a href="{{ route('shop.index') }}" class="block">ร้าน</a>
            <a href="{{ route('about') }}" class="block">เกี่ยวกับเรา</a>
        </div>
    </div>
</nav>
<div class="h-16"></div> {{-- Spacer for fixed nav --}}
```

- [ ] **Step 3: Create footer component**

`resources/views/components/footer.blade.php`:
```blade
<footer class="bg-brand-black text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="font-bold text-lg mb-4">CHOMIN</h3>
                <p class="text-sm text-gray-400">{{ \App\Models\SiteSetting::get('site_address') }}</p>
            </div>
            <div>
                <h4 class="font-semibold mb-4">เมนู</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="/" class="hover:text-white transition">หน้าแรก</a></li>
                    <li><a href="{{ route('collections.index') }}" class="hover:text-white transition">คอลเล็คชัน</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">ร้าน</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">ติดต่อเรา</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>{{ \App\Models\SiteSetting::get('site_phone') }}</li>
                    <li>{{ \App\Models\SiteSetting::get('site_email') }}</li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4">นโยบาย</h4>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-white transition">นโยบายความเป็นส่วนตัว</a></li>
                    <li><a href="#" class="hover:text-white transition">เงื่อนไขการใช้บริการ</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
            &copy; {{ date('Y') }} CHOMIN. All rights reserved.
        </div>
    </div>
</footer>
```

- [ ] **Step 4: Create product card component**

`resources/views/components/product-card.blade.php`:
```blade
@props(['product'])

<a href="{{ route('products.show', $product->slug) }}" class="group block">
    <div class="aspect-[3/4] overflow-hidden bg-brand-gray">
        @if($product->primaryImage)
            <img src="{{ Storage::url($product->primaryImage->image_path) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-brand-gray-light">
                No Image
            </div>
        @endif
    </div>
    <div class="mt-3">
        <h3 class="text-sm font-medium truncate">{{ $product->name }}</h3>
        <p class="text-sm text-brand-gray-medium mt-1">฿{{ number_format($product->price, 2) }}</p>
    </div>
</a>
```

- [ ] **Step 5: Commit**

```bash
git add resources/views/layouts/ resources/views/components/
git commit -m "feat: create frontend layout with navbar, footer, product card"
```

---

### Task 19: Create Routes and Controllers for Public Pages

**Files:**
- Create: `app/Http/Controllers/HomeController.php`
- Create: `app/Http/Controllers/CollectionController.php`
- Create: `app/Http/Controllers/ShopController.php`
- Create: `app/Http/Controllers/ProductController.php`
- Create: `app/Http/Controllers/AboutController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create all controllers**

```bash
php artisan make:controller HomeController
php artisan make:controller CollectionController
php artisan make:controller ShopController
php artisan make:controller ProductController
php artisan make:controller AboutController
```

- [ ] **Step 2: Implement HomeController**

`app/Http/Controllers/HomeController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Collection;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function __invoke()
    {
        $banners = Banner::active()->ordered()->get();
        $collections = Collection::active()->ordered()
            ->with(['products' => fn ($q) => $q->active()->with('primaryImage')->limit(6)])
            ->get();
        $quote = SiteSetting::get('footer_quote');

        return view('pages.home', compact('banners', 'collections', 'quote'));
    }
}
```

- [ ] **Step 3: Implement CollectionController**

`app/Http/Controllers/CollectionController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::active()->ordered()->get();
        return view('pages.collections.index', compact('collections'));
    }

    public function show(Collection $collection)
    {
        abort_unless($collection->is_active, 404);

        $categories = Category::active()->ordered()->get();
        $products = $collection->products()
            ->active()
            ->with('primaryImage', 'colors')
            ->when(request('category'), fn ($q) => $q->where('category_id', request('category')))
            ->when(request('sort'), fn ($q) => match (request('sort')) {
                'price_asc' => $q->orderBy('price'),
                'price_desc' => $q->orderByDesc('price'),
                'newest' => $q->orderByDesc('created_at'),
                default => $q->orderBy('sort_order'),
            }, fn ($q) => $q->orderBy('sort_order'))
            ->paginate(12);

        return view('pages.collections.show', compact('collection', 'products', 'categories'));
    }
}
```

- [ ] **Step 4: Implement ShopController**

`app/Http/Controllers/ShopController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;

class ShopController extends Controller
{
    public function __invoke()
    {
        $categories = Category::active()->ordered()->get();
        $collections = Collection::active()->ordered()->get();

        $products = Product::active()
            ->with('primaryImage', 'colors')
            ->when(request('category'), fn ($q) => $q->where('category_id', request('category')))
            ->when(request('collection'), fn ($q) => $q->where('collection_id', request('collection')))
            ->when(request('sort'), fn ($q) => match (request('sort')) {
                'price_asc' => $q->orderBy('price'),
                'price_desc' => $q->orderByDesc('price'),
                'newest' => $q->orderByDesc('created_at'),
                default => $q->orderBy('sort_order'),
            }, fn ($q) => $q->orderBy('sort_order'))
            ->paginate(12);

        return view('pages.shop', compact('products', 'categories', 'collections'));
    }
}
```

- [ ] **Step 5: Implement ProductController**

`app/Http/Controllers/ProductController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['colors.images', 'colors.variants', 'collection']);

        $related = Product::active()
            ->where('collection_id', $product->collection_id)
            ->where('id', '!=', $product->id)
            ->with('primaryImage')
            ->limit(4)
            ->get();

        return view('pages.products.show', compact('product', 'related'));
    }
}
```

- [ ] **Step 6: Implement AboutController**

`app/Http/Controllers/AboutController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class AboutController extends Controller
{
    public function __invoke()
    {
        $content = SiteSetting::get('about_content');
        return view('pages.about', compact('content'));
    }
}
```

- [ ] **Step 7: Add routes**

Add to `routes/web.php`:
```php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AboutController;

Route::get('/', HomeController::class)->name('home');
Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])->name('collections.show');
Route::get('/shop', ShopController::class)->name('shop.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/about', AboutController::class)->name('about');
```

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/ routes/web.php
git commit -m "feat: create controllers and routes for all public pages"
```

---

### Task 20: Create Frontend Blade Views (Home + Collections + Shop + Product Detail + About)

**Files:**
- Create: `resources/views/pages/home.blade.php`
- Create: `resources/views/pages/collections/index.blade.php`
- Create: `resources/views/pages/collections/show.blade.php`
- Create: `resources/views/pages/shop.blade.php`
- Create: `resources/views/pages/products/show.blade.php`
- Create: `resources/views/pages/about.blade.php`

- [ ] **Step 1: Create home page** — Full-width hero banner, collection sections with product cards matching the reference design (MIDNIGHT SERIES dark bg, URBAN WEEKEND white bg, SIGNATURE ACCESSORIES gray bg), quote section.

- [ ] **Step 2: Create collections index** — Grid of collection cards with cover images.

- [ ] **Step 3: Create collection show page** — Collection banner, filter sidebar (category, sort), product grid with pagination.

- [ ] **Step 4: Create shop page** — All products grid with category/collection filter and sort.

- [ ] **Step 5: Create product detail page** — Image gallery with Alpine.js color switcher, size selector, stock display, add-to-cart button, wishlist button, related products.

- [ ] **Step 6: Create about page** — Render `about_content` from site_settings.

- [ ] **Step 7: Build and test**

```bash
npm run build
php artisan serve
```

Verify: Visit homepage, collections, shop, product detail. All pages render correctly.

- [ ] **Step 8: Commit**

```bash
git add resources/views/pages/
git commit -m "feat: create all frontend Blade views matching premium design"
```

Note: Each view file will be 50-150 lines of Blade/Tailwind. The executing agent should create complete, pixel-accurate views following the design direction (minimal luxury, black/white/brown, full-width sections). Reference the design spec for exact color codes and typography.

---

## Phase 4: Cart & Checkout

> Deliverable: Working cart system (session + DB), checkout flow with PromptPay payment and slip upload.

---

### Task 21: Create Cart Service

**Files:**
- Create: `app/Services/CartService.php`
- Create: `app/Http/Controllers/CartController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create CartService**

`app/Services/CartService.php`:
```php
<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => null]
            );
        }

        $sessionId = session()->getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => null]
        );
    }

    public function addItem(int $variantId, int $quantity = 1): CartItem
    {
        $cart = $this->getCart();
        $variant = ProductVariant::findOrFail($variantId);

        $existing = $cart->items()
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $quantity;
            abort_if($newQty > $variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');
            $existing->update(['quantity' => $newQty]);
            return $existing->fresh();
        }

        abort_if($quantity > $variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');

        return $cart->items()->create([
            'product_id' => $variant->product_id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        $cart = $this->getCart();
        $item = $cart->items()->findOrFail($itemId);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $variant = $item->variant;
        abort_if($quantity > $variant->stock, 422, 'สินค้าในสต็อกไม่เพียงพอ');

        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->getCart();
        $cart->items()->findOrFail($itemId)->delete();
    }

    public function mergeSessionCart(): void
    {
        if (! Auth::check()) return;

        $sessionCart = Cart::where('session_id', session()->getId())->first();
        if (! $sessionCart || $sessionCart->items->isEmpty()) return;

        $userCart = Cart::firstOrCreate(
            ['user_id' => Auth::id()],
            ['session_id' => null]
        );

        foreach ($sessionCart->items as $item) {
            $existing = $userCart->items()
                ->where('product_variant_id', $item->product_variant_id)
                ->first();

            if ($existing) {
                $existing->update([
                    'quantity' => min($existing->quantity + $item->quantity, $item->variant->stock),
                ]);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }

        $sessionCart->items()->delete();
        $sessionCart->delete();
    }

    public function clear(): void
    {
        $cart = $this->getCart();
        $cart->items()->delete();
    }

    public function getItemCount(): int
    {
        return $this->getCart()->items->sum('quantity');
    }
}
```

- [ ] **Step 2: Create CartController**

`app/Http/Controllers/CartController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product.primaryImage', 'items.variant.color');

        return view('pages.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $this->cartService->addItem($request->variant_id, $request->quantity);

        return back()->with('success', 'เพิ่มสินค้าลงตะกร้าแล้ว');
    }

    public function update(Request $request, int $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:0']);
        $this->cartService->updateQuantity($itemId, $request->quantity);

        return back()->with('success', 'อัปเดตตะกร้าแล้ว');
    }

    public function remove(int $itemId)
    {
        $this->cartService->removeItem($itemId);
        return back()->with('success', 'ลบสินค้าออกจากตะกร้าแล้ว');
    }
}
```

- [ ] **Step 3: Add cart routes**

Add to `routes/web.php`:
```php
use App\Http\Controllers\CartController;

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
```

- [ ] **Step 4: Commit**

```bash
git add app/Services/ app/Http/Controllers/CartController.php routes/web.php
git commit -m "feat: create CartService and CartController with session/DB cart"
```

---

### Task 22: Create Checkout Flow

**Files:**
- Create: `app/Services/OrderService.php`
- Create: `app/Http/Controllers/CheckoutController.php`
- Create: `resources/views/pages/checkout.blade.php`
- Create: `resources/views/pages/checkout-success.blade.php`

- [ ] **Step 1: Create OrderService**

`app/Services/OrderService.php`:
```php
<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, $cart, ?Coupon $coupon = null, int $pointsUsed = 0): Order
    {
        return DB::transaction(function () use ($data, $cart, $coupon, $pointsUsed) {
            $cart->load('items.product', 'items.variant.color');

            // Verify stock with lock
            foreach ($cart->items as $item) {
                $variant = $item->variant()->lockForUpdate()->first();
                abort_if(
                    $variant->stock < $item->quantity,
                    422,
                    "สินค้า {$item->product->name} ({$variant->color->name} / {$variant->size}) สต็อกไม่เพียงพอ"
                );
            }

            $subtotal = $cart->items->sum(fn ($item) => $item->product->price * $item->quantity);

            // Shipping
            $shipping = ShippingSetting::current();
            $shippingFee = $shipping->getShippingFeeFor($subtotal);

            // Coupon discount
            $couponDiscount = 0;
            if ($coupon && $coupon->isValid($subtotal)) {
                $couponDiscount = $coupon->calculateDiscount($subtotal);
            }

            // Points discount
            $pointsToBaht = (int) SiteSetting::get('points_to_baht', 10);
            $pointsDiscount = $pointsUsed > 0 ? floor($pointsUsed / $pointsToBaht) : 0;

            $discount = $couponDiscount + $pointsDiscount;
            $total = max(0, $subtotal - $discount + $shippingFee);

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount' => $discount,
                'total' => $total,
                'points_used' => $pointsUsed,
                'coupon_id' => $coupon?->id,
                'shipping_name' => $data['shipping_name'],
                'shipping_phone' => $data['shipping_phone'],
                'shipping_address' => $data['shipping_address'],
                'shipping_district' => $data['shipping_district'],
                'shipping_province' => $data['shipping_province'],
                'shipping_postal_code' => $data['shipping_postal_code'],
                'note' => $data['note'] ?? null,
            ]);

            // Create order items + deduct stock
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->product->name,
                    'color_name' => $item->variant->color->name,
                    'size' => $item->variant->size,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);

                $item->variant->decrement('stock', $item->quantity);
            }

            // Deduct points
            if ($pointsUsed > 0) {
                auth()->user()->decrement('points', $pointsUsed);
                auth()->user()->pointTransactions()->create([
                    'order_id' => $order->id,
                    'points' => -$pointsUsed,
                    'type' => 'redeem',
                    'description' => "ใช้แต้มสั่งซื้อ {$order->order_number}",
                ]);
            }

            // Increment coupon usage
            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Clear cart
            $cart->items()->delete();

            return $order;
        });
    }
}
```

- [ ] **Step 2: Create CheckoutController**

`app/Http/Controllers/CheckoutController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\SiteSetting;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
    ) {}

    public function index()
    {
        $cart = $this->cartService->getCart();
        $cart->load('items.product', 'items.variant.color');

        abort_if($cart->items->isEmpty(), 404, 'ตะกร้าว่าง');

        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();

        return view('pages.checkout', compact('cart', 'addresses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_district' => 'required|string|max:255',
            'shipping_province' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'coupon_code' => 'nullable|string',
            'points_used' => 'nullable|integer|min:0',
        ]);

        $cart = $this->cartService->getCart();
        abort_if($cart->items->isEmpty(), 422, 'ตะกร้าว่าง');

        $coupon = null;
        if ($request->coupon_code) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();
            abort_unless($coupon && $coupon->isValid($cart->subtotal), 422, 'คูปองไม่ถูกต้อง');
        }

        $pointsUsed = min($request->points_used ?? 0, auth()->user()->points);

        $order = $this->orderService->createOrder(
            $request->only(['shipping_name', 'shipping_phone', 'shipping_address', 'shipping_district', 'shipping_province', 'shipping_postal_code', 'note']),
            $cart,
            $coupon,
            $pointsUsed,
        );

        return redirect()->route('checkout.success', $order)->with('success', 'สั่งซื้อสำเร็จ');
    }

    public function success(Request $request, \App\Models\Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $promptpay = [
            'id' => SiteSetting::get('promptpay_id'),
            'name' => SiteSetting::get('promptpay_name'),
            'qr' => SiteSetting::get('promptpay_qr'),
        ];

        return view('pages.checkout-success', compact('order', 'promptpay'));
    }
}
```

- [ ] **Step 3: Add checkout routes**

Add to `routes/web.php` inside auth middleware group:
```php
use App\Http\Controllers\CheckoutController;

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');
});
```

- [ ] **Step 4: Commit**

```bash
git add app/Services/OrderService.php app/Http/Controllers/CheckoutController.php routes/web.php
git commit -m "feat: create checkout flow with OrderService, stock lock, coupon, points"
```

---

### Task 23: Create Payment Slip Upload

**Files:**
- Create: `app/Http/Controllers/PaymentSlipController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create PaymentSlipController**

`app/Http/Controllers/PaymentSlipController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentSlipController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        abort_unless(in_array($order->status, ['pending', 'awaiting_payment']), 422, 'ไม่สามารถแนบสลิปได้');

        $request->validate([
            'slip' => 'required|image|max:5120',
        ]);

        $path = $request->file('slip')->store('payment-slips', 'public');

        $order->paymentSlip()->updateOrCreate(
            ['order_id' => $order->id],
            [
                'image_path' => $path,
                'uploaded_at' => now(),
                'confirmed_at' => null,
                'confirmed_by' => null,
                'rejection_reason' => null,
            ]
        );

        $order->update(['status' => 'awaiting_payment']);

        return back()->with('success', 'อัปโหลดสลิปเรียบร้อย รอตรวจสอบ');
    }
}
```

- [ ] **Step 2: Add route**

```php
use App\Http\Controllers\PaymentSlipController;

Route::middleware('auth')->group(function () {
    Route::post('/orders/{order}/slip', [PaymentSlipController::class, 'store'])->name('orders.slip.store');
});
```

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/PaymentSlipController.php routes/web.php
git commit -m "feat: create payment slip upload with status transition"
```

---

### Task 24: Create Cart and Checkout Blade Views

**Files:**
- Create: `resources/views/pages/cart.blade.php`
- Create: `resources/views/pages/checkout.blade.php`
- Create: `resources/views/pages/checkout-success.blade.php`

- [ ] **Step 1: Create cart page** — Product list with images, color, size, price, quantity controls, coupon input, points input, summary totals.

- [ ] **Step 2: Create checkout page** — Address selector, order summary, confirm button.

- [ ] **Step 3: Create checkout success page** — Order number, PromptPay QR, bank details, slip upload form.

- [ ] **Step 4: Commit**

```bash
git add resources/views/pages/cart.blade.php resources/views/pages/checkout.blade.php resources/views/pages/checkout-success.blade.php
git commit -m "feat: create cart and checkout Blade views"
```

---

### Task 25: Write Tests for Cart and Order Services

**Files:**
- Create: `tests/Feature/CartServiceTest.php`
- Create: `tests/Feature/OrderServiceTest.php`

- [ ] **Step 1: Create CartServiceTest**

```bash
php artisan make:test CartServiceTest
```

Test cases:
- Add item to cart
- Update quantity
- Remove item
- Cannot exceed stock
- Merge session cart on login

- [ ] **Step 2: Create OrderServiceTest**

```bash
php artisan make:test OrderServiceTest
```

Test cases:
- Create order deducts stock
- Create order with coupon applies discount
- Create order with points deducts user points
- Cannot checkout with insufficient stock (race condition protection)
- Order number auto-generates correctly

- [ ] **Step 3: Run tests**

```bash
php artisan test
```

Expected: All tests pass.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/
git commit -m "test: add feature tests for CartService and OrderService"
```

---

## Phase 5: User Profile + Wishlist + Email Notifications

> Deliverable: User profile pages, wishlist, order history, and all email notifications working.

---

### Task 26: Create Profile and Address Controllers

**Files:**
- Create: `app/Http/Controllers/ProfileController.php` (extend existing Breeze one)
- Create: `app/Http/Controllers/AddressController.php`
- Create: `app/Http/Controllers/OrderHistoryController.php`
- Create: `app/Http/Controllers/WishlistController.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Create AddressController**

`app/Http/Controllers/AddressController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses;
        return view('pages.profile.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if ($data['is_default'] ?? false) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($data);

        return back()->with('success', 'เพิ่มที่อยู่เรียบร้อย');
    }

    public function update(Request $request, Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if ($data['is_default'] ?? false) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'อัปเดตที่อยู่เรียบร้อย');
    }

    public function destroy(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'ลบที่อยู่เรียบร้อย');
    }
}
```

- [ ] **Step 2: Create OrderHistoryController**

`app/Http/Controllers/OrderHistoryController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Order;

class OrderHistoryController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('pages.profile.orders', compact('orders'));
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);
        $order->load('items', 'paymentSlip');

        return view('pages.profile.order-detail', compact('order'));
    }
}
```

- [ ] **Step 3: Create WishlistController**

`app/Http/Controllers/WishlistController.php`:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = auth()->user()->wishlists()
            ->with('product.primaryImage')
            ->latest()
            ->get();

        return view('pages.profile.wishlist', compact('wishlists'));
    }

    public function toggle(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $existing = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'ลบออกจาก Wishlist แล้ว');
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
        ]);

        return back()->with('success', 'เพิ่มใน Wishlist แล้ว');
    }
}
```

- [ ] **Step 4: Add profile routes**

Add to `routes/web.php` inside auth middleware:
```php
use App\Http\Controllers\AddressController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\WishlistController;

Route::middleware('auth')->group(function () {
    Route::get('/profile', fn () => view('pages.profile.index'))->name('profile.index');
    Route::get('/profile/points', fn () => view('pages.profile.points', [
        'transactions' => auth()->user()->pointTransactions()->latest()->paginate(20),
    ]))->name('profile.points');

    Route::resource('addresses', AddressController::class)->except(['create', 'show', 'edit']);

    Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});
```

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ routes/web.php
git commit -m "feat: create profile, address, order history, wishlist controllers and routes"
```

---

### Task 27: Create Profile Blade Views

**Files:**
- Create: `resources/views/pages/profile/index.blade.php`
- Create: `resources/views/pages/profile/addresses.blade.php`
- Create: `resources/views/pages/profile/orders.blade.php`
- Create: `resources/views/pages/profile/order-detail.blade.php`
- Create: `resources/views/pages/profile/wishlist.blade.php`
- Create: `resources/views/pages/profile/points.blade.php`

- [ ] **Step 1-6: Create each profile view** — Profile sidebar navigation + content area. Order detail includes status timeline, tracking info, slip view, and slip upload form.

- [ ] **Step 7: Commit**

```bash
git add resources/views/pages/profile/
git commit -m "feat: create all profile Blade views (info, addresses, orders, wishlist, points)"
```

---

### Task 28: Create Email Notifications

**Files:**
- Create: `app/Mail/OrderCreated.php`
- Create: `app/Mail/PaymentConfirmed.php`
- Create: `app/Mail/PaymentRejected.php`
- Create: `app/Mail/OrderShipped.php`
- Create: `app/Mail/OrderCompleted.php`
- Create: `app/Mail/OrderCancelled.php`
- Create: `app/Mail/NewOrderNotification.php` (to admin)
- Create: `app/Mail/NewSlipNotification.php` (to admin)
- Create: corresponding Blade email templates in `resources/views/emails/`

- [ ] **Step 1: Create OrderCreated mail**

```bash
php artisan make:mail OrderCreated --markdown=emails.order-created
```

`app/Mail/OrderCreated.php`:
```php
<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "ออเดอร์ {$this->order->order_number} — รอชำระเงิน");
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.order-created');
    }
}
```

- [ ] **Step 2: Create remaining mails** — Follow same pattern for PaymentConfirmed, PaymentRejected, OrderShipped, OrderCompleted, OrderCancelled, NewOrderNotification, NewSlipNotification. Each has a Blade markdown template showing relevant order info.

- [ ] **Step 3: Dispatch emails from order status transitions**

Add email dispatching to:
- `CheckoutController::store()` → `OrderCreated` to customer + `NewOrderNotification` to admin
- `PaymentSlipController::store()` → `NewSlipNotification` to admin
- `OrderResource` approve action → `PaymentConfirmed` to customer
- `OrderResource` reject action → `PaymentRejected` to customer
- `OrderResource` ship action → `OrderShipped` to customer
- `OrderResource` complete action → `OrderCompleted` to customer

- [ ] **Step 4: Commit**

```bash
git add app/Mail/ resources/views/emails/
git commit -m "feat: create all email notifications for order lifecycle"
```

---

## Phase 6: Points System, Auto-Cancel, Reports & Polish

> Deliverable: Points earning on completion, auto-cancel scheduler, admin reports with Excel export, cart merge on login, and final polish.

---

### Task 29: Implement Points Earning on Order Completion

**Files:**
- Modify: `app/Filament/Resources/OrderResource.php` (complete action)
- Create: `app/Services/PointsService.php`

- [ ] **Step 1: Create PointsService**

`app/Services/PointsService.php`:
```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\SiteSetting;

class PointsService
{
    public function earnPoints(Order $order): int
    {
        $pointsPerBaht = (int) SiteSetting::get('points_per_baht', 100);
        if ($pointsPerBaht <= 0) return 0;

        $pointsEarned = (int) floor($order->total / $pointsPerBaht);
        if ($pointsEarned <= 0) return 0;

        $order->user->increment('points', $pointsEarned);
        $order->update(['points_earned' => $pointsEarned]);

        PointTransaction::create([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'points' => $pointsEarned,
            'type' => 'earn',
            'description' => "สั่งซื้อ {$order->order_number}",
        ]);

        return $pointsEarned;
    }
}
```

- [ ] **Step 2: Update OrderResource complete action to earn points**

In the `complete` action of `OrderResource.php`, replace the comment with:
```php
->action(function (Order $record) {
    $record->update([
        'status' => 'completed',
        'completed_at' => now(),
    ]);
    app(\App\Services\PointsService::class)->earnPoints($record);
})
```

- [ ] **Step 3: Commit**

```bash
git add app/Services/PointsService.php app/Filament/Resources/OrderResource.php
git commit -m "feat: implement points earning on order completion"
```

---

### Task 30: Implement Auto-Cancel Scheduler

**Files:**
- Create: `app/Console/Commands/CancelExpiredOrders.php`
- Modify: `routes/console.php`

- [ ] **Step 1: Create command**

```bash
php artisan make:command CancelExpiredOrders
```

`app/Console/Commands/CancelExpiredOrders.php`:
```php
<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel orders pending payment for more than 48 hours';

    public function handle(): void
    {
        $orders = Order::where('status', 'pending')
            ->where('created_at', '<', now()->subHours(48))
            ->get();

        foreach ($orders as $order) {
            // Restore stock
            foreach ($order->items as $item) {
                $item->variant->increment('stock', $item->quantity);
            }

            // Restore points if used
            if ($order->points_used > 0) {
                $order->user->increment('points', $order->points_used);
                $order->user->pointTransactions()->create([
                    'order_id' => $order->id,
                    'points' => $order->points_used,
                    'type' => 'adjust',
                    'description' => "คืนแต้ม — ยกเลิกออเดอร์ {$order->order_number}",
                ]);
            }

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Send cancellation email
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->queue(new \App\Mail\OrderCancelled($order));

            $this->info("Cancelled order {$order->order_number}");
        }

        $this->info("Processed {$orders->count()} expired orders.");
    }
}
```

- [ ] **Step 2: Register schedule**

Add to `routes/console.php`:
```php
use Illuminate\Support\Facades\Schedule;

Schedule::command('orders:cancel-expired')->hourly();
```

- [ ] **Step 3: Commit**

```bash
git add app/Console/Commands/ routes/console.php
git commit -m "feat: add auto-cancel expired orders scheduler (48h)"
```

---

### Task 31: Create Admin Reports Page with Excel Export

**Files:**
- Create: `app/Filament/Pages/Reports.php`
- Create: `resources/views/filament/pages/reports.blade.php`
- Create: `app/Exports/SalesReportExport.php`
- Create: `app/Exports/TopProductsExport.php`

- [ ] **Step 1: Create SalesReportExport**

`app/Exports/SalesReportExport.php`:
```php
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        private string $from,
        private string $to,
    ) {}

    public function query()
    {
        return Order::query()
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$this->from, $this->to])
            ->with('user')
            ->orderByDesc('completed_at');
    }

    public function headings(): array
    {
        return ['เลขออเดอร์', 'ลูกค้า', 'ยอดรวม', 'ส่วนลด', 'ค่าส่ง', 'สุทธิ', 'วันที่'];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->user->name,
            $order->subtotal,
            $order->discount,
            $order->shipping_fee,
            $order->total,
            $order->completed_at->format('d/m/Y H:i'),
        ];
    }
}
```

- [ ] **Step 2: Create Reports Filament page with export actions** — Date range filter, sales table, top products table, export buttons using Maatwebsite Excel.

- [ ] **Step 3: Commit**

```bash
git add app/Filament/Pages/Reports.php app/Exports/ resources/views/filament/pages/reports.blade.php
git commit -m "feat: add admin reports page with sales and product reports + Excel export"
```

---

### Task 32: Cart Merge on Login + Auth Customization

**Files:**
- Create: `app/Listeners/MergeCartOnLogin.php`
- Modify: `app/Providers/EventServiceProvider.php` or `bootstrap/app.php`
- Modify Breeze registration to include phone field

- [ ] **Step 1: Create MergeCartOnLogin listener**

`app/Listeners/MergeCartOnLogin.php`:
```php
<?php

namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeCartOnLogin
{
    public function handle(Login $event): void
    {
        app(CartService::class)->mergeSessionCart();
    }
}
```

- [ ] **Step 2: Register the listener**

In `bootstrap/app.php` or `EventServiceProvider`:
```php
use App\Listeners\MergeCartOnLogin;
use Illuminate\Auth\Events\Login;

// In EventServiceProvider $listen:
protected $listen = [
    Login::class => [
        MergeCartOnLogin::class,
    ],
];
```

- [ ] **Step 3: Add phone field to Breeze registration**

Modify `app/Http/Controllers/Auth/RegisteredUserController.php` to include `phone` in validation and creation.

- [ ] **Step 4: Commit**

```bash
git add app/Listeners/ app/Providers/ app/Http/Controllers/Auth/
git commit -m "feat: merge session cart on login + add phone to registration"
```

---

### Task 33: Final Polish — SEO, Storage Link, Responsive Check

**Files:**
- Various layout and view tweaks

- [ ] **Step 1: Create storage link**

```bash
php artisan storage:link
```

- [ ] **Step 2: Add meta tags component**

Create `resources/views/components/meta.blade.php` for Open Graph and SEO meta tags. Use it in product detail and collection pages.

- [ ] **Step 3: Verify responsive design** — Check all pages on mobile/tablet/desktop viewport widths. Fix any layout issues.

- [ ] **Step 4: Add `.env.example` entries for mail config**

```
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@chomin.com
MAIL_FROM_NAME="CHOMIN"
```

- [ ] **Step 5: Final commit**

```bash
git add -A
git commit -m "feat: add SEO meta tags, storage link, responsive fixes, mail config"
```

---

### Task 34: Run Full Test Suite and Fix Issues

- [ ] **Step 1: Run all tests**

```bash
php artisan test
```

- [ ] **Step 2: Fix any failing tests**

- [ ] **Step 3: Run the application end-to-end**

```bash
php artisan serve
```

Verify manually:
- Homepage loads with collections
- Product detail shows color/size selector
- Cart add/update/remove works
- Checkout flow creates order
- Admin panel CRUD works for all resources
- Admin can approve/reject slips, ship orders, complete orders
- Points earned on completion
- Settings page saves correctly

- [ ] **Step 4: Final commit**

```bash
git add -A
git commit -m "fix: resolve test failures and final adjustments"
```
