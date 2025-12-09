@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-3xl font-bold mb-6">My Orders</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @forelse ($orders as $order)
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="p-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Order #{{ $order->id }}</h3>
                        <span class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Payment Status</p>
                            <p class="font-semibold capitalize {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $order->payment_status }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Order Status</p>
                            <p class="font-semibold capitalize {{ $order->order_status === 'completed' ? 'text-green-600' : 'text-blue-600' }}">
                                {{ $order->order_status }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Amount</p>
                            <p class="font-semibold">₱{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>

                    <p class="text-sm text-gray-600">
                        {{ $order->items_count }} item(s) • {{ $order->payment_method === 'paypal' ? 'Paid with PayPal' : 'Cash on Delivery' }}
                    </p>
                </div>

                <div class="ml-6">
                    <a href="{{ route('orders.show', $order->id) }}" 
                       class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-lg shadow-md p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Orders Yet</h3>
        <p class="text-gray-500 mb-4">You haven't placed any orders yet.</p>
        <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
            Start Shopping
        </a>
    </div>
    @endforelse
</div>
@endsection