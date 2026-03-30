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
                <x-brand-logo variant="white" class="h-16 lg:h-20 mx-auto mb-4" />
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
