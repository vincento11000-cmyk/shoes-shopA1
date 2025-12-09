@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50 px-4 py-8">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">ApexSole</h1>
        <p class="text-gray-600">Find your perfect pair from our collection</p>
    </div>

    <!-- Search Bar with Filter Toggle -->
    <div class="max-w-7xl mx-auto mb-8">
        <div class="relative">
            <!-- Search Bar -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2">
                <div class="flex items-center">
                    <div class="flex-1 flex items-center">
                        <svg class="ml-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <form method="GET" id="searchForm" class="w-full">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search for shoes..." 
                                   class="w-full px-4 py-3 text-gray-900 placeholder-gray-500 focus:outline-none"
                                   autocomplete="off"
                                   onkeypress="if(event.key === 'Enter') document.getElementById('searchForm').submit();">
                        </form>
                    </div>
                    
                    <!-- Filter Toggle Button -->
                    <button type="button" id="filterToggle" 
                            class="flex items-center gap-2 px-4 py-3 text-gray-600 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span class="hidden md:inline">Filters</span>
                        @if(request()->anyFilled(['category', 'min_price', 'max_price', 'size']))
                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs bg-blue-600 text-white rounded-full">
                            {{ collect(request()->only(['category', 'min_price', 'max_price', 'size']))->filter()->count() }}
                        </span>
                        @endif
                    </button>
                    
                    <!-- Search Button -->
                    <button type="button" onclick="document.getElementById('searchForm').submit()"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Search
                    </button>
                </div>
            </div>

            <!-- Filter Panel (Hidden by default) -->
            <div id="filterPanel" class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-200 p-6 z-50">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Filter Products</h3>
                    <button type="button" id="closeFilters" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form method="GET" id="filterForm" class="space-y-6">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    
                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Category</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <button type="button" 
                                    onclick="toggleCategory('all')" 
                                    class="p-3 text-sm rounded-lg border transition-all duration-200 {{ !request('category') ? 'bg-blue-600 text-white border-blue-600 shadow-sm hover:bg-blue-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                                All Categories
                            </button>
                            @foreach($categories as $cat)
                            <button type="button" 
                                    onclick="toggleCategory('{{ $cat->id }}')" 
                                    class="p-3 text-sm rounded-lg border transition-all duration-200 category-button {{ request('category') == $cat->id ? 'bg-blue-600 text-white border-blue-600 shadow-sm hover:bg-blue-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}"
                                    data-category-id="{{ $cat->id }}">
                                {{ $cat->name }}
                            </button>
                            @endforeach
                            <input type="hidden" id="categoryInput" name="category" value="{{ request('category') }}">
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Price Range</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <input type="number" name="min_price" value="{{ request('min_price') }}" 
                                       placeholder="Minimum price" 
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       id="minPriceInput">
                            </div>
                            <div>
                                <input type="number" name="max_price" value="{{ request('max_price') }}" 
                                       placeholder="Maximum price" 
                                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       id="maxPriceInput">
                            </div>
                        </div>
                    </div>

                    <!-- Size -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Size</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" 
                                    onclick="toggleSize('all')" 
                                    class="px-4 py-2 text-sm rounded-lg border transition-all duration-200 {{ !request('size') ? 'bg-blue-600 text-white border-blue-600 shadow-sm hover:bg-blue-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}">
                                All Sizes
                            </button>
                            @foreach($availableSizes as $size)
                            <button type="button" 
                                    onclick="toggleSize('{{ $size }}')" 
                                    class="px-4 py-2 text-sm rounded-lg border transition-all duration-200 size-button {{ request('size') == $size ? 'bg-blue-600 text-white border-blue-600 shadow-sm hover:bg-blue-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100 hover:border-gray-300' }}"
                                    data-size="{{ $size }}">
                                {{ $size }}
                            </button>
                            @endforeach
                            <input type="hidden" id="sizeInput" name="size" value="{{ request('size') }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-6 border-t border-gray-100">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Apply Filters
                        </button>
                        <button type="button" 
                                onclick="clearAllFilters()" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            Clear All
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    @if(request()->anyFilled(['search', 'category', 'min_price', 'max_price', 'size']))
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex flex-wrap gap-2 items-center">
            @if(request('search'))
                <span class="inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                    Search: "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 hover:text-blue-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request('category'))
                @php $selectedCat = $categories->firstWhere('id', request('category')); @endphp
                @if($selectedCat)
                <span class="inline-flex items-center bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    {{ $selectedCat->name }}
                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="ml-2 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
                @endif
            @endif
            
            @if(request('min_price') || request('max_price'))
                <span class="inline-flex items-center bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                    Price: 
                    @if(request('min_price'))₱{{ number_format(request('min_price'), 0) }}@endif
                    @if(request('min_price') && request('max_price')) - @endif
                    @if(request('max_price'))₱{{ number_format(request('max_price'), 0) }}@endif
                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="ml-2 hover:text-purple-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request('size'))
                <span class="inline-flex items-center bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    Size {{ request('size') }}
                    <a href="{{ request()->fullUrlWithQuery(['size' => null]) }}" class="ml-2 hover:text-orange-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </a>
                </span>
            @endif
            
            @if(request()->anyFilled(['category', 'min_price', 'max_price', 'size']))
            <a href="{{ route('products.index') }}?search={{ request('search') }}" 
               class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                Clear all filters
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Results Count -->
    <div class="max-w-7xl mx-auto mb-6">
        <p class="text-gray-600">
            @if($products->count() > 0)
                Showing <span class="font-semibold">{{ $products->count() }}</span> 
                @if($products->total() != $products->count())
                    of <span class="font-semibold">{{ $products->total() }}</span>
                @endif
                products
            @endif
        </p>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
    <div class="max-w-7xl mx-auto">
        <!-- Products -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($products as $shoe)
            <div class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden border border-gray-200">
                <a href="{{ route('products.show', $shoe->id) }}" class="block">
                    <!-- Image -->
                    <div class="relative overflow-hidden bg-gray-100">
                        <div class="aspect-square">
                            <img src="{{ asset('storage/'.$shoe->main_image) }}" 
                                 alt="{{ $shoe->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        
                        @if($shoe->category)
                        <span class="absolute top-3 left-3 bg-white/90 text-gray-800 text-xs font-medium px-3 py-1 rounded-full">
                            {{ $shoe->category->name }}
                        </span>
                        @endif
                    </div>
                    
                    <!-- Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 line-clamp-1">{{ $shoe->name }}</h3>
                        
                        <div class="mt-2">
                            <span class="text-xl font-bold text-gray-900">₱{{ number_format($shoe->base_price, 0) }}</span>
                        </div>
                        
                        <!-- Sizes -->
                        @if($shoe->variants->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Sizes:</span>
                                <div class="flex gap-1">
                                    @foreach($shoe->variants->take(3) as $variant)
                                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $variant->size }}
                                        </span>
                                    @endforeach
                                    @if($shoe->variants->count() > 3)
                                        <span class="text-xs text-gray-500 px-1">+{{ $shoe->variants->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>

        <!-- Simple Pagination -->
        @if($products->hasPages())
        <div class="mt-12 flex justify-center">
            <div class="flex items-center gap-2">
                @if(!$products->onFirstPage())
                    <a href="{{ $products->previousPageUrl() }}" 
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-gray-700">
                        Previous
                    </a>
                @endif
                
                <span class="px-4 py-2 text-gray-700">
                    Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                </span>
                
                @if($products->hasMorePages())
                    <a href="{{ $products->nextPageUrl() }}" 
                       class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-gray-700">
                        Next
                    </a>
                @endif
            </div>
        </div>
        @endif

    </div>

    @else
    <!-- Empty State -->
    <div class="max-w-7xl mx-auto">
        <div class="text-center py-16">
            <div class="w-20 h-20 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1m4 0h-4"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-600 mb-6">
                @if(request('search'))
                    No results for "{{ request('search') }}"
                @else
                    Try adjusting your search or filters
                @endif
            </p>
            <a href="{{ route('products.index') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                View All Products
            </a>
        </div>
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterToggle = document.getElementById('filterToggle');
    const filterPanel = document.getElementById('filterPanel');
    const closeFilters = document.getElementById('closeFilters');
    
    // Toggle filter panel
    if (filterToggle) {
        filterToggle.addEventListener('click', function(e) {
            e.preventDefault();
            filterPanel.classList.toggle('hidden');
        });
    }
    
    // Close filter panel
    if (closeFilters) {
        closeFilters.addEventListener('click', function() {
            filterPanel.classList.add('hidden');
        });
    }
    
    // Close panel when clicking outside
    document.addEventListener('click', function(e) {
        if (filterPanel && !filterPanel.classList.contains('hidden')) {
            if (!filterPanel.contains(e.target) && !filterToggle.contains(e.target)) {
                filterPanel.classList.add('hidden');
            }
        }
    });
    
    // Initialize button states from URL parameters
    initializeButtonStates();
});

