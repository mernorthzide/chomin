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
