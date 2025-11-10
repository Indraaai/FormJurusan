<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-accent-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:from-primary-700 hover:to-accent-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed shadow-soft hover:shadow-soft-lg transition-all duration-200']) }}>
    {{ $slot }}
</button>
