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
