<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class McpTokenController extends Controller
{
    public const NOMBRE_TOKEN = 'mcp-claude-connector';

    /** Genera (o regenera) el token MCP del usuario. Solo puede haber uno vigente. */
    public function store(Request $request): RedirectResponse
    {
        $request->user()->tokens()->where('name', self::NOMBRE_TOKEN)->delete();

        $plainTextToken = $request->user()
            ->createToken(self::NOMBRE_TOKEN)
            ->plainTextToken;

        // Sanctum no vuelve a mostrar el valor una vez creado: viaja UNA sola vez
        // por la sesion flash, nunca se guarda en la base de datos en texto plano.
        return redirect()->route('perfil.edit')->with('mcpToken', $plainTextToken);
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->user()->tokens()->where('name', self::NOMBRE_TOKEN)->delete();

        return redirect()->route('perfil.edit')->with('status', 'mcp-token-revocado');
    }
}
