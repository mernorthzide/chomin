<x-guest-layout>
    <!-- Logo (visible on mobile, hidden on desktop since left panel has it) -->
    <div class="text-center mb-8">
        <a href="/" class="inline-block">
            <x-brand-logo variant="dark" class="h-10" />
        </a>
    </div>

    <!-- Heading -->
    <div class="mb-8">
        <h1 class="font-serif text-2xl text-brand-black mb-1">สมัครสมาชิก</h1>
        <p class="text-sm text-brand-gray-medium">สร้างบัญชีเพื่อช้อปปิ้ง</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-brand-gray-dark mb-1">ชื่อ-นามสกุล</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                   class="block w-full rounded-none border-brand-gray-border focus:ring-brand-black focus:border-brand-black text-sm min-h-[44px]" />
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-brand-gray-dark mb-1">อีเมล</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                   class="block w-full rounded-none border-brand-gray-border focus:ring-brand-black focus:border-brand-black text-sm min-h-[44px]" />
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
                   class="block w-full rounded-none border-brand-gray-border focus:ring-brand-black focus:border-brand-black text-sm min-h-[44px]" />
            @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-brand-gray-dark mb-1">รหัสผ่าน</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="block w-full rounded-none border-brand-gray-border focus:ring-brand-black focus:border-brand-black text-sm min-h-[44px]" />
            @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <label for="password_confirmation" class="block text-sm font-medium text-brand-gray-dark mb-1">ยืนยันรหัสผ่าน</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="block w-full rounded-none border-brand-gray-border focus:ring-brand-black focus:border-brand-black text-sm min-h-[44px]" />
            @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                    class="w-full bg-brand-black text-white text-sm font-medium tracking-wide py-3.5 hover:bg-brand-gray-dark transition-colors duration-200">
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
        <a href="{{ route('login') }}" class="text-brand-black font-medium hover:underline">เข้าสู่ระบบ</a>
    </p>
</x-guest-layout>
