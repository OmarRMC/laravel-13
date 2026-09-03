@if (session('status'))
    <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm text-green-800 dark:text-green-200">
        {{ session('status') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm text-red-800 dark:text-red-200">
        {{ session('error') }}
    </div>
@endif
