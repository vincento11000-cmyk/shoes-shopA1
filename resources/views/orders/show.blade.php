@extends('layouts.app')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
            <p class="text-gray-600 mt-1">Placed on {{ $order->created_at->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('orders') }}" 
           class="mt-4 sm:mt-0 inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Orders
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-r">
            <div class="flex">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div class="text-green-700">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r">
            <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div class="text-red-700">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Payment Status -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Payment</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium capitalize">{{ $order->payment_method }}</span>
                </div>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Order Status</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    {{ $order->order_status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($order->order_status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                       ($order->order_status === 'shipped' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')) }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium">{{ $order->created_at->format('F d, Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer & Delivery Info -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Delivery Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Customer Name</p>
                    <p class="font-medium">{{ $order->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone Number</p>
                    <p class="font-medium">{{ $order->phone }}</p>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Delivery Address</p>
                    <p class="font-medium">{{ $order->address }}</p>
                </div>
                @if($order->notes)
                <div>
                    <p class="text-sm text-gray-500">Delivery Notes</p>
                    <p class="font-medium italic">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-8">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
        </div>
        
        <div class="divide-y">
            @foreach ($order->items as $item)
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . ($item->variant->variant_image ?: $item->product->main_image)) }}" 
                         alt="{{ $item->product->name }}"
                         class="w-20 h-20 object-cover rounded-lg">
                </div>
                <div class="ml-6 flex-1">
                    <h4 class="font-medium text-gray-900">{{ $item->product->name }}</h4>
                    <div class="flex items-center mt-2 text-sm text-gray-500">
                        <span class="mr-4">Size: {{ $item->variant->size }}</span>
                        <span>Color: {{ ucfirst($item->variant->color) }}</span>
                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-gray-600">Quantity: {{ $item->quantity }}</span>
                        <div class="text-right">
                            <p class="text-gray-500">₱{{ number_format($item->price, 2) }} each</p>
                            <p class="font-semibold text-gray-900">
                                ₱{{ number_format($item->price * $item->quantity, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Order Total -->
        <div class="px-6 py-6 bg-gray-50 border-t">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-900">Total Amount</span>
                <span class="text-2xl font-bold text-gray-900">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Cancel Order Button -->
    @if(in_array($order->order_status, ['pending', 'processing']))
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" 
              onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
            @csrf
            @method('POST')
            <div class="text-center">
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel Order
                </button>
                <p class="text-sm text-gray-500 mt-3">
                    You can only cancel orders that are still pending or processing.
                </p>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection