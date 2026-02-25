<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseAuthService;
use Illuminate\Support\Facades\Auth;

class SmiabAuthController extends Controller
{
    protected SupabaseAuthService $supabaseService;

    public function __construct(SupabaseAuthService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function refreshToken(Request $request)
    {
        $user = Auth::user();

        // Si no hay refresh_token guardado, no hay nada que renovar
        if (empty($user->smiab_refresh_token)) {
            return response()->json(['error' => 'No refresh token available'], 401);
        }

        try {
            $tokens = $this->supabaseService->refreshToken($user->smiab_refresh_token);

            // Guardamos el nuevo refresh_token en BD
            $user->update([
                'smiab_refresh_token' => $tokens['refresh_token']
            ]);

            // Guardamos el nuevo access_token en sesión
            session(['smiab_access_token' => $tokens['access_token']]);

            return response()->json([
                'access_token' => $tokens['access_token']
            ]);

        }
        catch (\Exception $e) {
            // Si el token expiró irrevocablemente
            return response()->json([
                'error' => 'SMIAB session has fully expired'
            ], 401);
        }
    }
}
