@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Shopping Cart</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($items->count() == 0)
        <div class="bg-white p-8 rounded-lg shadow text-center">
            <p class="text-gray-600 text-lg mb-4">Your cart is empty</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
                Continue Shopping
            </a>
        </div>
    @else
        <!-- Mobile View -->
        <div class="md:hidden">
            @foreach($items as $item)
            <div class="bg-white rounded-lg shadow mb-4 p-4">
                <div class="flex items-start space-x-4">
                    <img src="{{ asset('storage/' . ($item->variant->variant_image ?: $item->product->main_image)) }}" 
                         class="h-20 w-20 object-cover rounded flex-shrink-0">
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-lg">{{ $item->product->name }}</p>
                                <p class="text-gray-600 text-sm">{{ $item->product->category->name }}</p>
                            </div>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800" 
                                        onclick="return confirm('Remove this item from cart?')">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        
                        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500">Size:</span>
                                <span class="font-medium ml-2">{{ $item->variant->size }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Color:</span>
                                <span class="font-medium ml-2">{{ ucfirst($item->variant->color) }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-3 flex items-center justify-between">
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <span class="text-gray-500">Qty:</span>
                                <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                       min="1" max="{{ $item->variant->stock }}"
                                       class="w-16 p-1 border rounded text-center">
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </form>
                            <div class="text-right">
                                <div class="text-gray-500 text-sm">Price</div>
                                <div class="font-semibold">₱{{ number_format($item->product->base_price * $item->quantity, 2) }}</div>
                                <div class="text-sm text-gray-600">₱{{ number_format($item->product->base_price, 2) }} each</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Desktop View -->
        <div class="hidden md:block bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($items as $item)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ asset('storage/' . ($item->variant->variant_image ?: $item->product->main_image)) }}" 
                                         class="h-16 w-16 object-cover rounded">
                                    <div class="ml-4">
                                        <p class="font-semibold">{{ $item->product->name }}</p>
                                        <p class="text-gray-600 text-sm">{{ $item->product->category->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">{{ $item->variant->size }}</td>
                            <td class="px-6 py-4">{{ ucfirst($item->variant->color) }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" 
                                           min="1" max="{{ $item->variant->stock }}"
                                           class="w-20 p-2 border rounded">
                                    <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">Update</button>
                                </form>
                            </td>
                            <td class="px-6 py-4">₱{{ number_format($item->product->base_price, 2) }}</td>
                            <td class="px-6 py-4 font-semibold">₱{{ number_format($item->product->base_price * $item->quantity, 2) }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800" 
                                            onclick="return confirm('Remove this item from cart?')">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="mt-6 bg-white rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="text-2xl font-bold">
                        Total: ₱{{ number_format($total, 2) }}
                    </div>
                    <p class="text-gray-600 text-sm mt-1">{{ $items->count() }} {{ Str::plural('item', $items->count()) }} in cart</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                    <a href="{{ route('products.index') }}" 
                       class="text-center border border-blue-600 text-blue-600 px-6 py-3 rounded hover:bg-blue-50 transition">
                        Continue Shopping
                    </a>
                    <a href="{{ route('checkout.index') }}" 
                       class="text-center bg-green-600 text-white px-6 py-3 rounded hover:bg-green-700 transition">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@include('components.footer')
@endsection