@extends('layouts.app')

@section('title', 'My Profile')

@if(!auth()->user()->email_verified_at)
<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
    <p class="font-bold">Email Not Verified</p>
    <p>Your email address is not verified. Please verify your email to access all features.</p>
    <a href="{{ route('otp.verify.show') }}" class="text-blue-600 hover:text-blue-800 underline mt-2 inline-block">
        Verify Email Now
    </a>
</div>
@endif

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">My Profile</h1>

    <!-- Success Messages -->
    @if (session('status') === 'profile-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            Profile updated successfully!
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            Password updated successfully!
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Profile Information -->
        <div class="md:col-span-2 space-y-6">
            <!-- Update Profile Picture -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Profile Picture</h2>
                
                <div class="flex items-center space-x-6">
                    <!-- Current Profile Picture - Wrapped in div with ID for easy updating -->
                    <div id="profilePictureContainer">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                 alt="Profile Picture" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                        @else
                            <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Upload and Remove Forms -->
                    <div class="flex-1">
                        <!-- Upload Form -->
                        <form method="POST" action="{{ route('profile.picture.update') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('patch')
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Upload new picture
                                </label>
                                <input type="file" 
                                       name="profile_picture" 
                                       id="profile_picture"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-1">Maximum file size: 2MB. Allowed formats: JPG, PNG, GIF.</p>
                                @error('profile_picture')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button type="submit" 
                                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                                Upload Picture
                            </button>
                        </form>
                        
                        <!-- Remove Picture Form - SEPARATE FORM -->
                        @if(Auth::user()->profile_picture)
                        <form method="POST" action="{{ route('profile.picture.destroy') }}" class="mt-3" id="removePictureForm">
                            @csrf
                            @method('delete')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to remove your profile picture?')"
                                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                                Remove Picture
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Update Profile Information -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Profile Information</h2>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Optional">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <textarea name="address" id="address" rows="2"
                                      class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Optional">{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Save Profile Changes
                    </button>
                </form>
            </div>

            <!-- Update Password -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Update Password</h2>
                
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" name="current_password" id="current_password" 
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Enter your current password">
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="password" id="password" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter new password">
                            @error('password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Confirm new password">
                        </div>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Update Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Info with Profile Picture -->
            <div class="bg-white p-6 rounded-lg shadow text-center">
                <!-- Profile Picture Display - Wrapped in div with ID -->
                <div id="sidebarProfilePicture" class="flex justify-center mb-4">
                    @if(Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                             alt="Profile Picture" 
                             class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow">
                    @else
                        <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-100 shadow">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <h2 class="text-xl font-semibold mb-2">{{ Auth::user()->name }}</h2>
                <p class="text-gray-600 text-sm mb-4">{{ Auth::user()->email }}</p>
                
                <div class="space-y-3 text-left">
                    <div>
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="font-semibold">{{ Auth::user()->created_at->format('F j, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Account Type</p>
                        <p class="font-semibold">
                            @if(Auth::user()->hasRole('admin'))
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                    Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Customer
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-semibold mb-4">Quick Links</h2>
                <div class="space-y-3">
                    <a href="{{ route('products.index') }}" 
                       class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Continue Shopping
                    </a>
                    <a href="{{ route('cart.index') }}" 
                       class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        View Cart
                    </a>
                    <a href="{{ route('orders') }}" 
                       class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Order History
                    </a>
                    <a href="{{ route('home') }}" 
                       class="flex items-center text-blue-600 hover:text-blue-800 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Home Page
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white p-6 rounded-lg shadow border border-red-200">
                <h2 class="text-xl font-semibold mb-4 text-red-600">Danger Zone</h2>
                <p class="text-sm text-gray-600 mb-3">Once you delete your account, there is no going back. Please be certain.</p>
                
                @if ($errors->userDeletion->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->userDeletion->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')
                    
                    <!-- Password field (REQUIRED by your controller) -->
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Your Password
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-red-500 focus:border-red-500"
                               placeholder="Enter your current password"
                               required>
                        @error('password', 'userDeletion')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" 
                            onclick="return confirmDelete()"
                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm w-full">
                        Delete My Account
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview image before upload
document.getElementById('profile_picture')?.addEventListener('change', function(e) {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Update main profile picture
            const mainContainer = document.getElementById('profilePictureContainer');
            if (mainContainer) {
                const initial = '{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}';
                mainContainer.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Profile Picture" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                `;
            }
            
            // Update sidebar profile picture
            const sidebarContainer = document.getElementById('sidebarProfilePicture');
            if (sidebarContainer) {
                sidebarContainer.innerHTML = `
                    <img src="${e.target.result}" 
                         alt="Profile Picture" 
                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow">
                `;
            }
        };
        reader.readAsDataURL(this.files[0]);
    }
});

// Confirm account deletion
function confirmDelete() {
    const passwordInput = document.getElementById('password');
    const password = passwordInput?.value.trim();
    
    // Check if password is entered
    if (!password) {
        alert('Please enter your password to confirm account deletion.');
        if (passwordInput) {
            passwordInput.focus();
        }
        return false;
    }
    
    // Show confirmation dialog
    const confirmMessage = '⚠️ WARNING: This will permanently delete your account and all associated data.\n\nThis action cannot be undone. Are you absolutely sure?';
    return confirm(confirmMessage);
}

// Function to revert to initial letter when picture is removed (for AJAX)
function revertToInitialAvatar() {
    const initial = '{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}';
    
    // Update main profile picture container
    const mainContainer = document.getElementById('profilePictureContainer');
    if (mainContainer) {
        mainContainer.innerHTML = `
            <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold border-4 border-gray-200">
                ${initial}
            </div>
        `;
    }
    
    // Update sidebar profile picture container
    const sidebarContainer = document.getElementById('sidebarProfilePicture');
    if (sidebarContainer) {
        sidebarContainer.innerHTML = `
            <div class="w-24 h-24 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-100 shadow">
                ${initial}
            </div>
        `;
    }
    
    // Hide the remove picture button
    const removeForm = document.getElementById('removePictureForm');
    if (removeForm) {
        removeForm.style.display = 'none';
    }
}

// If you want to use AJAX for removing picture (optional)
document.addEventListener('DOMContentLoaded', function() {
    const removePictureForm = document.getElementById('removePictureForm');
    if (removePictureForm) {
        // Optional: AJAX submit for instant feedback
        removePictureForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to remove your profile picture?')) {
                // Submit form normally
                this.submit();
                
                // If you want AJAX instead, use this:
                
                fetch(this.action, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        revertToInitialAvatar();
                        // Show success message
                        alert('Profile picture removed successfully!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
                
            }
        });
    }
});
</script>
@endpush