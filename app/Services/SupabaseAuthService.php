<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SupabaseAuthService
{
    protected ?string $url;
    protected ?string $key;
    protected ?string $anonKey;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.service_role');
        $this->anonKey = config('services.supabase.anon_key');
    }

    /**
     * Authenticate a user with Supabase Auth (Sign In)
     *
     * @param string $email
     * @param string $password
     * @return array Contains access_token and refresh_token
     * @throws Exception
     */
    public function login(string $email, string $password): array
    {
        if (empty($this->url) || empty($this->anonKey)) {
            throw new Exception("Supabase URL or Anon Key is missing in configuration.");
        }

        $endpoint = "{$this->url}/auth/v1/token?grant_type=password";

        $response = Http::withHeaders([
            'apikey' => $this->anonKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'email' => $email,
            'password' => $password,
        ]);

        if ($response->failed()) {
            $error = $response->json();
            $message = $error['error_description'] ?? $error['msg'] ?? $response->body();
            throw new Exception("Supabase Login Error: " . $message);
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
        ];
    }

    /**
     * Refresh Supabase Session
     *
     * @param string $refreshToken
     * @return array Contains access_token and refresh_token
     * @throws Exception
     */
    public function refreshToken(string $refreshToken): array
    {
        if (empty($this->url) || empty($this->anonKey)) {
            throw new Exception("Supabase URL or Anon Key is missing in configuration.");
        }

        $endpoint = "{$this->url}/auth/v1/token?grant_type=refresh_token";

        $response = Http::withHeaders([
            'apikey' => $this->anonKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'refresh_token' => $refreshToken,
        ]);

        if ($response->failed()) {
            $error = $response->json();
            $message = $error['error_description'] ?? $error['msg'] ?? $response->body();
            throw new Exception("Supabase Refresh Token Error: " . $message);
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'] ?? null,
            'refresh_token' => $data['refresh_token'] ?? null,
        ];
    }

    /**
     * Create a user in Supabase Auth
     *
     * @param string $email
     * @param string $password
     * @param array $metadata
     * @return string User UUID
     * @throws Exception
     */
    public function createUser(string $email, string $password, array $metadata = []): string
    {
        if (empty($this->url) || empty($this->key)) {
            throw new Exception("Supabase URL or Service Role Key is missing in configuration.");
        }

        $endpoint = "{$this->url}/auth/v1/admin/users";

        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'email' => $email,
            'password' => $password,
            'email_confirm' => true,
            'user_metadata' => $metadata,
        ]);

        if ($response->failed()) {
            throw new Exception("Supabase Auth Error: " . $response->body());
        }

        $data = $response->json();
        return $data['id'] ?? throw new Exception("Supabase didn't return a User ID");
    }

    /**
     * Delete a user from Supabase Auth
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteUser(string $uuid): bool
    {
        $endpoint = "{$this->url}/auth/v1/admin/users/{$uuid}";

        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
        ])->delete($endpoint);

        return $response->successful();
    }

    /**
     * Get a user by email from Supabase Auth (Admin API)
     */
    public function getUserByEmail(string $email): ?array
    {
        if (empty($this->url) || empty($this->key)) {
            return null;
        }

        $endpoint = "{$this->url}/auth/v1/admin/users";

        $response = Http::withHeaders([
            'apikey' => $this->key,
            'Authorization' => 'Bearer ' . $this->key,
        ])->get($endpoint);

        if ($response->successful()) {
            $users = $response->json()['users'] ?? [];
            foreach ($users as $user) {
                if (($user['email'] ?? '') === $email) {
                    return $user;
                }
            }
        }

        return null;
    }
}
