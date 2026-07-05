<section>
    <header>
        <h2 class="text-lg font-medium text-brand-black">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-brand-gray-medium">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name"
                @error('name') aria-describedby="name-error" aria-invalid="true" @enderror />
            <x-input-error id="name-error" class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username"
                @error('email') aria-describedby="email-error" aria-invalid="true" @enderror />
            <x-input-error id="email-error" class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-brand-black">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-brand-gray-medium hover:text-brand-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-black">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p role="status" aria-live="polite" class="mt-2 font-medium text-sm text-brand-success">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    role="status"
                    aria-live="polite"
                    class="text-sm text-brand-gray-medium"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
