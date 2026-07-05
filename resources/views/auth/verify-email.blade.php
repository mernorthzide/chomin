<x-guest-layout>
    <div class="mb-4 text-sm text-brand-gray-medium">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div role="status" aria-live="polite" class="mb-4 font-medium text-sm text-brand-success">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="inline-flex items-center min-h-[44px] px-4 underline text-sm text-brand-gray-medium hover:text-brand-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-black">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
