@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 min-h-[44px] text-start text-base font-medium text-brand-black bg-brand-gray focus:outline-none focus:bg-brand-gray transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 min-h-[44px] text-start text-base font-medium text-brand-gray-medium hover:text-brand-black hover:bg-brand-gray focus:outline-none focus:text-brand-black focus:bg-brand-gray transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
