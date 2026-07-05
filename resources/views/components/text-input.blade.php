@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-brand-gray-border focus:border-brand-black focus:ring-brand-black rounded-none shadow-sm min-h-[44px]']) }}>
