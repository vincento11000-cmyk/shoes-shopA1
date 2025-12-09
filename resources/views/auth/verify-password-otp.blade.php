<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        <h2 class="text-lg font-semibold mb-2">Verify Your Identity</h2>
        <p>We've sent a 6-digit OTP code to <strong>{{ $email }}</strong></p>
        <p>Please enter the OTP to reset your password.</p>
        <p class="text-red-600 text-sm mt-2">The OTP expires in 10 minutes.</p>
        
        @if (session('status'))
            <div class="mt-2 p-3 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif
    </div>

    <form method="POST" action="{{ route('password.verify.otp.submit') }}">
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
                <a href="{{ route('password.request') }}" 
                   class="text-sm text-gray-600 hover:text-gray-900 underline">
                    {{ __('Use different email') }}
                </a>
                
                <form method="POST" action="{{ route('password.resend.otp') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="text-sm text-blue-600 hover:text-blue-900 underline">
                        {{ __('Resend OTP') }}
                    </button>
                </form>
            </div>

            <x-primary-button>
                {{ __('Verify OTP') }}
            </x-primary-button>
        </div>
    </form>

    <!-- DEBUG: Show OTP in development -->
    @if(env('APP_DEBUG'))
        @php
            $user = \App\Models\User::where('email', $email)->first();
        @endphp
        @if($user && $user->otp)
        <div class="mt-4 p-3 bg-yellow-100 text-yellow-800 text-sm rounded">
            <strong>DEBUG MODE:</strong> OTP is: <strong>{{ $user->otp }}</strong>
        </div>
        @endif
    @endif

    <div class="mt-6 pt-6 border-t border-gray-200">
        <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
            {{ __('Back to login') }}
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp');
            
            // Only allow numbers
            otpInput.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').slice(0, 6);
            });
            
            // Auto-focus and select
            otpInput.focus();
            otpInput.select();
        });
    </script>
</x-guest-layout>