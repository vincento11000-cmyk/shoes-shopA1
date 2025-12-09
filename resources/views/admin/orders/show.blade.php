@extends('admin.layout')

@section('title', 'Order #' . $order->id)

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
    <a href="{{ route('admin.orders') }}" class="text-blue-600 hover:text-blue-800">← Back to Orders</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Order Details --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Order Summary --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold mb-2">Order Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Order Date:</span>
                            <span>{{ $order->created_at->format('F d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span>
                                @if($order->payment_method === 'paypal')
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        PayPal
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        {{ ucfirst($order->payment_method) }}
                                    </span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status:</span>
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        @if($order->paypal_order_id)
                        <div class="flex justify-between">
                            <span class="text-gray-600">PayPal ID:</span>
                            <span class="text-sm text-gray-500">{{ $order->paypal_order_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-2">Delivery Information</h3>
                    <div class="space-y-2">
                        <div>
                            <span class="text-gray-600">Customer:</span>
                            <p class="font-semibold">{{ $order->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Phone:</span>
                            <p>{{ $order->phone }}</p>
                        </div>
                        <div>
                            <span class="text-gray-600">Address:</span>
                            <p>{{ $order->address }}</p>
                        </div>
                        @if($order->notes)
                        <div>
                            <span class="text-gray-600">Delivery Notes:</span>
                            <p class="italic">{{ $order->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Order Items</h2>
            
            <div class="space-y-4">
                @foreach ($order->items as $item)
                <div class="flex items-center border-b pb-4 last:border-b-0">
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
                        <p class="text-gray-600">₱{{ number_format($item->price, 2) }} × {{ $item->quantity }}</p>
                        <p class="font-semibold">₱{{ number_format($item->price * $item->quantity, 2) }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="border-t mt-4 pt-4">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total Amount:</span>
                    <span>₱{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Actions --}}
    <div class="space-y-6">
        {{-- Status Update --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Update Status</h2>
            
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select name="order_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Update Status
                </button>
            </form>
        </div>

        {{-- Order Information --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Order Information</h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Order ID:</span>
                    <span class="font-medium">#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer ID:</span>
                    <span>{{ $order->user_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Placed on:</span>
                    <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Last updated:</span>
                    <span>{{ $order->updated_at->format('M d, Y g:i A') }}</span>
                </div>
            </div>
        </div>

        {{-- RECEIPT ACTIONS --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Receipt Actions</h2>
            <div class="flex flex-col gap-3">
                <a href="{{ route('admin.orders.receipt', $order) }}" 
                   target="_blank"
                   class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-receipt"></i> View Receipt
                </a>
                
                <a href="{{ route('admin.orders.receipt', $order) }}?print=true" 
                   target="_blank"
                   class="bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i> Print Receipt
                </a>
            </div>
        </div>
    </div>
</div>
@endsection