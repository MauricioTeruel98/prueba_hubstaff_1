<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HubstaffAuthController extends Controller
{
    public function redirect()
    {
        $state = Str::random(40);
        session(['hubstaff_state' => $state]);

        $params = [
            'client_id' => config('services.hubstaff.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.hubstaff.redirect_uri'),
            // Simplificamos los scopes
            'scope' => 'hubstaff:read hubstaff:write',
            'state' => $state,
            'nonce' => Str::random(40)
        ];

        \Log::info('Hubstaff Auth URL:', [
            'params' => $params,
            'full_url' => "https://account.hubstaff.com/authorizations/new?" . http_build_query($params)
        ]);

        $url = "https://account.hubstaff.com/authorizations/new?" . http_build_query($params);

        return redirect()->away($url);
    }

    public function callback(Request $request)
    {
        try {
            if ($request->has('error')) {
                \Log::error('Hubstaff Auth Error:', [
                    'error' => $request->error,
                    'description' => $request->error_description
                ]);
                throw new \Exception($request->error_description ?? 'Error de autorización');
            }

            \Log::info('Hubstaff Auth Callback:', [
                'code' => $request->code,
                'state' => $request->state
            ]);

            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->asForm()
            ->post('https://account.hubstaff.com/access_tokens', [
                'client_id' => config('services.hubstaff.client_id'),
                'client_secret' => config('services.hubstaff.client_secret'),
                'code' => $request->code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.hubstaff.redirect_uri'),
            ]);

            if (!$response->successful()) {
                \Log::error('Hubstaff Token Error:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request_data' => [
                        'client_id' => config('services.hubstaff.client_id'),
                        'grant_type' => 'authorization_code',
                        'redirect_uri' => config('services.hubstaff.redirect_uri'),
                        'code_length' => strlen($request->code),
                    ]
                ]);
                throw new \Exception('Error al obtener el token de acceso');
            }

            $data = $response->json();
            \Log::info('Hubstaff Token Success:', [
                'expires_in' => $data['expires_in'] ?? null,
                'token_type' => $data['token_type'] ?? null
            ]);

            // Guardar el token en cache
            Cache::put('hubstaff_access_token', $data['access_token'], now()->addSeconds($data['expires_in']));
            Cache::put('hubstaff_refresh_token', $data['refresh_token'], now()->addDays(30));

            return redirect()->route('dashboard')->with('success', 'Conexión con Hubstaff establecida exitosamente');
        } catch (\Exception $e) {
            \Log::error('Hubstaff Auth Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function refreshToken()
    {
        try {
            $refreshToken = Cache::get('hubstaff_refresh_token');

            if (!$refreshToken) {
                throw new \Exception('No hay token de actualización disponible');
            }

            $response = Http::withOptions([
                'verify' => !app()->environment('local')
            ])->post('https://account.hubstaff.com/access_tokens', [
                'client_id' => config('services.hubstaff.client_id'),
                'client_secret' => config('services.hubstaff.client_secret'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            if (!$response->successful()) {
                throw new \Exception('Error al actualizar el token');
            }

            $data = $response->json();

            Cache::put('hubstaff_access_token', $data['access_token'], now()->addSeconds($data['expires_in']));
            Cache::put('hubstaff_refresh_token', $data['refresh_token'], now()->addDays(30));

            return response()->json(['message' => 'Token actualizado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
