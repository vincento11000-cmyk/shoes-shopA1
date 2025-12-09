<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - ApexSole</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* Custom styles for better visibility */
        .mobile-menu-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .mobile-menu-btn:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        .sidebar {
            background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
        }
        
        .user-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .active-nav-item {
            background: rgba(102, 126, 234, 0.15);
            border-left: 4px solid #667eea;
        }
        
        .badge-notification {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ 
    dropdownOpen: false, 
    mobileMenuOpen: false
}" @click.away="dropdownOpen = false">

    {{-- Mobile Top Bar --}}
    <header class="lg:hidden fixed top-0 left-0 right-0 bg-white border-b shadow-lg z-50">
        <div class="flex items-center justify-between px-4 py-3">
            {{-- Menu Button - Made more prominent --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="mobile-menu-btn p-3 flex items-center justify-center">
                <span class="material-icons text-white text-xl font-bold">
                    <template x-if="!mobileMenuOpen">menu</template>
                    <template x-if="mobileMenuOpen">close</template>
                </span>
            </button>
            
            {{-- Title --}}
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-600 to-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold text-sm">AS</span>
                </div>
                <h1 class="text-lg font-bold text-gray-800">
                    @yield('title', 'Dashboard')
                </h1>
            </div>
            
            {{-- User & Notifications --}}
            <div class="flex items-center space-x-3">
                <button class="p-2 relative hover:bg-gray-100 rounded-full">
                    <span class="material-icons text-gray-700">notifications</span>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                <div class="user-avatar w-9 h-9 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </div>
        
        {{-- Mobile Breadcrumb --}}
        <div class="px-4 pb-3">
            <div class="flex items-center text-sm text-gray-600">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-600 flex items-center">
                    <span class="material-icons text-xs mr-1">home</span>
                    Dashboard
                </a>
                <span class="mx-2">›</span>
                <span class="text-gray-800 font-medium">@yield('current-page', 'Overview')</span>
            </div>
        </div>
    </header>

    <div class="flex pt-20 lg:pt-0">

        {{-- Sidebar --}}
        <aside class="sidebar text-white h-screen fixed lg:sticky top-0 left-0 z-40 w-64 transform transition-transform duration-300 lg:translate-x-0"
               :class="{'-translate-x-full': !mobileMenuOpen, 'translate-x-0': mobileMenuOpen}">
            
            {{-- Brand --}}
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-purple-600 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-lg">A</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">ApexSole</h2>
                        <p class="text-sm text-gray-300">Admin Panel</p>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-blue-400">dashboard</span>
                    <span class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.orders') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.orders') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-green-400">shopping_cart</span>
                    <span class="font-medium">Orders</span>
                    <span class="ml-auto bg-blue-500/20 text-blue-300 text-xs font-medium px-2 py-1 rounded-full">12</span>
                </a>

                <a href="{{ route('admin.categories.index') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-purple-400">category</span>
                    <span class="font-medium">Categories</span>
                </a>

                <a href="{{ route('admin.products.index') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-yellow-400">inventory</span>
                    <span class="font-medium">Products</span>
                </a>

                <a href="{{ route('admin.variants.index') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.variants.*') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-pink-400">color_lens</span>
                    <span class="font-medium">Variants</span>
                </a>

                <a href="{{ route('admin.messages.index') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 {{ request()->routeIs('admin.messages.*') ? 'active-nav-item text-white' : 'text-gray-300' }}"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-red-400">message</span>
                    <span class="font-medium">Messages</span>
                    @php
                        $unreadCount = \App\Models\Message::where('status', 'unread')->count();
                    @endphp
                    @if($unreadCount > 0)
                    <span class="ml-auto badge-notification bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>

                <div class="h-px bg-gray-700 my-4"></div>

                <a href="{{ route('home') }}" 
                   class="flex items-center p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 text-gray-300"
                   @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                    <span class="material-icons mr-3 text-green-400">store</span>
                    <span class="font-medium">View Store</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                    @csrf
                    <button type="submit" 
                            class="flex items-center w-full p-3 rounded-lg hover:bg-gray-700 transition-all duration-200 text-gray-300"
                            @click="if (window.innerWidth < 1024) mobileMenuOpen = false">
                        <span class="material-icons mr-3 text-gray-400">logout</span>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </nav>
        </aside>

        {{-- Overlay for mobile sidebar --}}
        <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-black bg-opacity-60 z-30 lg:hidden"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen w-full">
            {{-- Desktop Header --}}
            <header class="hidden lg:block bg-white border-b px-8 py-4 shadow-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                        <div class="flex items-center text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard') }}" class="hover:text-purple-600">Dashboard</a>
                            <span class="mx-2">/</span>
                            <span class="text-gray-700 font-medium">@yield('current-page', 'Overview')</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <a href="{{ route('home') }}" 
                           class="px-4 py-2 bg-gradient-to-r from-purple-600 to-blue-500 text-white rounded-lg hover:shadow-lg transition-shadow duration-200">
                            View Store
                        </a>
                        
                        <div class="relative" x-data="{ dropdownOpen: false }">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center space-x-4 hover:bg-gray-50 p-2 rounded-xl transition-all duration-200">
                                <div class="user-avatar w-12 h-12 text-white rounded-full flex items-center justify-center font-bold text-lg">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-500">Administrator</p>
                                </div>
                                <span class="material-icons text-gray-400 transition-transform duration-200" 
                                      :class="{'rotate-180': dropdownOpen}">expand_more</span>
                            </button>
                            
                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute bg-white shadow-xl rounded-xl mt-2 py-2 w-64 right-0 border border-gray-200">
                                <div class="px-4 py-3 border-b">
                                    <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('admin.profile.edit') }}" 
                                   class="flex items-center px-4 py-3 hover:bg-gray-50 text-gray-700">
                                    <span class="material-icons mr-3 text-gray-500">person</span>
                                    <span>Edit Profile</span>
                                </a>
                                <a href="{{ route('home') }}" 
                                   class="flex items-center px-4 py-3 hover:bg-gray-50 text-gray-700">
                                    <span class="material-icons mr-3 text-gray-500">store</span>
                                    <span>View Store</span>
                                </a>
                                <div class="border-t my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center w-full px-4 py-3 hover:bg-red-50 text-red-600">
                                        <span class="material-icons mr-3">logout</span>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Content Area --}}
            <main class="flex-1 p-4 lg:p-8 bg-gray-50">
                {{-- Flash Messages --}}
                @if(session('success'))
                <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <span class="material-icons text-green-500 mr-3">check_circle</span>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <span class="material-icons text-red-500 mr-3">error</span>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
                @endif

                @yield('content')
            </main>

           

    <script>
        // Add smooth scrolling and better mobile experience
        document.addEventListener('DOMContentLoaded', function() {
            // Close mobile menu when clicking on links
            const mobileLinks = document.querySelectorAll('aside a');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        Alpine.store('mobileMenuOpen', false);
                    }
                });
            });
            
            // Prevent body scroll when mobile menu is open
            Alpine.effect(() => {
                const isMobileMenuOpen = Alpine.store('mobileMenuOpen');
                if (isMobileMenuOpen && window.innerWidth < 1024) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            });
        });
    </script>

</body>
</html>