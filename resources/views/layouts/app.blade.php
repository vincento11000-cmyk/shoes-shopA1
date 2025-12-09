<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ApexSole | Premium Footwear</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --accent: #f97316;
        }
        
        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #f97316;
            color: white;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .profile-initial {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        }
        
        .dropdown-menu {
            animation: fadeIn 0.15s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .mobile-menu {
            animation: slideDown 0.2s ease-out;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .nav-link {
            position: relative;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-hover);
        }
        
        .nav-link.active {
            color: var(--primary-hover);
            font-weight: 600;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--primary-hover);
        }
    </style>
</head>
<body class="bg-gray-50" x-data="{ dropdownOpen: false, mobileMenuOpen: false }">

    {{-- NAVBAR --}}
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                {{-- Logo and Mobile Menu Button --}}
                <div class="flex items-center">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 mr-2">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <a href="/" class="flex items-center space-x-2">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center shadow-md">
                            <i class="fas fa-shoe-prints text-white"></i>
                        </div>
                        <span class="text-xl font-bold text-gray-900">ApexSole</span>
                    </a>
                    
                    {{-- Desktop Navigation --}}
                    <div class="hidden lg:flex ml-10 space-x-8">
                        <a href="{{ route('home') }}" 
                           class="nav-link {{ request()->routeIs('home') ? 'active' : '' }} text-gray-700 font-medium">
                            Home
                        </a>
                        <a href="{{ route('products.index') }}" 
                           class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }} text-gray-700 font-medium">
                            Shop
                        </a>
                        @auth
                            <a href="{{ route('orders') }}" 
                               class="nav-link {{ request()->routeIs('orders') ? 'active' : '' }} text-gray-700 font-medium">
                                Orders
                            </a>
                        @endauth
                    </div>
                </div>
                
                {{-- Right side items --}}
                <div class="flex items-center space-x-6">
                    {{-- Cart with badge --}}
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-blue-600 transition-colors">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        
                        {{-- CART COUNTER --}}
                        @auth
                            @php
                                // Get the user's cart
                                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                                $cartCount = 0;
                                
                                // If cart exists, count the items in it
                                if ($cart) {
                                    $cartCount = $cart->items()->count();
                                }
                            @endphp
                            @if($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        @endauth
                    </a>
                    
                    {{-- Admin Panel Button (Desktop) --}}
                    @auth
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="hidden lg:flex items-center bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-lg font-medium hover:shadow-lg transition-all">
                                <i class="fas fa-cog mr-2"></i>
                                <span>Admin</span>
                            </a>
                        @endif
                    @endauth
                    
                    {{-- User Profile or Login --}}
                    @auth
                        <div class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    class="flex items-center space-x-3 focus:outline-none group">
                                {{-- NAV BAR: Name Only (No Email) --}}
                                <div class="hidden lg:block text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                </div>
                                
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-10 h-10 rounded-full border-2 border-white shadow-sm group-hover:border-blue-100 transition-all">
                                @else
                                    <div class="w-10 h-10 rounded-full profile-initial text-white flex items-center justify-center text-base font-bold border-2 border-white shadow-sm group-hover:border-blue-100 transition-all">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                
                                <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" 
                                   :class="{'rotate-180': dropdownOpen}"></i>
                            </button>
                            
                            {{-- DROPDOWN MENU: Shows Email --}}
                            <div x-show="dropdownOpen" 
                                 @click.away="dropdownOpen = false"
                                 class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border dropdown-menu z-10"
                                 x-transition
                                 style="display: none;">
                                
                                {{-- User Info Header with Email --}}
                                <div class="px-4 py-3 border-b bg-gradient-to-r from-gray-50 to-white">
                                    <div class="flex items-center space-x-3">
                                        @if(Auth::user()->profile_picture)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                                 alt="{{ Auth::user()->name }}" 
                                                 class="w-12 h-12 rounded-full border-2 border-white shadow">
                                        @else
                                            <div class="w-12 h-12 rounded-full profile-initial flex items-center justify-center text-white text-lg font-bold border-2 border-white shadow">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                            {{-- DROPDOWN: Email included --}}
                                            <p class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</p>
                                            @if(Auth::user()->hasRole('admin'))
                                                <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold bg-purple-100 text-purple-800 rounded-full">
                                                    Admin
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Dropdown Items --}}
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                                       @click="dropdownOpen = false">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">My Profile</p>
                                            <p class="text-xs text-gray-500">Edit personal information</p>
                                        </div>
                                    </a>
                                    
                                    <a href="{{ route('orders') }}" 
                                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors"
                                       @click="dropdownOpen = false">
                                        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-clipboard-list text-green-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium">My Orders</p>
                                            <p class="text-xs text-gray-500">View order history</p>
                                        </div>
                                    </a>
                                    
                                    @if(auth()->user()->hasRole('admin'))
                                        <a href="{{ route('admin.dashboard') }}" 
                                           class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors border-t border-gray-100"
                                           @click="dropdownOpen = false">
                                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                                <i class="fas fa-cog text-purple-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Admin Dashboard</p>
                                                <p class="text-xs text-gray-500">Manage website</p>
                                            </div>
                                        </a>
                                    @endif
                                    
                                    <div class="border-t border-gray-100"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 transition-colors"
                                                @click="dropdownOpen = false">
                                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center mr-3">
                                                <i class="fas fa-sign-out-alt text-red-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium">Logout</p>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 rounded-lg font-medium hover:shadow-md transition-all">
                                Sign Up
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        
        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" 
             class="lg:hidden bg-white border-t mobile-menu"
             x-transition
             style="display: none;">
            <div class="px-4 py-3 space-y-1">
                {{-- Navigation Links --}}
                <a href="{{ route('home') }}" 
                   class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 font-medium"
                   @click="mobileMenuOpen = false">
                    <i class="fas fa-home mr-3 w-6 text-center"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('products.index') }}" 
                   class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 font-medium"
                   @click="mobileMenuOpen = false">
                    <i class="fas fa-store mr-3 w-6 text-center"></i>
                    <span>Shop</span>
                </a>
                
                @auth
                    {{-- User Section --}}
                    <div class="border-t border-gray-200 pt-3 mt-2">
                        <div class="px-3 py-2 mb-2">
                            <div class="flex items-center">
                                @if(Auth::user()->profile_picture)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                         alt="{{ Auth::user()->name }}" 
                                         class="w-10 h-10 rounded-full border-2 border-white shadow mr-3">
                                @else
                                    <div class="w-10 h-10 rounded-full profile-initial flex items-center justify-center text-white font-bold mr-3">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    {{-- MOBILE: Shows Name Only --}}
                                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('orders') }}" 
                           class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 font-medium"
                           @click="mobileMenuOpen = false">
                            <i class="fas fa-clipboard-list mr-3 w-6 text-center"></i>
                            <span>My Orders</span>
                        </a>
                        
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100"
                           @click="mobileMenuOpen = false">
                            <i class="fas fa-user-edit mr-3 w-6 text-center"></i>
                            <span>Edit Profile</span>
                        </a>
                        
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center px-3 py-3 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 text-blue-700 font-medium"
                               @click="mobileMenuOpen = false">
                                <i class="fas fa-cog mr-3 w-6 text-center"></i>
                                <span>Admin Panel</span>
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full px-3 py-3 rounded-lg text-red-600 hover:bg-red-50 font-medium"
                                    @click="mobileMenuOpen = false">
                                <i class="fas fa-sign-out-alt mr-3 w-6 text-center"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                @else
                    {{-- Guest Links --}}
                    <div class="border-t border-gray-200 pt-3 mt-2">
                        <a href="{{ route('login') }}" 
                           class="flex items-center px-3 py-3 rounded-lg text-gray-700 hover:bg-gray-100 font-medium mb-2"
                           @click="mobileMenuOpen = false">
                            <i class="fas fa-sign-in-alt mr-3 w-6 text-center"></i>
                            <span>Sign In</span>
                        </a>
                        <a href="{{ route('register') }}" 
                           class="flex items-center justify-center px-3 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium"
                           @click="mobileMenuOpen = false">
                            <i class="fas fa-user-plus mr-2"></i>
                            <span>Create Account</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    {{-- Flash Messages --}}
    @if(session('success') || session('error'))
        <div class="fixed bottom-4 right-4 z-50">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 shadow-lg mb-2 max-w-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 shadow-lg max-w-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

</body>
</html>