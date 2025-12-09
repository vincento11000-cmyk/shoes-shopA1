@extends('admin.layout')

@section('title', 'Shoe Variants Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header with Stats -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Shoe Variants</h1>
            <p class="text-gray-600 mt-2">Manage product variations, sizes, colors, and inventory</p>
        </div>
        
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <div class="bg-blue-50 px-4 py-2 rounded-lg">
                <p class="text-sm text-gray-600">Total Variants</p>
                <p class="text-2xl font-bold text-blue-700">{{ $variants->count() }}</p>
            </div>
            
            <a href="{{ route('admin.variants.create') }}" 
               class="flex items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Variant
            </a>
        </div>
    </div>

    <!-- Variants Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Product & Details
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Attributes
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Stock Status
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Preview
                        </th>
                        <th class="py-4 px-6 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($variants as $variant)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <!-- Product Details -->
                        <td class="py-4 px-6">
                            <div class="flex items-start">
                                @if ($variant->variant_image)
                                <img src="{{ asset('storage/'.$variant->variant_image) }}" 
                                     alt="{{ $variant->product->name }}"
                                     class="w-16 h-16 rounded-lg object-cover mr-4">
                                @else
                                <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center mr-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $variant->product->name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">SKU: {{ $variant->sku ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">ID: #{{ $variant->id }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Attributes -->
                        <td class="py-4 px-6">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        Size: {{ $variant->size }}
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                        </svg>
                                        Color: {{ $variant->color }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    Price: <span class="font-semibold">₱{{ number_format($variant->price ?? $variant->product->base_price, 2) }}</span>
                                </p>
                            </div>
                        </td>

                        <!-- Stock Status -->
                        <td class="py-4 px-6">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    @if($variant->stock > 20)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        In Stock
                                    </span>
                                    @elseif($variant->stock > 0)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Low Stock
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        Out of Stock
                                    </span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600">Available: <span class="font-bold text-gray-900">{{ $variant->stock }}</span> units</p>
                                    @if($variant->stock <= 10 && $variant->stock > 0)
                                    <div class="mt-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ ($variant->stock / 10) * 100 }}%"></div>
                                        </div>
                                        <p class="text-xs text-yellow-600 mt-1">⚠️ Restock needed soon</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Preview Image -->
                        <td class="py-4 px-6">
                            <div class="relative group">
                                @if ($variant->variant_image)
                                <img src="{{ asset('storage/'.$variant->variant_image) }}" 
                                     alt="Variant Preview"
                                     class="w-20 h-20 rounded-lg object-cover cursor-pointer border border-gray-300">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition duration-200 rounded-lg"></div>
                                @else
                                <div class="w-20 h-20 rounded-lg bg-gray-100 flex flex-col items-center justify-center border border-gray-300">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs text-gray-500 mt-1">No image</span>
                                </div>
                                @endif
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.variants.edit', $variant->id) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-sm font-medium transition duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                
                                <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this variant?');"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-sm font-medium transition duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center">
                            <div class="text-gray-500">
                                <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-700">No variants found</h3>
                                <p class="mt-1 text-gray-500">Get started by creating your first variant.</p>
                                <a href="{{ route('admin.variants.create') }}" 
                                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                                    Add New Variant
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination - Fixed to work with both Collection and Paginator -->
        @if(method_exists($variants, 'hasPages') && $variants->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $variants->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Stats - Fixed for Collection -->
    @php
        // Calculate stats safely for both Collection and Paginator
        $inStockCount = $variants->where('stock', '>', 0)->count();
        $lowStockCount = $variants->where('stock', '>=', 1)->where('stock', '<=', 10)->count();
        $outOfStockCount = $variants->where('stock', 0)->count();
    @endphp
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">In Stock Variants</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $inStockCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Low Stock Variants</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $lowStockCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $outOfStockCount }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Confirmation for delete actions
document.addEventListener('DOMContentLoaded', function() {
    // Add confirmation to all delete forms
    const deleteForms = document.querySelectorAll('form[action*="destroy"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this variant? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
});
</script>
@endpush

@endsection