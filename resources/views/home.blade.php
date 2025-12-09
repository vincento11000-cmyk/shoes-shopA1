@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-8">
    <!-- Hero/Intro Section -->
    <div class="mb-12 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Step Into Style</h1>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">Discover the perfect pair that matches your stride</p>
    </div>

    <!-- Featured Shoes Carousel -->
    <section class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Featured Shoes</h2>
            <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm uppercase tracking-wide">
                View All →
            </a>
        </div>
        
        <div class="featured-carousel overflow-x-auto flex space-x-6 pb-4 cursor-grab active:cursor-grabbing" style="scroll-behavior: smooth;">
            @foreach($featured as $shoe)
            <div class="flex-shrink-0 w-72">
                <div class="group">
                    <a href="{{ route('products.show', $shoe->id) }}" 
                       class="block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="relative overflow-hidden bg-gray-100">
                            <img src="{{ asset('storage/'.$shoe->main_image) }}" 
                                 alt="{{ $shoe->name }}"
                                 class="w-full h-64 object-contain p-4 group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-4 right-4">
                                <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-semibold">Featured</span>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $shoe->name }}</h3>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-gray-900">₱{{ number_format($shoe->base_price, 0) }}</span>
                                <span class="text-gray-400 text-sm">Free Shipping</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- New Arrivals Carousel -->
    <section class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <div>
                <span class="text-blue-600 font-semibold text-sm uppercase tracking-wider">Just Dropped</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">New Arrivals</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm uppercase tracking-wide">
                See Newest →
            </a>
        </div>
        
        <div class="new-carousel overflow-x-auto flex space-x-6 pb-4 cursor-grab active:cursor-grabbing" style="scroll-behavior: smooth;">
            @foreach($new as $shoe)
            <div class="flex-shrink-0 w-72">
                <div class="group">
                    <a href="{{ route('products.show', $shoe->id) }}" 
                       class="block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100">
                            <img src="{{ asset('storage/'.$shoe->main_image) }}" 
                                 alt="{{ $shoe->name }}"
                                 class="w-full h-64 object-contain p-6 group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500 rounded-2xl transition-colors duration-300"></div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $shoe->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">₱{{ number_format($shoe->base_price, 0) }}</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">New</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Best Sellers Carousel -->
    <section class="mb-16">
        <div class="flex justify-between items-center mb-8">
            <div>
                <span class="text-amber-600 font-semibold text-sm uppercase tracking-wider">Customer Favorite</span>
                <h2 class="text-3xl font-bold text-gray-900 mt-1">Best Sellers</h2>
            </div>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm uppercase tracking-wide">
                Shop Trending →
            </a>
        </div>
        
        <div class="best-carousel overflow-x-auto flex space-x-6 pb-4 cursor-grab active:cursor-grabbing" style="scroll-behavior: smooth;">
            @foreach($best as $shoe)
            <div class="flex-shrink-0 w-72">
                <div class="group relative">
                    <a href="{{ route('products.show', $shoe->id) }}" 
                       class="block bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
                        <div class="relative overflow-hidden bg-gradient-to-br from-amber-50 to-white">
                            <img src="{{ asset('storage/'.$shoe->main_image) }}" 
                                 alt="{{ $shoe->name }}"
                                 class="w-full h-64 object-contain p-6">
                            <!-- Bestseller badge -->
                            <div class="absolute top-4 left-4">
                                <div class="bg-amber-500 text-white px-3 py-1 rounded-r-full text-xs font-bold flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    Top Seller
                                </div>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-1">{{ $shoe->name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-900">₱{{ number_format($shoe->base_price, 0) }}</span>
                                <div class="flex items-center text-amber-500">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span class="ml-1 text-sm font-semibold">4.8</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Call to Action -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-10 text-center mb-16">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Find Your Perfect Pair?</h2>
        <p class="text-blue-100 mb-6 max-w-2xl mx-auto">Explore our complete collection with 100+ styles for every occasion</p>
        <a href="{{ route('products.index') }}" 
           class="inline-block bg-white text-blue-700 font-semibold px-8 py-4 rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
            Browse All Shoes
        </a>
    </div>
</div>

@include('components.footer')

@endsection

@push('styles')
<style>
    .line-clamp-1 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 1;
    }
    .group:hover .group-hover\:scale-105 {
        transform: scale(1.05);
    }
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    /* Hide scrollbar but keep functionality */
    .featured-carousel,
    .new-carousel,
    .best-carousel {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    
    .featured-carousel::-webkit-scrollbar,
    .new-carousel::-webkit-scrollbar,
    .best-carousel::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera */
    }
    
    /* Prevent image selection during drag */
    .featured-carousel img,
    .new-carousel img,
    .best-carousel img {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple drag-to-scroll functionality
    function makeDraggable(carousel) {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        carousel.addEventListener('mousedown', (e) => {
            isDown = true;
            carousel.classList.add('cursor-grabbing');
            carousel.classList.remove('cursor-grab');
            startX = e.pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });
        
        carousel.addEventListener('mouseleave', () => {
            isDown = false;
            carousel.classList.remove('cursor-grabbing');
            carousel.classList.add('cursor-grab');
        });
        
        carousel.addEventListener('mouseup', () => {
            isDown = false;
            carousel.classList.remove('cursor-grabbing');
            carousel.classList.add('cursor-grab');
        });
        
        carousel.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - carousel.offsetLeft;
            const walk = (x - startX) * 2; // Scroll speed multiplier
            carousel.scrollLeft = scrollLeft - walk;
        });
        
        // Touch support for mobile
        carousel.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - carousel.offsetLeft;
            scrollLeft = carousel.scrollLeft;
        });
        
        carousel.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - carousel.offsetLeft;
            const walk = (x - startX) * 1.5;
            carousel.scrollLeft = scrollLeft - walk;
        });
        
        carousel.addEventListener('touchend', () => {
            isDown = false;
        });
    }
    
    // Apply to all carousels
    const carousels = document.querySelectorAll('.featured-carousel, .new-carousel, .best-carousel');
    carousels.forEach(makeDraggable);
    
    // Enable smooth trackpad/mouse wheel scrolling
    carousels.forEach(carousel => {
        carousel.addEventListener('wheel', (e) => {
            // Allow natural horizontal scrolling
            if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                // Horizontal scroll - let browser handle it
                return;
            }
            
            // If vertical scroll but holding shift, convert to horizontal
            if (e.shiftKey) {
                e.preventDefault();
                carousel.scrollLeft += e.deltaY * 2;
            }
            // For two-finger horizontal scrolling on trackpad
            else if (e.deltaX !== 0) {
                e.preventDefault();
                carousel.scrollLeft += e.deltaX;
            }
        }, { passive: false });
    });
});
</script>
@endpush