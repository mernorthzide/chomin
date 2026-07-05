<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-danger border border-transparent font-semibold text-xs text-white uppercase tracking-widest hover:opacity-90 active:bg-brand-danger focus:outline-none focus:ring-2 focus:ring-brand-danger focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
