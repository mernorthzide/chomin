# Product Import Seeder Design

## Overview

สร้าง Laravel Seeder เพื่อ import สินค้าเสื้อเชิ้ต CHO.MIN เข้าระบบจากข้อมูล SKU/Color ที่ได้จาก Google Drive โดยสินค้าจะแยกตามชุดรวม คอ+แขน+กระเป๋า (30 สินค้า) แต่ละตัวมี 50 สี × 9 ไซส์

## Data Source

ข้อมูลจากไฟล์ `SKU_Color.xlsx` ใน Google Drive folder:
`https://drive.google.com/drive/folders/1vEvOKS7bQSOx4G2tbpahELWXM3EpyZIO`

### SKU Structure

```
CHO-CLS-[Collar]-[Cuff]-[Pocket]-[ColorCode]-[Size]
เช่น CHO-CLS-FR-C1-NP-11047-M
```

| Section | Code | Description |
|---------|------|-------------|
| Brand | CHO | CHO.MIN |
| Model | CLS | CM Classic |
| Collar | FR | French Collar - คอปกทรงมาตรฐาน |
| Collar | IT | Italian Collar - คอปกกว้างพรีเมียม |
| Collar | MD | Mandarin Collar - คอจีนไม่มีปกพับ |
| Collar | BD | Button Down - คอปกติดกระดุม |
| Collar | BC | Band Collar - คอแถบเรียบมินิมอล |
| Cuff | C1 | 1 Button - แขนกระดุมเดี่ยว |
| Cuff | C2 | 2 Button - แขนปรับได้สองระดับ |
| Cuff | CF | French Cuff - แขนพับใส่กระดุมข้อมือ |
| Pocket | NP | No Pocket - ไม่มีกระเป๋า |
| Pocket | YP | Yes Pocket - มีกระเป๋าหน้าอก |

### Colors (50 สี)

Group 1 (19 สี, รหัส 7-88):

| รหัส | ตัวย่อ | ชื่อสี | HEX |
|------|--------|--------|-----|
| 7 | WLG | Warm Light Greige | #D8D5C5 |
| 13 | VGY | Vivid Golden Yellow | #F5D300 |
| 21 | LKH | Light Khaki | #D9D2B4 |
| 26 | OLG | Olive Green | #6F6A47 |
| 33 | DTG | Deep Teal Green | #24524F |
| 39 | MSB | Muted Slate Blue | #505E84 |
| 40 | DIN | Deep Indigo | #3C3F5A |
| 42 | SLG | Soft Lavender Gray | #C7C2D8 |
| 43 | DVI | Dusty Violet | #7C72A3 |
| 48 | NBN | Near Black Navy | #070A12 |
| 55 | MTL | Muted Teal | #3F8C84 |
| 60 | CHP | Charcoal Plum | #564D57 |
| 61 | DGR | Dark Graphite | #3E404A |
| 62 | DNG | Deep Navy Gray | #243447 |
| 65 | BLT | Blush Taupe | #DCC9CF |
| 73 | SCP | Soft Coral Pink | #D9817E |
| 79 | BOR | Burnt Orange | #EB6529 |
| 81 | VRE | Vivid Red | #C81D14 |
| 88 | DCR | Deep Crimson | #8F1620 |

Group 2 (31 สี, รหัส 11002-11055):

