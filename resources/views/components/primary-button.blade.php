<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' => '
            inline-flex items-center justify-center
            px-6 py-3
            bg-primary-600 hover:bg-primary-700
            border border-transparent
            rounded-lg
            font-semibold text-sm text-white
            focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
            active:scale-[0.98]
            disabled:opacity-50 disabled:cursor-not-allowed
            shadow-sm hover:shadow-md
            transition
        ',
    ]) }}>
    {{ $slot }}
</button>
