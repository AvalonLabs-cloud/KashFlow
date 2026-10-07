<?php

declare(strict_types=1);

namespace App\Service;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service to handle Flutterwave OAuth2 Access Token Generation
 * Documentation: https://developer.flutterwave.com/docs/authentication
 */
class TokenGenerationService
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $authUrl;

    public function __construct()
    {
        $this->clientId = config('flutterwave.client_id');
        $this->clientSecret = config('flutterwave.secret_key');
        $this->authUrl = config('flutterwave.token_generation_url');
    }

    /**
     * Get a valid access token.
     * Uses Cache to prevent unnecessary API calls within the token's lifespan.
     *
     * @throws RequestException
     */
    public function getAccessToken(): string
    {
        return Cache::remember('flw_access_token', 420, function () {
            return $this->requestNewToken();
        });
    }

    /**
     * Request a fresh token from Flutterwave IDP
     */
    protected function requestNewToken(): string
    {
        $response = Http::asForm()->post($this->authUrl, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ]);

        if ($response->failed()) {
            Log::info('something failed '.$response);
        }

        $data = $response->json();

        // Flutterwave v4 returns access_token and expires_in (usually 600 seconds)
        return $data['access_token'];
    }

    /**
     * Helper to get the full Authorization header
     */
    public function getAuthHeader(): string
    {
        return 'Bearer '.$this->getAccessToken();
    }
}