| รหัส | ตัวย่อ | ชื่อสี | HEX |
|------|--------|--------|-----|
| 11002 | FBL | Floral Blush | #FBECE8 |
| 11003 | PAC | Pastel Aqua | #C2E2EB |
| 11004 | BPW | Baby Powder | #E1E8F9 |
| 11007 | CFS | Creamy Sun | #FFF7D0 |
| 11009 | LBL | Light Baby Blue | #D8EFFF |
| 11010 | SKY | Sky Blue | #8CC6F8 |
| 11011 | PBZ | Pink Breeze | #F2C3E6 |
| 11012 | LPL | Lilac Petal | #E6C4EB |
| 11013 | WLV | White Lavender | #E2D9F4 |
| 11014 | CLB | Cool Light Blue | #A4BEF0 |
| 11015 | DGP | Deep Grape | #4F3C6F |
| 11016 | DCB | Dark Cocoa Brown | #3E3342 |
| 11021 | FUC | Fuchsia | #B832A6 |
| 11022 | CFS2 | Calm Soft Sand | #E8E6D2 |
| 11023 | OFF | Off White | #F7F6EF |
| 11024 | CSH | Creamy Shell | #F7E1B5 |
| 11025 | MCB | Mocha Brown | #6D4E3F |
| 11026 | CRL | Coral Rose | #EB9C8E |
| 11027 | OLV | Olive Verde | #5E633D |
| 11031 | BUR | Burgundy | #602E35 |
| 11033 | ASK | Aqua Sky | #67C1CF |
| 11036 | RAZ | Royal Azure | #2F5D9F |
| 11042 | DSP | Deep Sapphire | #315DA8 |
| 11044 | SSM | Soft Silver Mist | #D9D9D9 |
| 11045 | CLG | Cool Lavender Grey | #B7B7C2 |
| 11046 | STG | Steel Grey | #9EA3AD |
| 11047 | SLG | Slate Grey | #7C818C |
| 11048 | GPG | Graphite Grey | #6B707A |
| 11049 | ESB | Espresso Brown | #3B3432 |
| 11051 | MNN | Midnight Navy | #2B2F4A |
| 11055 | CHN | Charcoal Navy | #2F334F |

### Sizes (9 ไซส์)

XS, S, M, L, XL, 2XL, 3XL, 4XL, 5XL

## What Gets Created

### Collection (1)

| Name | Slug |
|------|------|
| CM Classic | cm-classic |

### Category (1)

| Name | Slug |
|------|------|
| เสื้อเชิ้ต | shirt |

### Products (30)

Naming pattern: `CM Classic - [Collar Name] - [Cuff Name] - [Pocket Name]`

Examples:
- CM Classic - French Collar - 1 Button - No Pocket
- CM Classic - French Collar - 1 Button - Yes Pocket
- CM Classic - French Collar - 2 Button - No Pocket
- CM Classic - Italian Collar - French Cuff - Yes Pocket
- ... (30 total combinations)

All products:
- price: 590.00 (placeholder, editable via admin)
- collection: CM Classic
- category: เสื้อเชิ้ต
- is_active: true
- is_featured: false

### Product Colors (1,500)

Each product gets all 50 colors with:
- name: color name from Excel (e.g., "Warm Light Greige")
- color_code: HEX value from Excel (e.g., "#D8D5C5")

### Product Images (1,500)

Each product color gets 1 swatch image:
- image_path: `products/colors/{color_code}.png`
- is_primary: true (first color) / false (rest)
- Source images from Drive: `icon/sku/7-88_color19/*.png` and `icon/sku/11002-11055_color31/*.png`

### Product Variants (13,500)

Each product color × 9 sizes:
- size: XS, S, M, L, XL, 2XL, 3XL, 4XL, 5XL
- stock: 100
- sku: `CHO-CLS-{collar}-{cuff}-{pocket}-{colorCode}-{size}`

## Files to Create/Modify

1. **`database/seeders/ProductSeeder.php`** — Main seeder with all product data hardcoded
2. **Copy color swatch PNGs** to `storage/app/public/products/colors/`

## How to Run

```bash
# Copy color swatch images first
cp /tmp/chomin-drive/Work/icon/sku/7-88_color19/*.png storage/app/public/products/colors/
cp /tmp/chomin-drive/Work/icon/sku/11002-11055_color31/*.png storage/app/public/products/colors/

# Run seeder
php artisan db:seed --class=ProductSeeder
```

## Totals

| Entity | Count |
|--------|-------|
| Collections | 1 |
| Categories | 1 |
| Products | 30 |
| Product Colors | 1,500 |
| Product Images | 1,500 |
| Product Variants | 13,500 |
| Color Swatch PNGs | 50 |

## Notes

- ราคา 590฿ เป็น placeholder — แก้ไขได้ภายหลังผ่านหน้า Filament admin
- Stock 100 เป็นค่าเริ่มต้น — อัปเดตได้ทีหลัง
- SKU format ตาม Excel spec ทุกประการ
- รูป swatch สีเป็นรูปสี่เหลี่ยมแสดงสีผ้าจริงจากโรงงาน พร้อมรหัสสีมุมขวาล่าง
