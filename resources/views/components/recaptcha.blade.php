@if(!$isDisabled)
    <div class="g-recaptcha my-4" data-sitekey="{{ $siteKey }}"></div>
    
    @error('g-recaptcha-response')
        <span class="text-red-600 text-sm mt-1">{{ $message }}</span>
    @enderror
    
    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endpush
@else
    <!-- reCAPTCHA disabled in local environment -->
    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded my-4">
        <p class="text-sm">reCAPTCHA is disabled in local environment for testing.</p>
    </div>
@endif