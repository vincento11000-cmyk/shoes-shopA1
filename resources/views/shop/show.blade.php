@extends('layouts.app')

@section('content')

@php
    // Initialize relatedProducts if not set
    $relatedProducts = $relatedProducts ?? collect();
    
    // Color mapping function
    function getColorHex($colorName) {
        $colors = [
            'black' => '#000000',
            'white' => '#ffffff',
            'red' => '#ef4444',
            'blue' => '#3b82f6',
            'green' => '#10b981',
            'yellow' => '#fbbf24',
            'purple' => '#8b5cf6',
            'pink' => '#ec4899',
            'brown' => '#92400e',
            'gray' => '#6b7280',
            'grey' => '#6b7280',
            'orange' => '#f97316',
            'cyan' => '#06b6d4',
            'indigo' => '#6366f1',
        ];
        
        return $colors[strtolower($colorName)] ?? '#cccccc';
    }
    
    // Pre-process colors with hex values
    $processedColors = [];
    foreach ($colors as $color) {
        $hexColor = getColorHex($color['color']);
        $processedColors[] = [
            'color' => $color['color'],
            'image' => $color['image'] ?? null,
            'hex' => $hexColor
        ];
    }
@endphp

<div class="container mx-auto px-4 py-6">
    <!-- Product Grid -->
    <div class="flex flex-col lg:grid lg:grid-cols-2 lg:gap-8">
        {{-- LEFT IMAGE --}}
        <div class="lg:sticky lg:top-24 lg:self-start mb-8 lg:mb-0">
            <!-- Main Image Container -->
            <div class="relative bg-gray-100 rounded-xl shadow-sm overflow-hidden">
                <img id="shoeImage"
                     src="{{ asset('storage/'.$product->main_image) }}" 
                     class="w-full h-auto max-h-[500px] object-contain rounded-xl">
                
                <!-- Stock Badge -->
                <div class="absolute top-4 left-4">
                    <span class="px-3 py-1 bg-blue-600 text-white text-sm font-semibold rounded-full">
                        In Stock
                    </span>
                </div>
            </div>
            
            <!-- Thumbnail Images -->
            @if($product->variants->whereNotNull('variant_image')->count() > 0)
            <div class="mt-4">
                <h3 class="font-semibold text-gray-700 mb-3 text-lg">Colors</h3>
                <div class="flex flex-wrap gap-3">
                    @foreach($processedColors as $color)
                        @php
                            // Create a style string with the hex color
                            $dotStyle = "background-color: " . $color['hex'] . ";";
                        @endphp
                        <button type="button" 
                                class="color-thumbnail p-2 border-2 border-transparent rounded-xl hover:border-blue-500 transition active:scale-95"
                                data-color="{{ $color['color'] }}"
                                data-image="{{ $color['image'] ? asset('storage/'.$color['image']) : asset('storage/'.$product->main_image) }}">
                            <div class="relative">
                                <img src="{{ $color['image'] ? asset('storage/'.$color['image']) : asset('storage/'.$product->main_image) }}" 
                                     class="w-14 h-14 md:w-16 md:h-16 object-cover rounded-lg" 
                                     alt="{{ ucfirst($color['color']) }}">
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white"
                                     style="<?php echo $dotStyle; ?>"></div>
                            </div>
                            <p class="text-xs text-center mt-2 font-medium">{{ ucfirst($color['color']) }}</p>
                        </button>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- RIGHT DETAILS --}}
        <div class="lg:pl-4">
            <!-- Breadcrumb Navigation (Mobile Hidden) -->
            <nav class="hidden md:block mb-4 text-sm">
                <ol class="flex flex-wrap items-center gap-2">
                    <li><a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800">Products</a></li>
                    <li class="text-gray-500">/</li>
                    <li><a href="#" class="text-blue-600 hover:text-blue-800">{{ $product->category->name }}</a></li>
                    <li class="text-gray-500">/</li>
                    <li class="text-gray-700 font-medium">{{ $product->name }}</li>
                </ol>
            </nav>

            <!-- Product Title & Price -->
            <div class="mb-6">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-2xl md:text-3xl text-blue-600 font-bold">
                            ₱{{ number_format($product->base_price, 2) }}
                        </p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                                {{ $product->category->name }}
                            </span>
                            <span id="stockBadge" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                                @if($sizes->count() > 0)
                                    {{ $sizes->first()['stock'] }} in stock
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <!-- Share Button (Mobile Only) -->
                    <button class="mt-4 sm:mt-0 flex items-center gap-2 text-gray-600 hover:text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"></path>
                        </svg>
                        <span class="text-sm">Share</span>
                    </button>
                </div>
            </div>

            {{-- PRODUCT DESCRIPTION (Mobile Collapsible) --}}
            @if($product->description)
            <div class="mb-6">
                <details class="group">
                    <summary class="flex items-center justify-between p-4 bg-gray-50 rounded-lg cursor-pointer">
                        <h3 class="font-semibold text-lg text-gray-800">Product Description</h3>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </summary>
                    <div class="p-4 bg-gray-50 rounded-b-lg mt-1">
                        <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                    </div>
                </details>
            </div>
            @endif

            {{-- ADD TO CART FORM --}}
            <form action="{{ route('cart.add') }}" method="POST" class="bg-white rounded-xl shadow-sm border p-4 md:p-6 mb-8">
                @csrf

                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="variantIdField">

                <div class="space-y-6">
                    {{-- COLOR SELECTOR --}}
                    <div>
                        <label class="block text-base md:text-lg font-semibold text-gray-800 mb-3">Select Color</label>
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                            @foreach($processedColors as $color)
                                @php
                                    // Create a style string with the hex color
                                    $bgStyle = "background-color: " . $color['hex'] . ";";
                                @endphp
                                <button type="button" 
                                        class="color-option flex flex-col items-center p-3 border-2 rounded-xl transition-all active:scale-95"
                                        data-color="{{ $color['color'] }}"
                                        data-image="{{ $color['image'] ? asset('storage/'.$color['image']) : asset('storage/'.$product->main_image) }}">
                                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-full mb-2 overflow-hidden border border-gray-200" 
                                         style="<?php echo $bgStyle; ?>">
                                        @if($color['image'])
                                        <img src="{{ asset('storage/'.$color['image']) }}" 
                                             class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <span class="text-xs md:text-sm font-medium text-gray-700">{{ ucfirst($color['color']) }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- SIZE SELECTOR --}}
                    <div>
                        <label class="block text-base md:text-lg font-semibold text-gray-800 mb-3">Select Size</label>
                        <div class="grid grid-cols-5 sm:grid-cols-6 md:grid-cols-8 gap-2">
                            @foreach($sizes as $size)
                                <button type="button" 
                                        class="size-option py-3 md:py-4 px-2 border rounded-lg text-center text-sm md:text-base transition-all active:scale-95"
                                        data-size="{{ $size['size'] }}"
                                        data-stock="{{ $size['stock'] }}">
                                    {{ $size['size'] }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- STOCK & QUANTITY --}}
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Stock Info Card -->
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 mb-1">Available Stock</p>
                                    <p id="stockInfo" class="text-2xl md:text-3xl font-bold text-gray-900">
                                        @if($sizes->count() > 0)
                                            {{ $sizes->first()['stock'] }}
                                        @else
                                            0
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600 mb-1">Your Selection</p>
                                    <p id="selectedOptions" class="font-semibold text-gray-900">
                                        @if($processedColors && $sizes->count() > 0)
                                            {{ ucfirst($processedColors[0]['color']) }} • {{ $sizes->first()['size'] }}
                                        @else
                                            Select options
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div>
                            <label class="block text-base md:text-lg font-semibold text-gray-800 mb-3">Quantity</label>
                            <div class="flex items-center max-w-xs">
                                <button type="button" 
                                        id="decreaseQty" 
                                        class="px-5 py-3 bg-gray-100 rounded-l-lg active:bg-gray-200 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" 
                                       name="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="1" 
                                       id="quantityInput"
                                       class="flex-1 p-3 border-y text-center text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" 
                                        id="increaseQty" 
                                        class="px-5 py-3 bg-gray-100 rounded-r-lg active:bg-gray-200 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Tap +/- or enter quantity</p>
                        </div>
                    </div>

                    {{-- ACTION BUTTONS --}}
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t">
                        <button type="submit" 
                                id="addToCartBtn"
                                class="flex-1 bg-blue-600 text-white px-6 py-4 rounded-xl text-lg font-semibold active:bg-blue-700 transition active:scale-[0.98] shadow-lg">
                            <div class="flex items-center justify-center gap-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Add to Cart</span>
                            </div>
                        </button>
                        
                        <!-- Wishlist Button -->
                        <button type="button" 
                                class="px-6 py-4 border-2 border-gray-300 text-gray-700 rounded-xl font-semibold active:bg-gray-50 transition active:scale-[0.98]">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="hidden sm:inline">Wishlist</span>
                            </div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- PRODUCT DETAILS SECTION --}}
    <div class="mt-8 bg-white rounded-xl shadow-sm p-5 md:p-6">
        <h2 class="text-xl md:text-2xl font-bold mb-5 pb-3 border-b">Product Details</h2>
        
        <div class="flex flex-col md:grid md:grid-cols-2 md:gap-8">
            <div class="mb-6 md:mb-0">
                <h3 class="font-semibold text-lg md:text-xl mb-3 md:mb-4 text-gray-800">Product Information</h3>
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row">
                        <span class="font-medium text-gray-700 w-full sm:w-40 mb-1 sm:mb-0">Category:</span>
                        <span class="text-gray-900">{{ $product->category->name }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row">
                        <span class="font-medium text-gray-700 w-full sm:w-40 mb-1 sm:mb-0">Available Colors:</span>
                        <span class="text-gray-900">
                            @php
                                $colorNames = [];
                                foreach($processedColors as $color) {
                                    $colorNames[] = ucfirst($color['color']);
                                }
                                echo implode(', ', $colorNames);
                            @endphp
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row">
                        <span class="font-medium text-gray-700 w-full sm:w-40 mb-1 sm:mb-0">Available Sizes:</span>
                        <span class="text-gray-900">
                            @php
                                $sizeNames = [];
                                foreach($sizes as $size) {
                                    $sizeNames[] = $size['size'];
                                }
                                echo implode(', ', $sizeNames);
                            @endphp
                        </span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-lg md:text-xl mb-3 md:mb-4 text-gray-800">Description</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 leading-relaxed">
                        @if($product->description)
                            {{ $product->description }}
                        @else
                            <span class="text-gray-500 italic">No description available.</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- RELATED PRODUCTS --}}
    @if($relatedProducts->count() > 0)
    <div class="mt-10">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl md:text-2xl font-bold">You Might Also Like</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 text-sm md:text-base flex items-center gap-1">
                View All
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($relatedProducts as $related)
            <a href="{{ route('products.show', $related->id) }}" 
               class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition active:scale-[0.98] group">
                <div class="relative aspect-square overflow-hidden">
                    <img src="{{ asset('storage/'.$related->main_image) }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    <!-- Quick Add to Cart Button -->
                    <button class="absolute bottom-3 right-3 w-10 h-10 bg-blue-600 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-lg">
                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 text-sm md:text-base group-hover:text-blue-600 transition truncate-2-lines">{{ $related->name }}</h3>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-blue-600 font-bold text-sm md:text-base">₱{{ number_format($related->base_price, 2) }}</p>
                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">
                            {{ $related->category->name }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Floating Action Button (Mobile Only) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg p-4 z-50">
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold text-lg">₱{{ number_format($product->base_price, 2) }}</p>
                <p id="mobileSelection" class="text-sm text-gray-600">Select options</p>
            </div>
            <button type="submit" 
                    form="cartForm"
                    id="mobileAddToCartBtn"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold active:bg-blue-700">
                Add to Cart
            </button>
        </div>
    </div>
</div>

{{-- VARIANT LOGIC --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const sizeButtons = document.querySelectorAll('.size-option');
    const colorButtons = document.querySelectorAll('.color-option');
    const thumbnailButtons = document.querySelectorAll('.color-thumbnail');
    const stockInfo = document.getElementById('stockInfo');
    const stockBadge = document.getElementById('stockBadge');
    const shoeImage = document.getElementById('shoeImage');
    const quantityInput = document.getElementById('quantityInput');
    const variantIdField = document.getElementById('variantIdField');
    const addToCartBtn = document.getElementById('addToCartBtn');
    const mobileAddToCartBtn = document.getElementById('mobileAddToCartBtn');
    const increaseQtyBtn = document.getElementById('increaseQty');
    const decreaseQtyBtn = document.getElementById('decreaseQty');
    const selectedOptions = document.getElementById('selectedOptions');
    const mobileSelection = document.getElementById('mobileSelection');
    
    // Get variants
    const variants = JSON.parse(document.getElementById('variantsData').textContent);

    // State
    let selectedSize = null;
    let selectedColor = null;

    // Find variant
    function findVariant(size, color) {
        return variants.find(v => v.size === size && v.color === color);
    }

    // Update UI
    function updateVariantSelection() {
        if (selectedSize && selectedColor) {
            const variant = findVariant(selectedSize, selectedColor);
            
            // Update selection text
            const selectionText = `${ucfirst(selectedColor)} • ${selectedSize}`;
            selectedOptions.textContent = selectionText;
            mobileSelection.textContent = selectionText;
            
            if (variant && variant.stock > 0) {
                // In stock
                stockInfo.textContent = variant.stock;
                stockBadge.textContent = `${variant.stock} in stock`;
                stockBadge.className = 'px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm';
                
                quantityInput.max = variant.stock;
                quantityInput.disabled = false;
                variantIdField.value = variant.id;
                
                // Update buttons
                [addToCartBtn, mobileAddToCartBtn].forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    btn.classList.add('bg-blue-600', 'active:bg-blue-700');
                });
                
                // Update image if available
                if (variant.variant_image) {
                    shoeImage.src = "{{ asset('storage/') }}/" + variant.variant_image;
                }
            } else {
                // Out of stock
                stockInfo.textContent = variant ? '0' : 'N/A';
                stockBadge.textContent = 'Out of stock';
                stockBadge.className = 'px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm';
                
                quantityInput.max = 1;
                quantityInput.disabled = true;
                variantIdField.value = "";
                
                // Disable buttons
                [addToCartBtn, mobileAddToCartBtn].forEach(btn => {
                    btn.disabled = true;
                    btn.classList.remove('bg-blue-600', 'active:bg-blue-700');
                    btn.classList.add('bg-gray-400', 'cursor-not-allowed');
                });
            }
        } else {
            // No selection
            selectedOptions.textContent = 'Select options';
            mobileSelection.textContent = 'Select options';
            stockInfo.textContent = '-';
            stockBadge.textContent = 'Select size & color';
            stockBadge.className = 'px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm';
            
            quantityInput.disabled = true;
            variantIdField.value = "";
            
            // Disable buttons
            [addToCartBtn, mobileAddToCartBtn].forEach(btn => {
                btn.disabled = true;
                btn.classList.remove('bg-blue-600', 'active:bg-blue-700');
                btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            });
        }
    }

    // Helper function
    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Size selection
    sizeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state
            sizeButtons.forEach(btn => {
                btn.classList.remove('border-blue-600', 'bg-blue-50', 'text-blue-600', 'font-bold');
                btn.classList.add('border-gray-300', 'text-gray-700');
            });
            
            // Set active
            this.classList.remove('border-gray-300', 'text-gray-700');
            this.classList.add('border-blue-600', 'bg-blue-50', 'text-blue-600', 'font-bold');
            
            selectedSize = this.dataset.size;
            updateVariantSelection();
        });
    });

    // Color selection
    colorButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state
            colorButtons.forEach(btn => {
                btn.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200');
                btn.classList.add('border-gray-300');
            });
            
            // Set active
            this.classList.remove('border-gray-300');
            this.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
            
            selectedColor = this.dataset.color;
            shoeImage.src = this.dataset.image;
            updateVariantSelection();
        });
    });

    // Thumbnail selection
    thumbnailButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedColor = this.dataset.color;
            shoeImage.src = this.dataset.image;
            
            // Update color buttons
            colorButtons.forEach(btn => {
                if (btn.dataset.color === selectedColor) {
                    colorButtons.forEach(b => b.classList.remove('border-blue-600', 'ring-2', 'ring-blue-200'));
                    btn.classList.add('border-blue-600', 'ring-2', 'ring-blue-200');
                }
            });
            
            updateVariantSelection();
        });
    });

    // Quantity controls
    increaseQtyBtn.addEventListener('click', function() {
        const current = parseInt(quantityInput.value);
        const max = parseInt(quantityInput.max);
        if (current < max) {
            quantityInput.value = current + 1;
        }
    });

    decreaseQtyBtn.addEventListener('click', function() {
        const current = parseInt(quantityInput.value);
        const min = parseInt(quantityInput.min);
        if (current > min) {
            quantityInput.value = current - 1;
        }
    });

    // Quantity input
    quantityInput.addEventListener('change', function() {
        const max = parseInt(this.max);
        const min = parseInt(this.min);
        let value = parseInt(this.value);
        
        if (isNaN(value)) value = min;
        if (value > max) value = max;
        if (value < min) value = min;
        
        this.value = value;
    });

    // Touch feedback
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('touchstart', function() {
            this.classList.add('active');
        });
        
        button.addEventListener('touchend', function() {
            this.classList.remove('active');
        });
    });

    // Form submission
    document.querySelector('form').id = 'cartForm';
    mobileAddToCartBtn.addEventListener('click', function() {
        document.getElementById('cartForm').submit();
    });

    // Initialize
    if (colorButtons.length > 0) {
        colorButtons[0].click();
    }
    if (sizeButtons.length > 0) {
        sizeButtons[0].click();
    }
});
</script>

{{-- Hidden element with variants data --}}
<script id="variantsData" type="application/json">
    {!! json_encode($product->variants) !!}
</script>

<style>
/* Touch improvements */
@media (max-width: 768px) {
    button, 
    .color-option, 
    .size-option,
    .color-thumbnail {
        min-height: 44px; /* Apple recommended touch target */
    }
    
    input[type="number"] {
        font-size: 16px; /* Prevent iOS zoom */
    }
    
    /* Prevent text selection on buttons */
    button {
        user-select: none;
        -webkit-tap-highlight-color: transparent;
    }
}

/* Line clamp for product titles */
.truncate-2-lines {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Smooth transitions */
.color-option,
.size-option,
.color-thumbnail {
    transition: all 0.2s ease;
}

/* Active state for mobile */
button.active {
    transform: scale(0.95);
}
</style>

@endsection