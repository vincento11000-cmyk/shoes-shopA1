@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">Checkout</h1>

    {{-- WEATHER WARNING --}}
    @if($weatherWarning)
    <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-semibold text-yellow-800">
                    ⚠️ Weather Alert
                </p>
                <p class="text-sm text-yellow-700 mt-1">
                    {{ $weatherWarning['warning'] }}
                </p>
                @if(isset($weatherWarning['temperature']))
                <p class="text-xs text-yellow-600 mt-1">
                    Current temperature in Manila: {{ $weatherWarning['temperature'] }}°C
                </p>
                @endif
                <p class="text-xs text-yellow-600 mt-2">
                    <i class="fas fa-info-circle mr-1"></i> Please allow extra time for delivery
                </p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- SIMPLE CHECKOUT FORM -->
    <form method="POST" action="" id="checkoutForm">
        @csrf

        <!-- CUSTOMER DETAILS -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Customer Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input type="text" name="name" id="customerName" value="{{ Auth::user()->name }}" 
                           class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="text" name="phone" id="customerPhone" 
                           class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="09XX-XXX-XXXX" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address *</label>
                    <textarea name="address" id="customerAddress" rows="3" 
                              class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              placeholder="Street, Barangay, City, Province" required></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes (Optional)</label>
                    <textarea name="notes" id="customerNotes" rows="2" 
                              class="w-full p-3 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Any special delivery instructions..."></textarea>
                </div>
            </div>
        </div>

        <!-- ORDER SUMMARY -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            
            <div class="space-y-4">
                @foreach($items as $item)
                <div class="flex items-center border-b pb-4">
                    <img src="{{ asset('storage/' . ($item->variant->variant_image ?: $item->product->main_image)) }}" 
                         class="w-20 h-20 object-cover rounded">
                    <div class="ml-4 flex-1">
                        <h3 class="font-semibold">{{ $item->product->name }}</h3>
                        <p class="text-gray-600 text-sm">
                            Size: {{ $item->variant->size }} | 
                            Color: {{ ucfirst($item->variant->color) }}
                        </p>
                        <p class="text-gray-600 text-sm">Quantity: {{ $item->quantity }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">₱{{ number_format($item->product->base_price, 2) }} × {{ $item->quantity }}</p>
                        <p class="font-semibold">₱{{ number_format($item->product->base_price * $item->quantity, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="border-t mt-4 pt-4">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total Amount:</span>
                    <span id="displayTotal">₱{{ number_format($total, 2) }}</span>
                    <span id="codTotal" style="display:none">₱{{ number_format($total + 50, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- PAYMENT METHOD - SIMPLE RADIO BUTTONS -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Payment Method</h2>
            
            <div class="space-y-4">
                <!-- PayPal Option -->
                <div class="payment-option payment-option-paypal payment-selected">
                    <div class="flex items-center p-4 border-2 border-blue-500 rounded-lg bg-blue-50">
                        <input type="radio" id="paypalRadio" name="payment_method" value="paypal" checked class="h-5 w-5 text-blue-600">
                        <label for="paypalRadio" class="ml-3 flex-1 cursor-pointer">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-blue-600 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.2 18c-.6 0-1.2-.6-1.2-1.2V7.2C6 6.6 6.6 6 7.2 6h9.6c.6 0 1.2.6 1.2 1.2v9.6c0 .6-.6 1.2-1.2 1.2H7.2z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-gray-800">PayPal</h3>
                                    <p class="text-gray-600 text-sm">Pay securely with PayPal</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2 ml-11">
                                You will be redirected to PayPal to complete your payment.
                            </p>
                        </label>
                    </div>
                </div>

                <!-- COD Option -->
                <div class="payment-option payment-option-cod">
                    <div class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500">
                        <input type="radio" id="codRadio" name="payment_method" value="cod" class="h-5 w-5 text-green-600">
                        <label for="codRadio" class="ml-3 flex-1 cursor-pointer">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Cash on Delivery (COD)</h3>
                                    <p class="text-gray-600 text-sm">Pay when you receive your order</p>
                                </div>
                            </div>
                            <div class="text-sm text-gray-600 mt-2 ml-11">
                                <p>✅ No online payment required</p>
                                <p>✅ Pay with cash when your order arrives</p>
                                <p>✅ Additional ₱50 COD fee applies</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="bg-white rounded-lg shadow p-6">
            <button type="button" id="submitButton" class="w-full bg-blue-600 text-white py-4 px-6 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M7.2 18c-.6 0-1.2-.6-1.2-1.2V7.2C6 6.6 6.6 6 7.2 6h9.6c.6 0 1.2.6 1.2 1.2v9.6c0 .6-.6 1.2-1.2 1.2H7.2z"/>
                </svg>
                <span id="buttonText">Pay with PayPal - ₱{{ number_format($total, 2) }}</span>
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('cart.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Cart
                </a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Checkout page loaded');
    
    const paypalRadio = document.getElementById('paypalRadio');
    const codRadio = document.getElementById('codRadio');
    const submitButton = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    const displayTotal = document.getElementById('displayTotal');
    const codTotal = document.getElementById('codTotal');
    const checkoutForm = document.getElementById('checkoutForm');
    
    // Get payment option containers
    const paypalOption = document.querySelector('.payment-option-paypal');
    const codOption = document.querySelector('.payment-option-cod');
    
    // Store route URLs
    const paypalRoute = "{{ route('checkout.pay') }}";
    const codRoute = "{{ route('checkout.cod') }}";
    
    // Get the total from PHP - make sure it's a proper number
    const total = parseFloat('{{ $total }}');
    const codTotalAmount = total + 50;
    
    console.log('Total:', total);
    console.log('COD Total:', codTotalAmount);
    
    // Update UI when payment method changes
    function updatePaymentMethod() {
        if (paypalRadio.checked) {
            // PayPal selected
            buttonText.textContent = 'Pay with PayPal - ₱' + total.toFixed(2);
            submitButton.className = 'w-full bg-blue-600 text-white py-4 px-6 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center';
            displayTotal.style.display = 'inline';
            if (codTotal) codTotal.style.display = 'none';
            checkoutForm.action = paypalRoute;
            
            // Visual selection for PayPal
            if (paypalOption) {
                paypalOption.classList.add('payment-selected');
                const paypalDiv = paypalOption.querySelector('div');
                paypalDiv.className = 'flex items-center p-4 border-2 border-blue-500 rounded-lg bg-blue-50';
                
                // Update PayPal icon color
                const paypalIcon = paypalOption.querySelector('svg');
                if (paypalIcon) {
                    paypalIcon.classList.remove('text-gray-600');
                    paypalIcon.classList.add('text-blue-600');
                }
            }
            
            if (codOption) {
                codOption.classList.remove('payment-selected');
                const codDiv = codOption.querySelector('div');
                codDiv.className = 'flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500';
                
                // Reset COD icon color
                const codIcon = codOption.querySelector('svg');
                if (codIcon) {
                    codIcon.classList.remove('text-green-600');
                    codIcon.classList.add('text-gray-600');
                }
            }
            
        } else if (codRadio.checked) {
            // COD selected
            buttonText.textContent = 'Place COD Order - ₱' + codTotalAmount.toFixed(2);
            submitButton.className = 'w-full bg-green-600 text-white py-4 px-6 rounded-lg text-lg font-semibold hover:bg-green-700 transition duration-200 flex items-center justify-center';
            displayTotal.style.display = 'none';
            if (codTotal) codTotal.style.display = 'inline';
            checkoutForm.action = codRoute;
            
            // Visual selection for COD
            if (codOption) {
                codOption.classList.add('payment-selected');
                const codDiv = codOption.querySelector('div');
                codDiv.className = 'flex items-center p-4 border-2 border-green-500 rounded-lg bg-green-50';
                
                // Update COD icon color
                const codIcon = codOption.querySelector('svg');
                if (codIcon) {
                    codIcon.classList.remove('text-gray-600');
                    codIcon.classList.add('text-green-600');
                }
            }
            
            if (paypalOption) {
                paypalOption.classList.remove('payment-selected');
                const paypalDiv = paypalOption.querySelector('div');
                paypalDiv.className = 'flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500';
                
                // Reset PayPal icon color
                const paypalIcon = paypalOption.querySelector('svg');
                if (paypalIcon) {
                    paypalIcon.classList.remove('text-blue-600');
                    paypalIcon.classList.add('text-gray-600');
                }
            }
        }
        
        console.log('Payment method updated:', paypalRadio.checked ? 'PayPal' : 'COD');
        console.log('Form action set to:', checkoutForm.action);
    }
    
    // Listen for payment method changes
    if (paypalRadio) {
        paypalRadio.addEventListener('change', updatePaymentMethod);
    }
    
    if (codRadio) {
        codRadio.addEventListener('change', updatePaymentMethod);
    }
    
    // Also make the entire payment option clickable
    if (paypalOption) {
        paypalOption.addEventListener('click', function() {
            paypalRadio.checked = true;
            updatePaymentMethod();
        });
    }
    
    if (codOption) {
        codOption.addEventListener('click', function() {
            codRadio.checked = true;
            updatePaymentMethod();
        });
    }
    
    // Handle form submission
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Submit button clicked');
            
            // Basic validation
            const name = document.getElementById('customerName');
            const phone = document.getElementById('customerPhone');
            const address = document.getElementById('customerAddress');
            
            if (!name || !name.value.trim()) {
                alert('Please enter your name.');
                if (name) name.focus();
                return;
            }
            
            if (!phone || !phone.value.trim()) {
                alert('Please enter your phone number.');
                if (phone) phone.focus();
                return;
            }
            
            if (!address || !address.value.trim()) {
                alert('Please enter your delivery address.');
                if (address) address.focus();
                return;
            }
            
            // Validate phone number
            const phoneRegex = /^09[0-9]{9}$/;
            const cleanPhone = phone.value.replace(/\D/g, '');
            if (!phoneRegex.test(cleanPhone)) {
                alert('Please enter a valid Philippine phone number (09XXXXXXXXX).');
                if (phone) phone.focus();
                return;
            }
            
            // Make sure form action is set
            if (!checkoutForm.action) {
                checkoutForm.action = paypalRoute;
            }
            
            console.log('Form validated. Payment method:', paypalRadio.checked ? 'PayPal' : 'COD');
            console.log('Submitting to:', checkoutForm.action);
            
            // Show loading state
            submitButton.disabled = true;
            buttonText.textContent = 'Processing...';
            
            // Submit the form
            setTimeout(function() {
                checkoutForm.submit();
            }, 500);
        });
    }
    
    // Initialize
    updatePaymentMethod();
    console.log('Checkout script initialized');
});
</script>

<style>
.payment-option {
    cursor: pointer;
    transition: all 0.2s ease;
}

.payment-option:hover {
    transform: translateY(-2px);
}

.payment-selected {
    transform: translateY(-2px);
}
</style>

{{-- At the end of your checkout.blade.php --}}
@include('components.footer')

@endsection