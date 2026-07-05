# Progress

_Last updated: 2026-07-05_

## Current sprint
**ไม่มี sprint ที่ active — คิวว่าง.** งานลูกค้าทั้งชุด (Shopee catalogue + brief content + Drive images) เสร็จครบ **F001–F004 evaluator PASS ทั้งหมด** (2026-07-05).

### สรุปงานที่เสร็จ (2026-07-05)
- **F001** ลด size claim ทั้งไซต์ → S/M/L/XL (ตรง inventory จริง; ตัด XS–6XL + custom-size). size-recommender clamp S–XL.
- **F002** legal/policy copy: IP clause (terms), PDPA non-disclosure + EN privacy production copy, damaged-goods 3-วัน+แนบภาพ (returns) th+en. migration returns 30→7 วัน.
- **F003** brief parity: home 6-card selling grid + 5 personas + Made For You + value-prop; about+seeder placket (ดีเทล=4); FAQ 5→8 (size-list/size-consult/care). i18n th/en.
- **F004** Drive images (2 รูป): flat-lay→About hero, models→home hero. **user ยอมรับ "XS-6XL" ที่ฝังใน pixel ของ models image** (ต่างจาก text S–XL บนเว็บ).

### ⚠️ เรื่องที่ค้าง / ต้องรู้
- **ยังไม่ commit** — รอ user สั่ง. working tree มี dirty เก่า**ก่อน session นี้** (auth/profile/navbar/modal/about "By the Numbers"/meta theme-color ฯลฯ) ที่ไม่เกี่ยว F001–F004 — **อย่าเหมา commit รวม**; แยกไฟล์ของ sprint เท่านั้น.
- **รูป F004 gitignored** (storage/) — local asset; deploy รูปทำแยกเหมือน brand image อื่น (ดู [[project_shopee_sync]] flow).
- **APP_URL=http://127.0.0.1:8001** — dev server ต้องรันบน :8001 ถึงจะเห็นรูป storage (บน :8000 รูป 404 ทุกตัว).

### Ground truth (audit wf_55b0f317-ff0, 2026-07-05)
- catalogue: import ครบ 6 family / **64 สี** (">50 สี" จริง) / ราคา ฿1,190 (PINSTRIPE ฿1,390) ถูกต้อง — **แต่ variant ซื้อได้แค่ S/M/L/XL** ขณะที่ทั้งไซต์โฆษณา XS–6XL → F001 แก้ให้ตรง
- decision (user): ลด claim เหลือ S/M/L/XL (ไม่เพิ่ม variant / ไม่ทำ made-to-order). คง custom-DESIGN + 50+ สี
- ทั้ง 5 area (home/about/policies/faq/catalogue) เป็น **partial** ไม่มีอันไหน done

## State a fresh session needs
- **Start dev:** `composer dev` — รัน `php artisan serve` (http://127.0.0.1:8000), `queue:listen`, `pail`, และ `vite` พร้อมกัน
  - Storefront: `http://127.0.0.1:8000/th` (ทุก route ต้องมี locale prefix `th|en`; bare path redirect ไป `/th`)
  - Admin (Filament 4): `http://127.0.0.1:8000/admin` — ครั้งแรกต้อง `php artisan shield:super-admin --user=1`, login `admin@chomin.com` / `password`
- **Verify:** `./vendor/bin/pint --test --dirty && php artisan test && npm run build`
- **E2E mode:** playwright-mcp (ขับ browser จริงผ่าน Playwright MCP)

## Environment facts
- **DB:** MySQL (`DB_CONNECTION=mysql`, database `chomin`, root/empty @ 127.0.0.1:3306) — migrated + seeded อยู่แล้ว. rebuild: `php artisan migrate` → `php artisan db:seed` (DatabaseSeeder ใช้ ShopeeProductSeeder เป็น catalogue จริง — 6 colour families จาก `database/seeders/data/shopee.json`). **อย่ารันทั้ง ShopeeProductSeeder และ ProductSeeder** — แต่ละตัว deactivate ของอีกตัว
- **Tests:** รันบน sqlite `:memory:` + `RefreshDatabase` (แยกจาก MySQL dev DB), ไม่ต้องพึ่ง external service
- **Mail:** `MAIL_MAILER=log` + `QUEUE_CONNECTION=database` — checkout dispatch queued mail, `composer dev` queue:listen เก็บให้ mail ลง `storage/logs`
- **Payment เปิด default:** `promptpay_slip` + `cod` (bank_transfer ปิด). COD fee 30, min 0 / max 50000 (config/chomin.php)
- **Coupons / gift_cards seed ว่างเปล่า** — ต้องสร้างใน admin panel ก่อนทดสอบ flow ที่เกี่ยวข้อง

## Known loose ends
- **Lint baseline แดง:** `./vendor/bin/pint --test` ปัจจุบัน fail บน ~11 ไฟล์ legacy (database/migrations, database/seeders/*, bootstrap/providers.php, config/permission.php, deploy.php) ที่ไม่เกี่ยวกับงานใหม่. VERIFY จึงใช้ `--dirty` เพื่อ lint เฉพาะไฟล์ที่ sprint แตะ. ถ้าจะ normalize baseline: รัน `./vendor/bin/pint` (จะ reformat 11 ไฟล์) แยกเป็น sprint ต่างหาก — อย่าเหมารวมกับงานฟีเจอร์
- **Test brittleness:** หลาย test assert กับ Blade markup / CSS-class string (เช่น `test_storefront_interaction_markup_covers_audit_fixes`) — UI refactor อาจทำ test แดงโดยไม่ใช่ regression จริง evaluator ต้องแยกแยะ
