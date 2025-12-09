<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    protected $secretKey;
    protected $siteKey;

    public function __construct()
    {
        $this->secretKey = config('recaptcha.secret_key');
        $this->siteKey = config('recaptcha.site_key');
    }

    public function verify($response)
    {
        // Skip verification in local environment for testing
        if (app()->environment('local') && !$response) {
            return true;
        }

        if (empty($response)) {
            return false;
        }

        try {
            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => request()->ip(),
            ]);

            $responseData = $verifyResponse->json();

            Log::info('reCAPTCHA Verification', [
                'success' => $responseData['success'] ?? false,
                'score' => $responseData['score'] ?? null,
                'errors' => $responseData['error-codes'] ?? [],
                'ip' => request()->ip(),
            ]);

            if (config('recaptcha.version', 'v2') === 'v3') {
                return $responseData['success'] && ($responseData['score'] >= config('recaptcha.score_threshold', 0.5));
            }

            return $responseData['success'] ?? false;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification failed', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);

            return false;
        }
    }

    public function getSiteKey()
    {
        return $this->siteKey;
    }

    public function shouldSkip()
    {
        return app()->environment('local') && empty($this->siteKey);
    }
}