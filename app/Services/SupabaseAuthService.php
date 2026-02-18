<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SupabaseAuthService
{
    protected ?string $url;
    protected ?string $key;

    public function __construct()
    {
        $this->url = config('services.supabase.url');
        $this->key = config('services.supabase.service_role');
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
