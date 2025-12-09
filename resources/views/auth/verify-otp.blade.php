<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <h2 class="text-lg font-semibold mb-2">Verify Your Email</h2>
        <p>We've sent a 6-digit OTP code to <strong>{{ auth()->user()->email ?? 'your email' }}</strong></p>
        <p>Please check your email and enter the code below. The code will expire in {{ config('otp.expiry', 5) }} minutes.</p>
        
        @if (session('status'))
            <div class="mt-2 p-3 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif
        
        <!-- Debug info (remove in production) -->
        @if(env('APP_DEBUG'))
            <div class="mt-3 p-2 bg-yellow-100 text-yellow-800 text-sm rounded">
                <strong>Debug Info:</strong><br>
                User: {{ auth()->user()->email }}<br>
                OTP Sent: {{ auth()->user()->otp ? 'Yes' : 'No' }}<br>
                OTP Purpose: {{ auth()->user()->otp_purpose ?? 'None' }}
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf

        <!-- OTP Code -->
        <div class="mt-4">
            <x-input-label for="otp" :value="__('OTP Code')" />
            <x-text-input 
                id="otp" 
                class="block mt-1 w-full text-center text-2xl tracking-widest" 
                type="text" 
                name="otp" 
                required 
                autofocus 
                maxlength="6"
                pattern="\d{6}"
                placeholder="123456"
                autocomplete="off"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <div class="flex items-center space-x-4">
                <form method="POST" action="{{ route('otp.resend') }}">
                    @csrf
                    <button type="submit" 
                            class="text-sm text-blue-600 hover:text-blue-900 underline">
                        {{ __('Resend OTP') }}
                    </button>
                </form>
                
                @if(env('APP_DEBUG'))
                <a href="/debug-otp-status" target="_blank" 
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    Debug
                </a>
                @endif
            </div>

            <x-primary-button>
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>

    @if(auth()->check())
    <div class="mt-6 pt-6 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    class="text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
    @endif
</x-guest-layout>