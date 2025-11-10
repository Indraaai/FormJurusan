@props(['disabled' => false])

<input @disabled($disabled)
    {{ $attributes->merge(['class' => 'border-secondary-300 text-secondary-900 placeholder-secondary-400 focus:border-primary-500 focus:ring-primary-500 rounded-lg shadow-sm disabled:bg-secondary-50 disabled:text-secondary-500 transition-colors']) }}>