function initializeButtonStates() {
    // Get current values from URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const currentCategory = urlParams.get('category');
    const currentSize = urlParams.get('size');
    
    // Update category buttons
    const categoryButtons = document.querySelectorAll('.category-button');
    categoryButtons.forEach(btn => {
        const categoryId = btn.getAttribute('data-category-id');
        if (categoryId === currentCategory) {
            btn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    });
    
    // Update "All Categories" button
    const allCategoryBtn = document.querySelector('button[onclick*="toggleCategory(\'all\')"]');
    if (!currentCategory && allCategoryBtn) {
        allCategoryBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
        allCategoryBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
    }
    
    // Update size buttons
    const sizeButtons = document.querySelectorAll('.size-button');
    sizeButtons.forEach(btn => {
        const size = btn.getAttribute('data-size');
        if (size === currentSize) {
            btn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    });
    
    // Update "All Sizes" button
    const allSizeBtn = document.querySelector('button[onclick*="toggleSize(\'all\')"]');
    if (!currentSize && allSizeBtn) {
        allSizeBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
        allSizeBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
    }
}

function toggleCategory(categoryId) {
    const categoryInput = document.getElementById('categoryInput');
    
    // Reset all category buttons to default style
    const categoryButtons = document.querySelectorAll('.category-button');
    const allCategoryBtn = document.querySelector('button[onclick*="toggleCategory(\'all\')"]');
    
    // Reset all buttons
    if (allCategoryBtn) {
        allCategoryBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        allCategoryBtn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    }
    
    categoryButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    });
    
    if (categoryId === 'all') {
        categoryInput.value = '';
        // Highlight "All Categories" button
        if (allCategoryBtn) {
            allCategoryBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            allCategoryBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    } else {
        categoryInput.value = categoryId;
        // Highlight selected category button
        const selectedBtn = document.querySelector('.category-button[data-category-id="' + categoryId + '"]');
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            selectedBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    }
}

function toggleSize(size) {
    const sizeInput = document.getElementById('sizeInput');
    
    // Reset all size buttons to default style
    const sizeButtons = document.querySelectorAll('.size-button');
    const allSizeBtn = document.querySelector('button[onclick*="toggleSize(\'all\')"]');
    
    // Reset all buttons
    if (allSizeBtn) {
        allSizeBtn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        allSizeBtn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    }
    
    sizeButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    });
    
    if (size === 'all') {
        sizeInput.value = '';
        // Highlight "All Sizes" button
        if (allSizeBtn) {
            allSizeBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            allSizeBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    } else {
        sizeInput.value = size;
        // Highlight selected size button
        const selectedBtn = document.querySelector('.size-button[data-size="' + size + '"]');
        if (selectedBtn) {
            selectedBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
            selectedBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        }
    }
}

function clearAllFilters() {
    // Reset all filter inputs
    document.getElementById('categoryInput').value = '';
    document.getElementById('sizeInput').value = '';
    document.getElementById('minPriceInput').value = '';
    document.getElementById('maxPriceInput').value = '';
    
    // Reset button styles
    const categoryButtons = document.querySelectorAll('.category-button');
    const allCategoryBtn = document.querySelector('button[onclick*="toggleCategory(\'all\')"]');
    
    categoryButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    });
    
    const sizeButtons = document.querySelectorAll('.size-button');
    const allSizeBtn = document.querySelector('button[onclick*="toggleSize(\'all\')"]');
    
    sizeButtons.forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
        btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
    });
    
    // Highlight "All" buttons
    if (allCategoryBtn) {
        allCategoryBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
        allCategoryBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
    }
    
    if (allSizeBtn) {
        allSizeBtn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200', 'hover:bg-gray-100', 'hover:border-gray-300');
        allSizeBtn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'shadow-sm', 'hover:bg-blue-700');
    }
    
    // Submit the form to apply changes
    document.getElementById('filterForm').submit();
}
</script>

{{-- At the end of your checkout.blade.php --}}
@include('components.footer')


@endsection