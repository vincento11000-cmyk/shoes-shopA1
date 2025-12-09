@extends('layouts.app')

@section('title', 'Send Feedback / Message')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="bg-white rounded-xl shadow-lg p-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-3">Send Us a Message</h1>
            <p class="text-gray-600">
                We'd love to hear from you! Share your feedback, suggestions, or ask us anything.
            </p>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('feedback.store') }}" class="space-y-6">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Name *
                </label>
                <input type="text" name="name" 
                       value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address *
                </label>
                <input type="email" name="email" 
                       value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Subject *
                </label>
                <input type="text" name="subject" 
                       value="{{ old('subject') }}"
                       placeholder="What is this regarding?"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       required>
                @error('subject')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message Type *
                </label>
                <select name="type" 
                        class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="feedback" {{ old('type') == 'feedback' ? 'selected' : '' }}>General Feedback</option>
                    <option value="inquiry" {{ old('type') == 'inquiry' ? 'selected' : '' }}>Product Inquiry</option>
                    <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                    <option value="complaint" {{ old('type') == 'complaint' ? 'selected' : '' }}>Complaint</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Message --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Your Message *
                </label>
                <textarea name="message" rows="6"
                          class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Please share your thoughts, questions, or concerns in detail..."
                          required>{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit" 
                        class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Send Message
                </button>
            </div>

            {{-- Privacy Note --}}
            <div class="text-center text-sm text-gray-500 pt-4">
                <p>Your message will be saved in our system and our team will review it shortly.</p>
                <p class="mt-1">We typically respond within 24-48 hours.</p>
            </div>
        </form>
    </div>

    {{-- Back Link --}}
    <div class="text-center mt-6">
        <a href="{{ url('/') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            ← Back to Home
        </a>
    </div>
</div>
@endsection