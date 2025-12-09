@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="max-w-6xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <!-- PayPal Success Confirmation -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- PayPal Blue Header -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-500 to-blue-600 px-8 py-12 text-center text-white">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-6 shadow-lg">
                <svg class="w-12 h-12 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold mb-3 tracking-tight">Payment Successful!</h1>
            <p class="text-blue-100 text-lg opacity-90">Your PayPal payment has been confirmed</p>
            
            @if(isset($order) && $order)
            <div class="mt-8 inline-block bg-white/25 backdrop-blur-sm rounded-xl px-8 py-4 border border-white/30">
                <p class="text-sm text-blue-50 uppercase tracking-wider font-medium">Order ID</p>
                <p class="text-3xl font-bold tracking-tight">#{{ $order->id }}</p>
                <p class="text-sm text-blue-50 mt-2">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="p-10">
            <!-- Success Message -->
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-100 rounded-full mb-5 shadow-sm">
                    <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                </div>
                <p class="text-2xl font-semibold text-gray-800 mb-3">Thank you for your order!</p>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    We've sent a confirmation email to your registered email address.
                    You will receive your order within 3-5 business days.
                </p>
            </div>

            <!-- Order Summary -->
            <div class="border border-gray-200 rounded-2xl overflow-hidden mb-10 shadow-sm">
                <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white px-8 py-6">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-2xl font-bold">Payment Receipt</h2>
                            <p class="text-gray-300 text-sm mt-1">Order #{{ $order->id }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-300 uppercase tracking-wider">Date</p>
                            <p class="font-medium text-lg">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="p-8">
                    <div class="space-y-6 mb-8">
                        @php
                            $orderItems = \App\Models\OrderItem::where('order_id', $order->id)
                                ->with('product')
                                ->get();
                            
                            $subtotal = $orderItems->sum(function($item) {
                                return $item->price * $item->quantity;
                            });
                            
                            $shippingFee = $order->shipping_fee ?? 0;
                            $calculatedTotal = $subtotal + $shippingFee;
                        @endphp
                        
                        @foreach($orderItems as $item)
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-gray-100 gap-4">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-lg">
                                    {{ $item->product->name ?? 'Product #' . $item->product_id }}
                                </p>
                                @if($item->size || $item->color)
                                <div class="flex flex-wrap gap-3 text-sm text-gray-600 mt-2">
                                    @if($item->size)
                                    <span class="bg-gray-100 px-3 py-1 rounded-full">Size: {{ $item->size }}</span>
                                    @endif
                                    @if($item->color)
                                    <span class="bg-gray-100 px-3 py-1 rounded-full">Color: {{ $item->color }}</span>
                                    @endif
                                </div>
                                @endif
                                <p class="text-sm text-gray-500 mt-2">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-blue-700">₱{{ number_format($item->price, 2) }} each</p>
                                <p class="text-xl font-bold text-blue-800 mt-1">
                                    ₱{{ number_format($item->price * $item->quantity, 2) }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Price Breakdown -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-8 border border-blue-100 shadow-sm">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700 font-medium">Items Subtotal</span>
                                <span class="font-semibold text-gray-900 text-lg">₱{{ number_format($subtotal, 2) }}</span>
                            </div>
                            
                            @if($shippingFee > 0)
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-700 font-medium">Shipping Fee</span>
                                <span class="font-semibold text-gray-900 text-lg">₱{{ number_format($shippingFee, 2) }}</span>
                            </div>
                            @endif
                            
                            <div class="border-t border-blue-200 pt-6 mt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-900">Total Amount Paid</span>
                                    <span class="text-2xl font-bold text-blue-600">₱{{ number_format($calculatedTotal, 2) }}</span>
                                </div>
                            </div>
                            
                            <!-- Payment Note -->
                            <div class="pt-6">
                                <div class="flex items-center gap-4 bg-blue-100 text-blue-800 rounded-xl p-4">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-lg">Payment of ₱{{ number_format($calculatedTotal, 2) }} completed via PayPal</p>
                                        <p class="text-sm text-blue-700 mt-1">Transaction confirmed on {{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                <!-- Shipping Info -->
                <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-bold text-gray-800 text-xl mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Shipping Information
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Recipient Name</p>
                                @php
                                    $customerName = $order->customer_name ?? $order->user->name ?? 'Customer';
                                @endphp
                                <p class="font-semibold text-gray-900 text-lg">{{ $customerName }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Delivery Address</p>
                                @php
                                    $customerAddress = $order->address ?? 'Address not specified';
                                @endphp
                                <p class="font-semibold text-gray-900 text-lg">{{ $customerAddress }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Contact Number</p>
                                @php
                                    $customerPhone = $order->customer_phone ?? $order->phone ?? 'Not provided';
                                @endphp
                                <p class="font-semibold text-gray-900 text-lg">{{ $customerPhone }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white border border-blue-200 rounded-2xl p-8 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <h3 class="font-bold text-gray-800 text-xl mb-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        Payment Information
                    </h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm text-gray-500 mb-2 uppercase tracking-wider">Payment Method</p>
                            <div class="inline-flex items-center bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 px-5 py-2.5 rounded-lg font-semibold text-lg border border-blue-200">
                                <svg class="w-6 h-6 mr-2.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
                                </svg>
                                PayPal
                            </div>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-2 uppercase tracking-wider">Payment Status</p>
                            <span class="inline-flex items-center bg-gradient-to-r from-emerald-100 to-green-100 text-emerald-800 px-5 py-2.5 rounded-lg font-semibold text-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Paid & Confirmed
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-500 mb-2 uppercase tracking-wider">Transaction Date</p>
                            <p class="font-semibold text-gray-900 text-lg">{{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                        
                        <div class="mt-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                            <p class="text-blue-800 font-semibold">
                                💳 <strong>Payment Confirmed:</strong> Your PayPal payment of ₱{{ number_format($calculatedTotal, 2) }} has been processed successfully.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-8 mb-10">
                <h4 class="font-bold text-blue-800 text-xl mb-6 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    What's Next?
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">1</div>
                        <h5 class="font-bold text-gray-800 text-center mb-2">Order Processed</h5>
                        <p class="text-sm text-gray-600 text-center">We're preparing your items for shipment</p>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">2</div>
                        <h5 class="font-bold text-gray-800 text-center mb-2">Shipment Tracking</h5>
                        <p class="text-sm text-gray-600 text-center">We'll email you tracking information</p>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">3</div>
                        <h5 class="font-bold text-gray-800 text-center mb-2">Delivery</h5>
                        <p class="text-sm text-gray-600 text-center">Expect delivery in 3-5 business days</p>
                    </div>
                    
                    <div class="bg-white rounded-xl p-6 border border-blue-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center text-xl font-bold mb-4 mx-auto">4</div>
                        <h5 class="font-bold text-gray-800 text-center mb-2">Enjoy Your Order</h5>
                        <p class="text-sm text-gray-600 text-center">Check items upon delivery</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col lg:flex-row gap-4 justify-center pt-10 border-t border-gray-200">
                <a href="{{ route('home') }}" 
                   class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-10 py-4 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex-1 text-center">
                    Continue Shopping
                </a>
                
                @if(isset($order) && $order)
                <a href="{{ route('orders.show', $order->id) }}" 
                   class="bg-gradient-to-r from-gray-800 to-gray-900 text-white px-10 py-4 rounded-xl hover:from-gray-900 hover:to-black transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex-1 text-center">
                    View Order Details
                </a>
                
                <button onclick="window.print()" 
                        class="bg-white border-2 border-blue-600 text-blue-600 px-10 py-4 rounded-xl hover:bg-blue-50 transition-all duration-300 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex-1 text-center">
                    Print Receipt
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Help Section -->
    <div class="mt-10 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl p-10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-6 shadow-sm">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-900 text-2xl mb-3">Need Help with Your Order?</h3>
            <p class="text-gray-700 text-lg max-w-2xl mx-auto mb-8">Our support team is here to assist you with delivery or payment questions</p>
        </div>
        
        <div class="flex flex-col md:flex-row gap-6 justify-center">
            <a href="mailto:support@shoeshop.com" 
               class="inline-flex items-center justify-center gap-3 bg-white border border-blue-300 text-blue-700 px-8 py-4 rounded-xl hover:bg-blue-50 transition-all duration-300 font-semibold shadow-sm hover:shadow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Email Support
            </a>
            
            <a href="tel:+1234567890" 
               class="inline-flex items-center justify-center gap-3 bg-white border border-blue-300 text-blue-700 px-8 py-4 rounded-xl hover:bg-blue-50 transition-all duration-300 font-semibold shadow-sm hover:shadow">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Call Support: +1 (234) 567-890
            </a>
            
            <a href="https://wa.me/1234567890" 
               class="inline-flex items-center justify-center gap-3 bg-white border border-green-300 text-green-700 px-8 py-4 rounded-xl hover:bg-green-50 transition-all duration-300 font-semibold shadow-sm hover:shadow">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.436 9.884-9.884 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                </svg>
                WhatsApp Support
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    a { text-decoration: none !important; color: black !important; }
    button { display: none !important; }
    .bg-gradient-to-r { background: #2563eb !important; }
    .shadow-lg, .shadow-xl, .shadow-sm { box-shadow: none !important; }
    .transform { transform: none !important; }
    .border { border-color: #d1d5db !important; }
}
</style>
@endsection