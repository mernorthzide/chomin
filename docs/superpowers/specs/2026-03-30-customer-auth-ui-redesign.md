# Customer Auth UI Redesign — Full-Page Premium Split Layout

**Date:** 2026-03-30
**Status:** Approved
**Approach:** B — Full-Page Premium Split Layout

---

## Overview

ปรับปรุงหน้า login, register, และ forgot-password ของลูกค้าให้เข้ากับแบรนด์ CHOMIN (Thai Premium Fashion) โดยเปลี่ยนจาก Breeze template เดิม (ภาษาอังกฤษ, สี indigo, พื้นเทา) เป็น split layout แบบพรีเมียมภาษาไทย

Admin login ที่ `/admin` ไม่เกี่ยวข้อง — Filament จัดการแยกอยู่แล้ว

---

## Design

### Guest Layout (Split Layout)

**Desktop (md+):**
- แบ่งหน้าจอ 2 ฝั่ง 50/50
- **ฝั่งซ้าย:** พื้น `bg-brand-black` + gradient overlay (placeholder สำหรับรูปแฟชัน) + ข้อความ "CHOMIN" font-serif ตัวใหญ่ + tagline "Thai Premium Fashion"
- **ฝั่งขวา:** พื้นขาว + logo "CHOMIN" tracking-wide ด้านบน + ฟอร์มตรงกลาง

**มือถือ:**
- ซ่อนฝั่งซ้ายทั้งหมด
- แสดงแค่ฟอร์มบนพื้นขาว + logo "CHOMIN" ด้านบน

### หน้า Login (`/login`)

**องค์ประกอบ:**
- Logo: "CHOMIN" — font-serif, tracking-[0.2em]
- Heading: "เข้าสู่ระบบ" — h2, font-serif
- Subtitle: "ยินดีต้อนรับกลับมา" — text-brand-gray-medium
- ฟิลด์:
  - อีเมล (required)
  - รหัสผ่าน (required)
- Checkbox: "จดจำฉัน" + ลิงก์ "ลืมรหัสผ่าน?" (flex justify-between)
- ปุ่ม: "เข้าสู่ระบบ" — bg-brand-brown, text-white, tracking-wide
- Divider: "─── หรือ ───"
- ลิงก์: "ยังไม่มีบัญชี? สมัครสมาชิก" — text-brand-brown → `/register`

### หน้า Register (`/register`)

**องค์ประกอบ:**
- Logo: "CHOMIN" — font-serif, tracking-[0.2em]
- Heading: "สมัครสมาชิก" — h2, font-serif
- Subtitle: "สร้างบัญชีเพื่อช้อปปิ้ง" — text-brand-gray-medium
- ฟิลด์:
  - ชื่อ-นามสกุล (required)
  - อีเมล (required)
  - เบอร์โทรศัพท์ (optional — แสดง "ไม่บังคับ" ใน label)
  - รหัสผ่าน (required)
  - ยืนยันรหัสผ่าน (required)
- ปุ่ม: "สมัครสมาชิก" — bg-brand-brown, text-white, tracking-wide
- Divider: "─── หรือ ───"
- ลิงก์: "มีบัญชีอยู่แล้ว? เข้าสู่ระบบ" — text-brand-brown → `/login`

### หน้า Forgot Password (`/forgot-password`)

- แปลภาษาไทย + ปรับสีให้ตรงกับ login/register
- Heading: "ลืมรหัสผ่าน"
- คำอธิบาย: "กรุณากรอกอีเมลของคุณ เราจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่"
- ปุ่ม: "ส่งลิงก์รีเซ็ตรหัสผ่าน"

---

## Styling

| Element | Value |
|---------|-------|
| Input border | `border-brand-gray-border` |
| Input focus | `focus:ring-brand-brown focus:border-brand-brown` |
| ปุ่มหลัก | `bg-brand-brown text-white hover:bg-opacity-90` |
| ลิงก์ | `text-brand-brown hover:underline` |
| Validation error | `text-red-500 text-sm` |
| Font heading | `font-serif` (Playfair Display) |
| Font body | `font-sans` (IBM Plex Sans Thai) |

---

## Files to Modify

| File | Changes |
|------|---------|
| `resources/views/layouts/guest.blade.php` | เปลี่ยนเป็น split layout, เพิ่ม Vite assets, เพิ่มรูป/gradient ฝั่งซ้าย |
| `resources/views/auth/login.blade.php` | แปลไทย, ปรับสี brand, เพิ่มลิงก์สมัครสมาชิก + divider |
| `resources/views/auth/register.blade.php` | แปลไทย, ปรับสี brand, ปรับลิงก์เข้าสู่ระบบ + divider |
| `resources/views/auth/forgot-password.blade.php` | แปลไทย, ปรับสี brand |

## Files NOT Modified

- Controllers — logic ไม่เปลี่ยน
- Routes — ไม่เปลี่ยน
- Admin panel (`/admin`) — Filament จัดการแยก
- Navbar, Footer — ไม่เกี่ยว
- Blade components (text-input, input-label ฯลฯ) — ใช้ inline Tailwind classes แทน

## Image Placeholder

- ฝั่งซ้ายใช้ `bg-brand-black` + gradient overlay ไปก่อน
- สามารถเปลี่ยนเป็นรูปจริงทีหลังโดยเพิ่ม `background-image` ใน CSS หรือ inline style
