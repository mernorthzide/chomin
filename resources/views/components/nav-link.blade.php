@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-black text-sm font-medium leading-5 text-brand-black focus:outline-none focus:border-brand-black transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-brand-gray-medium hover:text-brand-black hover:border-brand-gray-border focus:outline-none focus:text-brand-black focus:border-brand-gray-border transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
