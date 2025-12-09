@extends('admin.layout')

@section('title', 'Orders Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Orders Management</h1>
        <p class="text-gray-600">{{ $orders->total() }} orders found</p>
    </div>

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.orders') }}" class="mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" 
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by order ID, customer name, or phone..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" 
                        onchange="this.form.submit()"
                        class="w-full md:w-auto px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Clear Filters Button -->
            @if(request('search') || request('status'))
            <div>
                <a href="{{ route('admin.orders') }}" 
                   class="px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 inline-block">
                    Clear Filters
                </a>
            </div>
            @endif
        </div>
    </form>

    @if($orders->isEmpty())
    <!-- Empty State -->
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Orders Found</h3>
        <p class="text-gray-500 mb-4">
            @if(request('search') || request('status'))
                No orders match your search criteria.
            @else
                There are no orders in the system.
            @endif
        </p>
        @if(request('search') || request('status'))
        <a href="{{ route('admin.orders') }}" 
           class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Clear Filters
        </a>
        @endif
    </div>
    @else
    <!-- Desktop Table -->
    <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $order->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->phone }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $order->order_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->order_status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            ₱{{ number_format($order->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="lg:hidden space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-lg shadow p-4">
            <!-- Card Header -->
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="flex items-center mb-1">
                        <span class="font-bold text-gray-900 mr-2">#{{ $order->id }}</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                            {{ $order->order_status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($order->order_status === 'processing' ? 'bg-blue-100 text-blue-800' : 
                               ($order->order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $order->created_at->format('M d, Y') }}
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-gray-900">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-4">
                <div class="text-sm font-medium text-gray-900">{{ $order->name }}</div>
                <div class="text-sm text-gray-500">{{ $order->phone }}</div>
            </div>

            <!-- Actions -->
            <div class="pt-3 border-t">
                <a href="{{ route('admin.orders.show', $order->id) }}" 
                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                    View Details
                </a>
            </div>
        </div>
        @endforeach

        <!-- Mobile Pagination -->
        @if($orders->hasPages())
        <div class="bg-white rounded-lg shadow p-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection