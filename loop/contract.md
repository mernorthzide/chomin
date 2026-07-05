# Contract — F004 Drive image integration

- **Sprint:** F004 · **ร่างเมื่อ:** 2026-07-05 · **rev 1**
- **E2E mode:** playwright-mcp (home /th+/en, about /th+/en) — **server ต้องรันบน port ที่ตรง APP_URL (`http://127.0.0.1:8001`)** ไม่งั้น Storage::url() images 404 (dev-env quirk, กระทบทุกรูปเท่ากัน)

## Spec summary
ลูกค้าส่งโฟลเดอร์ Drive "ภาพเว็บไซต์ CM" — มี **2 รูป** (IMG_8216 flat-lay, IMG_8217 models). ดึงผ่าน browser-harness (Chrome ล็อกอิน) แล้ววางตามที่ user สั่ง.

## Decisions baked (user)
- IMG_8216 (flat-lay "Design Your Own Shirt", landscape, self-branded) → **About hero** (full-width image section บนสุดของ about, ก่อน "The Brand" hero เดิม).
- IMG_8217 (models lookbook, portrait, self-branded) → **home hero** (image section บนสุดของ home, ก่อน campaign-hero). **user เลือกใช้ทั้งภาพ (ยอมรับ "XS - 6XL SIZE" ที่ฝังใน pixel ของภาพ)**.
- ไฟล์เก็บที่ `storage/app/public/products/chomin-imagen/from-client/` (`brand-flatlay-design-your-own.jpg`, `models-lookbook-unisex.jpg`) — gitignored (local asset เหมือน brand image อื่นของโปรเจกต์).
- **alt text ห้ามใส่เลขไซซ์** (กันเพิ่ม claim XS-6XL เป็น TEXT ใน HTML) — XS-6XL อยู่ได้เฉพาะใน pixel ของภาพตามที่ user ยอมรับ.

## Checklist (F004)
- **C01** ไฟล์รูป 2 ไฟล์อยู่ที่ `storage/app/public/products/chomin-imagen/from-client/` และ serve ได้ (HTTP 200) บน port APP_URL.
- **C02** home มี `<img>` ชี้ `from-client/models-lookbook-unisex.jpg` เป็น section บนสุด (ก่อน campaign-hero); playwright: รูป **โหลดจริง** (naturalWidth > 0) บน `/th` และ `/en`.
- **C03** about มี `<img>` ชี้ `from-client/brand-flatlay-design-your-own.jpg` เป็น section บนสุด (ก่อน "The Brand" hero); playwright: รูปโหลดจริง (naturalWidth > 0) บน `/th` และ `/en`.
- **C04** ไม่มี **TEXT** `XS.{0,4}6XL` ใน rendered HTML ของ home+about (grep rendered = 0); "XS-6XL" อยู่ได้เฉพาะใน pixel ของภาพ (ไม่ใช่ alt/markup).
- **C05** no-regression F001–F003: home ยังมี campaign-hero `Design Your Own Shirt`, 6-card grid, personas; about ยังมี "The Brand" hero + placket=4; topbar/hero ยังโชว์ S–XL.
- **C06** layout ไม่พัง: ที่ desktop และ 375px ไม่มี horizontal overflow (`scrollWidth <= innerWidth+1`); รูป portrait บน home ไม่สูงเกิน (มี max-width).
- **C07** VERIFY เขียว: `./vendor/bin/pint --test --dirty && php artisan test && npm run build` (81 tests).

> presentational sprint — grade ด้วย playwright (รูปโหลดจริง) + no-regress + verify.
