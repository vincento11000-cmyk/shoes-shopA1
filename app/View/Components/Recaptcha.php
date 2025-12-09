<?php

namespace App\View\Components;

use App\Services\RecaptchaService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Recaptcha extends Component
{
    public $siteKey;
    public $isDisabled;

    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->siteKey = $recaptchaService->getSiteKey();
        $this->isDisabled = $recaptchaService->shouldSkip();
    }

    public function render(): View|Closure|string
    {
        return view('components.recaptcha');
    }
}