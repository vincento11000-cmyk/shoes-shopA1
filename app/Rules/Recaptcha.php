<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements Rule
{
    public function passes($attribute, $value)
    {
        if (app()->environment('local') && !$value) {
            return true; // Skip in local for testing
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        $responseData = $response->json();

        if (config('recaptcha.version') === 'v3') {
            return $responseData['success'] && $responseData['score'] >= config('recaptcha.score_threshold');
        }

        return $responseData['success'];
    }

    public function message()
    {
        return 'The reCAPTCHA verification failed. Please try again.';
    }
}