# Customer Auth UI Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the customer login, register, and forgot-password pages with a premium split layout matching the CHOMIN brand (Thai language, brand colors, font-serif headings).

**Architecture:** Replace the default Laravel Breeze guest layout with a full-page split layout (image panel left, form right on desktop; form-only on mobile). All three auth views get Thai labels, brand-brown styling, and cross-navigation links.

**Tech Stack:** Laravel Blade, Tailwind CSS, IBM Plex Sans Thai (font-sans), Playfair Display (font-serif), Alpine.js (existing)

---

## File Structure

| File | Role | Action |
|------|------|--------|
| `resources/views/layouts/guest.blade.php` | Guest layout wrapper for all auth pages | Rewrite — split layout |
| `resources/views/auth/login.blade.php` | Customer login form | Rewrite — Thai, brand styling, register link |
| `resources/views/auth/register.blade.php` | Customer registration form | Rewrite — Thai, brand styling, login link |
| `resources/views/auth/forgot-password.blade.php` | Password reset request form | Rewrite — Thai, brand styling |

No new files created. No controllers, routes, or other files modified.

---

### Task 1: Rewrite Guest Layout — Split Layout

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`

- [ ] **Step 1: Replace the entire guest layout with the split layout**

Replace the full contents of `resources/views/layouts/guest.blade.php` with:

```blade
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CHOMIN') }}</title>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-brand-black">

    <div class="min-h-screen flex">

        <!-- Left Panel: Brand Image (hidden on mobile) -->
        <div class="hidden md:flex md:w-1/2 bg-brand-black relative items-center justify-center">
            <!-- Gradient overlay placeholder for future brand image -->
            <div class="absolute inset-0 bg-gradient-to-br from-brand-black via-brand-brown/30 to-brand-black"></div>

            <!-- Brand content -->
            <div class="relative z-10 text-center px-12">
                <h1 class="font-serif text-5xl lg:text-6xl font-normal text-white tracking-[0.2em] mb-4">
                    CHOMIN
                </h1>
                <p class="text-sm text-white/60 tracking-[0.15em] uppercase">
                    Thai Premium Fashion
                </p>
            </div>
        </div>

        <!-- Right Panel: Form -->
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center px-6 py-12 sm:px-12">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>
</html>
```

- [ ] **Step 2: Verify the layout renders**

Open `/login` in a browser. You should see:
- Desktop: split layout with dark left panel (gradient + "CHOMIN" text) and white right panel
- Mobile: only the white form panel, no left panel

- [ ] **Step 3: Commit**

```bash
git add resources/views/layouts/guest.blade.php
git commit -m "feat: rewrite guest layout as premium split layout for CHOMIN brand"
```

---

### Task 2: Rewrite Login Page

**Files:**
- Modify: `resources/views/auth/login.blade.php`

- [ ] **Step 1: Replace the entire login view**

Replace the full contents of `resources/views/auth/login.blade.php` with:

```blade
<x-guest-layout>
    <!-- Logo (visible on mobile, hidden on desktop since left panel has it) -->
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <span class="font-serif text-3xl font-normal tracking-[0.2em] text-brand-black">CHOMIN</span>
        </a>
    </div>

    <!-- Heading -->
    <div class="mb-8">
        <h2 class="font-serif text-2xl text-brand-black mb-1">เข้าสู่ระบบ</h2>
        <p class="text-sm text-brand-gray-medium">ยินดีต้อนรับกลับมา</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-brand-gray-dark mb-1">อีเมล</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-brand-gray-dark mb-1">รหัสผ่าน</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-brand-gray-border text-brand-brown focus:ring-brand-brown">
                <span class="ms-2 text-sm text-brand-gray-medium">จดจำฉัน</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-brand-brown hover:underline" href="{{ route('password.request') }}">
                    ลืมรหัสผ่าน?
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                    class="w-full bg-brand-brown text-white text-sm font-medium tracking-wide py-3 rounded hover:bg-opacity-90 transition-all duration-200">
                เข้าสู่ระบบ
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div class="flex items-center my-6">
        <div class="flex-1 border-t border-brand-gray-border"></div>
        <span class="px-4 text-xs text-brand-gray-light">หรือ</span>
        <div class="flex-1 border-t border-brand-gray-border"></div>
    </div>

    <!-- Register Link -->
    <p class="text-center text-sm text-brand-gray-medium">
        ยังไม่มีบัญชี?
        <a href="{{ route('register') }}" class="text-brand-brown font-medium hover:underline">สมัครสมาชิก</a>
    </p>
