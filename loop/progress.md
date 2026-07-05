# Progress

_Last updated: 2026-07-05_

## Current sprint
ยังไม่มี sprint ที่ active

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
