@extends('admin.layout')

@section('content')

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Dashboard</h1>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $orders }}</p>
                </div>
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>

        <!-- Customers -->
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Customers</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $customers }}</p>
                </div>
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0A5.981 5.981 0 0018 9a5.981 5.981 0 00-2.672-5.013M15 6a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-800">₱{{ number_format($sales, 2) }}</p>
                </div>
                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-white rounded-lg shadow p-5 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Low Stock</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $low_stock }}</p>
                </div>
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.346 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-5 border-b flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Recent Orders</h2>
            <a href="{{ route('admin.orders') }}" class="text-sm text-blue-600 hover:text-blue-900 font-medium">
                View all →
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        // Get recent orders - you'll need to pass this from controller
                        $recentOrders = \App\Models\Order::latest()->take(5)->get();
                    @endphp
                    
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $order->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->order_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->order_status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">₱{{ number_format($order->total_amount, 2) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="bg-blue-50 rounded-lg p-4">
            <h3 class="font-medium text-gray-700 mb-1">Avg. Order Value</h3>
            <p class="text-xl font-bold text-gray-900">₱{{ number_format($sales / max($orders, 1), 2) }}</p>
        </div>
        <div class="bg-green-50 rounded-lg p-4">
            <h3 class="font-medium text-gray-700 mb-1">Paid Orders</h3>
            <p class="text-xl font-bold text-gray-900">
                {{ \App\Models\Order::where('payment_status', 'paid')->count() }}
            </p>
        </div>
        <div class="bg-yellow-50 rounded-lg p-4">
            <h3 class="font-medium text-gray-700 mb-1">Pending Orders</h3>
            <p class="text-xl font-bold text-gray-900">
                {{ \App\Models\Order::where('order_status', 'pending')->count() }}
            </p>
        </div>
    </div>
</div>

@endsection