</x-guest-layout>
```

- [ ] **Step 2: Verify in browser**

Open `/login`. Check:
- Logo "CHOMIN" shows at top of form
- All labels are in Thai
- "ลืมรหัสผ่าน?" link works → goes to `/forgot-password`
- "สมัครสมาชิก" link works → goes to `/register`
- Input focus ring is brown (not indigo)
- Button is `brand-brown`

- [ ] **Step 3: Commit**

```bash
git add resources/views/auth/login.blade.php
git commit -m "feat: redesign login page with Thai labels, brand styling, and register link"
```

---

### Task 3: Rewrite Register Page

**Files:**
- Modify: `resources/views/auth/register.blade.php`

- [ ] **Step 1: Replace the entire register view**

Replace the full contents of `resources/views/auth/register.blade.php` with:

```blade
<x-guest-layout>
    <!-- Logo (visible on mobile, hidden on desktop since left panel has it) -->
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <span class="font-serif text-3xl font-normal tracking-[0.2em] text-brand-black">CHOMIN</span>
        </a>
    </div>

    <!-- Heading -->
    <div class="mb-8">
        <h2 class="font-serif text-2xl text-brand-black mb-1">สมัครสมาชิก</h2>
        <p class="text-sm text-brand-gray-medium">สร้างบัญชีเพื่อช้อปปิ้ง</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-brand-gray-dark mb-1">ชื่อ-นามสกุล</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-brand-gray-dark mb-1">อีเมล</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <label for="phone" class="block text-sm font-medium text-brand-gray-dark mb-1">
                เบอร์โทรศัพท์ <span class="text-brand-gray-light font-normal">(ไม่บังคับ)</span>
            </label>
            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-brand-gray-dark mb-1">รหัสผ่าน</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-brand-gray-dark mb-1">ยืนยันรหัสผ่าน</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                    class="w-full bg-brand-brown text-white text-sm font-medium tracking-wide py-3 rounded hover:bg-opacity-90 transition-all duration-200">
                สมัครสมาชิก
            </button>
        </div>
    </form>

    <!-- Divider -->
    <div class="flex items-center my-6">
        <div class="flex-1 border-t border-brand-gray-border"></div>
        <span class="px-4 text-xs text-brand-gray-light">หรือ</span>
        <div class="flex-1 border-t border-brand-gray-border"></div>
    </div>

    <!-- Login Link -->
    <p class="text-center text-sm text-brand-gray-medium">
        มีบัญชีอยู่แล้ว?
        <a href="{{ route('login') }}" class="text-brand-brown font-medium hover:underline">เข้าสู่ระบบ</a>
    </p>
</x-guest-layout>
```

- [ ] **Step 2: Verify in browser**

Open `/register`. Check:
- All labels in Thai
- Phone field shows "(ไม่บังคับ)" in the label
- "เข้าสู่ระบบ" link works → goes to `/login`
- Submit button is `brand-brown`
- Validation errors appear in Thai after submitting empty form

- [ ] **Step 3: Commit**

```bash
git add resources/views/auth/register.blade.php
git commit -m "feat: redesign register page with Thai labels, brand styling, and login link"
```

---

### Task 4: Rewrite Forgot Password Page

**Files:**
- Modify: `resources/views/auth/forgot-password.blade.php`

- [ ] **Step 1: Replace the entire forgot-password view**

Replace the full contents of `resources/views/auth/forgot-password.blade.php` with:

```blade
<x-guest-layout>
    <!-- Logo -->
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <span class="font-serif text-3xl font-normal tracking-[0.2em] text-brand-black">CHOMIN</span>
        </a>
    </div>

    <!-- Heading -->
    <div class="mb-8">
        <h2 class="font-serif text-2xl text-brand-black mb-1">ลืมรหัสผ่าน</h2>
        <p class="text-sm text-brand-gray-medium">กรุณากรอกอีเมลของคุณ เราจะส่งลิงก์สำหรับตั้งรหัสผ่านใหม่</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-brand-gray-dark mb-1">อีเมล</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="block w-full rounded border-brand-gray-border focus:ring-brand-brown focus:border-brand-brown text-sm py-2.5" />
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                    class="w-full bg-brand-brown text-white text-sm font-medium tracking-wide py-3 rounded hover:bg-opacity-90 transition-all duration-200">
                ส่งลิงก์รีเซ็ตรหัสผ่าน
            </button>
        </div>
    </form>

    <!-- Back to Login -->
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-brand-brown hover:underline">
            ← กลับไปหน้าเข้าสู่ระบบ
        </a>
    </div>
</x-guest-layout>
```

- [ ] **Step 2: Verify in browser**

Open `/forgot-password`. Check:
- Heading "ลืมรหัสผ่าน" displays correctly
- Description text is in Thai
- Button text "ส่งลิงก์รีเซ็ตรหัสผ่าน"
- "กลับไปหน้าเข้าสู่ระบบ" link → `/login`

- [ ] **Step 3: Commit**

```bash
git add resources/views/auth/forgot-password.blade.php
git commit -m "feat: redesign forgot-password page with Thai labels and brand styling"
```

---

### Task 5: Final Visual Verification

- [ ] **Step 1: Build production assets**

```bash
npm run build
```

Expected: Vite build succeeds with no errors.

- [ ] **Step 2: Cross-page navigation check**

Test the full flow in browser:
1. Go to `/login` → click "สมัครสมาชิก" → lands on `/register`
2. On `/register` → click "เข้าสู่ระบบ" → lands on `/login`
3. On `/login` → click "ลืมรหัสผ่าน?" → lands on `/forgot-password`
4. On `/forgot-password` → click "กลับไปหน้าเข้าสู่ระบบ" → lands on `/login`
5. Resize to mobile width → left panel disappears, form fills full width
6. Go to `/admin` → Filament login page still works independently

- [ ] **Step 3: Commit assets if needed**

```bash
git add public/build/
git commit -m "build: compile production assets for auth UI redesign"
```
