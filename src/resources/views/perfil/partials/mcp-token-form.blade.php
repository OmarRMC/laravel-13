<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('MCP connector token') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Paste it as the :header value in Claude.ai, ChatGPT, Codex CLI or Gemini CLI. It never expires: treat it like a password.', ['header' => 'Authorization']) }}
        </p>
    </header>

    @if (session('mcpToken'))
        <div x-data="{ copiado: false }" class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg space-y-2">
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                {{ __('Copy it now: it will not be shown again.') }}
            </p>

            <div class="flex gap-2">
                <input type="text" readonly value="Bearer {{ session('mcpToken') }}" x-ref="token"
                       class="flex-1 font-mono text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">

                <x-secondary-button
                    type="button"
                    x-on:click="navigator.clipboard.writeText($refs.token.value); copiado = true"
                >
                    <span x-show="!copiado">{{ __('Copy') }}</span>
                    <span x-show="copiado">{{ __('Copied') }} ✓</span>
                </x-secondary-button>
            </div>
        </div>
    @elseif ($tieneMcpToken)
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('You already have an active token (value hidden).') }}
        </p>
    @endif

    @if (session('status') === 'mcp-token-revocado')
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Token revoked.') }}</p>
    @endif

    <div class="flex items-center gap-3">
        <form method="post" action="{{ route('mcp-token.store') }}">
            @csrf
            <x-primary-button>
                {{ $tieneMcpToken ? __('Regenerate token') : __('Generate token') }}
            </x-primary-button>
        </form>

        @if ($tieneMcpToken)
            <form method="post" action="{{ route('mcp-token.destroy') }}">
                @csrf
                @method('delete')
                <x-danger-button>{{ __('Revoke') }}</x-danger-button>
            </form>
        @endif
    </div>
</section>
