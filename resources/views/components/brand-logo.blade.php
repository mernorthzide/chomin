@props(['variant' => 'dark', 'class' => 'h-8'])

@php
    $src = $variant === 'white'
        ? asset('images/brand/chomin-logo-white.png')
        : asset('images/brand/chomin-logo-dark.png');
@endphp

<img src="{{ $src }}" alt="CHOMIN" {{ $attributes->merge(['class' => $class]) }} />
