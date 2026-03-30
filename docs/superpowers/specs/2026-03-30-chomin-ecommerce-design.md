# Chomin Ecommerce — Design Spec

> เว็บ ecommerce แฟชั่นสไตล์ premium ภาษาไทย สร้างด้วย Laravel

## Tech Stack

- **Framework:** Laravel 11 + PHP 8.3
- **Frontend:** Blade templates + Tailwind CSS 3 + Alpine.js
- **Admin Panel:** Filament 3
- **Database:** MySQL 8
- **Auth:** Laravel Breeze (ลูกค้า) + Filament Shield (admin roles)
- **Email:** Laravel Mail + Queue (ส่ง background)
- **File Storage:** Laravel Storage (รูปสินค้า, สลิปโอนเงิน)
- **Scheduler:** Laravel Scheduler (auto-cancel orders)

## Database Schema

### users
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| name | string | |
| email | string unique | |
| password | string | |
| phone | string nullable | |
| points | int default 0 | แต้มสะสม |
| email_verified_at | timestamp nullable | |
| remember_token | string nullable | |
| timestamps | | |

### addresses
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| user_id | FK → users | |
| name | string | ชื่อผู้รับ |
| phone | string | |
| address | text | |
| district | string | เขต/อำเภอ |
| province | string | จังหวัด |
| postal_code | string | |
| is_default | boolean default false | |
| timestamps | | |

### collections
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| name | string | เช่น MIDNIGHT SERIES |
| slug | string unique | |
| description | text nullable | |
| image | string nullable | รูปปก |
| banner_image | string nullable | รูป banner ใหญ่ |
| is_active | boolean default true | |
| sort_order | int default 0 | |
| timestamps | | |

### categories
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| name | string | เช่น เสื้อเชิ้ต, กางเกง |
| slug | string unique | |
| image | string nullable | |
| is_active | boolean default true | |
| sort_order | int default 0 | |
| timestamps | | |

### products
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| name | string | |
| slug | string unique | |
| description | text nullable | |
| price | decimal(10,2) | |
| collection_id | FK → collections | |
| category_id | FK → categories | |
| is_active | boolean default true | |
| is_featured | boolean default false | แสดงหน้าแรก |
| sort_order | int default 0 | |
| timestamps | | |

### product_colors
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| product_id | FK → products | |
| name | string | เช่น ดำ, ขาว, น้ำตาล |
| color_code | string | hex เช่น #000000 |
| sort_order | int default 0 | |

### product_images
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| product_id | FK → products | |
| product_color_id | FK → product_colors | |
| image_path | string | |
| is_primary | boolean default false | |
| sort_order | int default 0 | |

### product_variants
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| product_id | FK → products | |
| product_color_id | FK → product_colors | |
| size | string | S, M, L, XL, etc. |
| stock | int default 0 | |
| sku | string unique nullable | |

### carts
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| user_id | FK → users nullable | null = guest (ใช้ session) |
| session_id | string nullable | สำหรับ guest cart |
| timestamps | | |

### cart_items
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| cart_id | FK → carts | |
| product_id | FK → products | |
| product_variant_id | FK → product_variants | |
| quantity | int default 1 | |
| timestamps | | |

### wishlists
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| user_id | FK → users | |
| product_id | FK → products | |
| timestamps | | |
| unique(user_id, product_id) | | |

### orders
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| user_id | FK → users | |
| order_number | string unique | auto-generate เช่น CHO-20260330-0001 |
| status | enum | pending, awaiting_payment, paid, shipping, completed, cancelled |
| subtotal | decimal(10,2) | |
| shipping_fee | decimal(10,2) | |
| discount | decimal(10,2) default 0 | |
| total | decimal(10,2) | |
| points_earned | int default 0 | |
| points_used | int default 0 | |
| coupon_id | FK → coupons nullable | |
| shipping_name | string | |
| shipping_phone | string | |
| shipping_address | text | |
| shipping_district | string | |
| shipping_province | string | |
| shipping_postal_code | string | |
| tracking_number | string nullable | |
| carrier_name | string nullable | ชื่อขนส่ง |
| shipped_at | timestamp nullable | |
| completed_at | timestamp nullable | |
| cancelled_at | timestamp nullable | |
| note | text nullable | หมายเหตุจากลูกค้า |
| timestamps | | |

