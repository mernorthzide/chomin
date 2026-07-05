@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-brand-gray-medium']) }}>
    {{ $value ?? $slot }}
</label>
