<div class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400">
    @foreach (config('app.available_locales') as $loc)
        <a href="{{ route('locale.switch', $loc) }}"
           class="{{ app()->getLocale() === $loc ? 'font-semibold text-gray-900 dark:text-white' : 'hover:underline' }}">
            {{ strtoupper($loc) }}
        </a>
        @if (! $loop->last)
            <span>|</span>
        @endif
    @endforeach
</div>