### order_items
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| order_id | FK → orders | |
| product_id | FK → products | |
| product_variant_id | FK → product_variants | |
| product_name | string | snapshot ชื่อตอนสั่ง |
| color_name | string | snapshot สี |
| size | string | snapshot size |
| price | decimal(10,2) | snapshot ราคา |
| quantity | int | |

### payment_slips
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| order_id | FK → orders | |
| image_path | string | |
| uploaded_at | timestamp | |
| confirmed_at | timestamp nullable | |
| confirmed_by | FK → users nullable | admin ที่ approve |
| rejection_reason | string nullable | เหตุผลที่ reject |

### coupons
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| code | string unique | |
| type | enum | fixed, percent |
| value | decimal(10,2) | จำนวนเงิน หรือ % |
| max_discount | decimal(10,2) nullable | เฉพาะ percent — ลดสูงสุดไม่เกิน |
| min_order_amount | decimal(10,2) default 0 | ยอดขั้นต่ำ |
| max_uses | int nullable | จำกัดจำนวนครั้ง |
| used_count | int default 0 | |
| starts_at | timestamp nullable | |
| expires_at | timestamp nullable | |
| is_active | boolean default true | |
| timestamps | | |

### point_transactions
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| user_id | FK → users | |
| order_id | FK → orders nullable | |
| points | int | บวก = ได้, ลบ = ใช้ |
| type | enum | earn, redeem, adjust |
| description | string | เช่น "สั่งซื้อ CHO-20260330-0001" |
| timestamps | | |

### banners
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| title | string nullable | |
| subtitle | string nullable | |
| image | string | |
| link | string nullable | |
| is_active | boolean default true | |
| sort_order | int default 0 | |
| timestamps | | |

### shipping_settings
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| shipping_fee | decimal(10,2) | ค่าส่งเหมา |
| free_shipping_min_amount | decimal(10,2) nullable | ฟรีค่าส่งเมื่อซื้อครบ |
| timestamps | | |

### site_settings
| Column | Type | Note |
|--------|------|------|
| id | bigint PK | |
| key | string unique | |
| value | text nullable | |

**ค่า key ใน site_settings:**
- `site_name` — ชื่อร้าน
- `site_phone` — เบอร์โทร
- `site_email` — อีเมล
- `site_address` — ที่อยู่ร้าน
- `promptpay_id` — หมายเลข PromptPay
- `promptpay_qr` — รูป QR Code
- `promptpay_name` — ชื่อบัญชี
- `points_per_baht` — ทุกกี่บาทได้ 1 แต้ม (เช่น 100)
- `points_to_baht` — กี่แต้มแลก 1 บาท (เช่น 10)
- `about_content` — เนื้อหาหน้าเกี่ยวกับเรา
- `footer_quote` — ข้อความ quote หน้าแรก

### Relationships

```
User hasOne Cart
User hasMany Address, Order, Wishlist, PointTransaction
Cart hasMany CartItem
CartItem belongsTo Product, ProductVariant
Product belongsTo Collection, Category
Product hasMany ProductColor, ProductVariant, ProductImage
ProductColor hasMany ProductImage, ProductVariant
Order belongsTo User, Coupon
Order hasMany OrderItem
Order hasOne PaymentSlip
OrderItem belongsTo Product, ProductVariant
```

## Frontend Pages

### Design Direction
- **สี:** พื้นขาว `#FFFFFF`, ดำ `#000000`, น้ำตาลเข้ม `#3C2415`, เทาอ่อน `#F5F5F5`
- **Typography:** IBM Plex Sans Thai (body) + serif font สำหรับ collection titles
- **Layout:** Full-width sections สลับพื้นขาว/ดำ
- **Product cards:** รูป + ชื่อ + ราคา แบบ minimal
- **Interaction:** Alpine.js — cart dropdown, color/size selector, image gallery, mobile menu

### หน้าลูกค้า (Public)

**หน้าแรก:**
- Hero banner (จาก banners table, สลับได้)
- Product cards แถวแนวนอน scroll ได้
- Collection sections: MIDNIGHT SERIES (พื้นดำ), URBAN WEEKEND (พื้นขาว), SIGNATURE ACCESSORIES (พื้นเทา)
- แต่ละ collection มี banner + product cards + ปุ่ม "ดูทั้งหมด"
- Quote section (จาก site_settings)
- Footer — ลิงก์, ข้อมูลร้าน

**คอลเล็คชัน (Collections):**
- Grid แสดง collections ทั้งหมดที่ active
- แต่ละ collection แสดงรูปปก + ชื่อ

**สินค้าในคอลเล็คชัน:**
- Banner ของ collection
- Grid สินค้า + filter (category, สี, ช่วงราคา) + sort (ใหม่สุด, ราคาต่ำ-สูง)

**รายละเอียดสินค้า:**
- Gallery รูปแยกตามสี (คลิกสีเปลี่ยนรูป)
- เลือก size + สี
- แสดง stock (เหลือ/หมด)
- ปุ่มเพิ่มลง cart / เพิ่ม wishlist
- รายละเอียดสินค้า (description)
- สินค้าแนะนำจาก collection เดียวกัน

**ร้าน (Shop):**
- สินค้าทั้งหมด + filter/sort เหมือนหน้าคอลเล็คชัน

**ตะกร้าสินค้า:**
- รายการสินค้า (รูป, ชื่อ, สี, size, ราคา, จำนวน)
- แก้ไขจำนวน / ลบ
- ใส่รหัส coupon
- เลือกใช้แต้มสะสม (แสดงแต้มที่มี + จำนวนเงินที่ลดได้)
- สรุป: subtotal, ส่วนลด coupon, ส่วนลดแต้ม, ค่าส่ง, รวม

**Checkout:**
- เลือกที่อยู่จัดส่งที่มีอยู่ หรือเพิ่มใหม่
- สรุปออเดอร์
- ยืนยันสั่งซื้อ → แสดง QR PromptPay + ข้อมูลบัญชี
- ฟอร์มแนบสลิป (upload รูป)

**สมัครสมาชิก / เข้าสู่ระบบ:**
- Register: ชื่อ, อีเมล, รหัสผ่าน, เบอร์โทร
- Login: อีเมล + รหัสผ่าน
- Forgot password: ส่ง reset link ทาง email

**โปรไฟล์สมาชิก:**
- แก้ไขข้อมูลส่วนตัว
- จัดการที่อยู่จัดส่ง (CRUD, ตั้ง default)
- ประวัติสั่งซื้อ (รายการ + สถานะ + คลิกดูรายละเอียด)
- แต้มสะสม (ยอดปัจจุบัน + ประวัติได้/ใช้)
- Wishlist (grid สินค้า + ลบได้)

**รายละเอียดออเดอร์:**
- เลขออเดอร์, สถานะ (timeline), วันที่สั่ง
- Tracking number + ชื่อขนส่ง (ถ้ามี)
- รายการสินค้า
- สรุปราคา
- สลิปที่แนบ

**เกี่ยวกับเรา:**
- เนื้อหาจาก site_settings (`about_content`)

### Cart System
- **Guest:** Session-based cart (เก็บใน session)
- **Logged in:** Database cart (ตาราง carts/cart_items) หรือ merge จาก session เมื่อ login
- Alpine.js อัปเดต cart count บน navbar แบบ realtime
- ตรวจ stock ก่อนเพิ่ม cart และก่อน checkout

## Admin Panel (Filament 3)

### Dashboard Widgets
- ยอดขายวันนี้ / สัปดาห์ / เดือน (Stat widgets)
- ออเดอร์รอยืนยันชำระเงิน (จำนวน + quick link)
- สินค้าใกล้หมด stock (รายการ variant ที่ stock ≤ 5)
- สมาชิกใหม่วันนี้/สัปดาห์
- กราฟยอดขาย (Line chart รายวัน/รายเดือน)

### Resources

**Products Resource:**
- List: ตาราง + search + filter (collection, category, active, featured)
- Create/Edit: ชื่อ, slug (auto), ราคา, description, collection, category
- Relation managers: สี (+ color picker), รูปแยกตามสี (upload multiple), variants (size + stock + SKU)

**Collections Resource:**
- CRUD + upload banner + image
- จัดลำดับ (sort_order) + เปิด/ปิด

**Categories Resource:**
- CRUD + จัดลำดับ + เปิด/ปิด

**Orders Resource:**
- List: ตาราง + filter (สถานะ, วันที่) + search (order_number, ชื่อลูกค้า)
- View: รายละเอียดออเดอร์, รายการสินค้า, ที่อยู่จัดส่ง
- Actions: ดูสลิป, approve/reject สลิป, ใส่ tracking number + ชื่อขนส่ง, เปลี่ยนสถานะ
- Export Excel

**Users Resource:**
- List + search + filter
- View: ข้อมูล, ประวัติสั่งซื้อ, แต้มสะสม
- Action: ปรับแต้มมือ (+ reason)

**Coupons Resource:**
- CRUD: code, type (fixed/percent), value, max_discount, min_order_amount, max_uses, วันเริ่ม/หมดอายุ

**Payment Slips Resource:**
- List: สลิปรอตรวจ (filter by status)
- View: รูปสลิป + ข้อมูลออเดอร์
- Actions: Approve (→ order status = paid), Reject (+ เหตุผล → order status = pending)

**Banners Resource:**
- CRUD + upload รูป + จัดลำดับ + เปิด/ปิด

### Settings Page (Filament Custom Page)
- ชื่อร้าน, เบอร์โทร, อีเมล, ที่อยู่
- PromptPay: ID, ชื่อบัญชี, QR Code (upload)
- ค่าส่ง + ยอดขั้นต่ำฟรีค่าส่ง
- แต้มสะสม: points_per_baht, points_to_baht
- เนื้อหาหน้าเกี่ยวกับเรา
- ข้อความ quote หน้าแรก

### Reports Page (Filament Custom Page)
- รายงานยอดขาย (เลือกช่วงวันที่) — ตาราง + กราฟ
- รายงานสินค้าขายดี (top 10/20)
- Export Excel ทุกรายงาน

### Admin Roles (Filament Shield)
- **Super Admin:** เข้าถึงทุกอย่าง
- **Staff:** เข้าถึงเฉพาะ Orders, Products, Payment Slips, Collections, Categories

## Order Flow

```
เพิ่มสินค้าลงตะกร้า → ตรวจ stock
    ↓
Checkout → เลือกที่อยู่ + coupon/แต้ม → ตรวจ stock อีกครั้ง
    ↓
ยืนยันออเดอร์ → หัก stock → สถานะ: pending (รอชำระเงิน)
    ↓
แสดง QR PromptPay → ลูกค้าแนบสลิป → สถานะ: awaiting_payment
    ↓
Admin ตรวจสลิป
    ├── Approve → สถานะ: paid → email ยืนยัน
    └── Reject → สถานะ: pending → email แจ้งเหตุผล + แนบสลิปใหม่
    ↓
Admin จัดส่ง + ใส่ tracking → สถานะ: shipping → email แจ้ง tracking
    ↓
Admin กด complete → สถานะ: completed → คำนวณแต้ม → email ขอบคุณ
```

### Auto-cancel
- Scheduler ตรวจทุก 1 ชั่วโมง
- ออเดอร์สถานะ pending เกิน 48 ชั่วโมง → cancel + คืน stock
- แจ้ง email ลูกค้า

### Stock Management
- หัก stock เมื่อยืนยันออเดอร์ (ไม่ใช่เมื่อเพิ่ม cart)
- คืน stock เมื่อ cancel
- ตรวจ stock ซ้ำก่อน checkout เพื่อป้องกัน race condition (ใช้ DB lock)

## Email Notifications

| Event | To | Content |
|-------|-----|---------|
| ออเดอร์ใหม่ | ลูกค้า | สรุปออเดอร์ + ข้อมูลโอนเงิน |
| Confirm ชำระเงิน | ลูกค้า | ยืนยันรับชำระ |
| Reject สลิป | ลูกค้า | เหตุผล + ให้แนบใหม่ |
| จัดส่งแล้ว | ลูกค้า | Tracking number + ขนส่ง |
| ออเดอร์สำเร็จ | ลูกค้า | ขอบคุณ + แต้มที่ได้รับ |
| Auto-cancel | ลูกค้า | แจ้งยกเลิกเนื่องจากไม่ชำระ |
| ออเดอร์ใหม่เข้า | Admin | แจ้งเตือน |
| สลิปรอตรวจ | Admin | แจ้งเตือน |

## Points System

- **ได้แต้ม:** เมื่อ order completed → `floor(total / points_per_baht)` แต้ม
- **ใช้แต้ม:** ตอน checkout → ลดราคา `floor(points_used / points_to_baht)` บาท
- **ปรับมือ:** Admin ปรับได้ + ใส่เหตุผล (type = adjust)
- **ประวัติ:** ทุก transaction บันทึกใน point_transactions

## Non-Functional Requirements

- **Responsive:** รองรับ mobile, tablet, desktop
- **SEO:** Meta tags, Open Graph, structured data สำหรับสินค้า
- **Performance:** Image optimization (lazy loading, WebP), Blade caching
- **Security:** CSRF protection, input validation, file upload validation (สลิป/รูปสินค้า — เฉพาะ image types, max 5MB)
