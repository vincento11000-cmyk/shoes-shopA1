@extends('layouts.app')

@section('title', 'Thank You!')

@section('content')
<div class="max-w-2xl mx-auto py-16 px-4 text-center">
    <div class="bg-white rounded-xl shadow-lg p-8">
        {{-- Success Icon --}}
        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
            </svg>
        </div>

        {{-- Message --}}
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Message Sent Successfully!</h1>
        
        <div class="space-y-4 mb-8">
            <p class="text-lg text-gray-600">
                Thank you for reaching out to us! Your message has been saved in our system.
            </p>
            
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-blue-800">
                    <strong>What happens next?</strong>
                </p>
                <ul class="text-left mt-2 space-y-1 text-blue-700">
                    <li>✓ Your message has been recorded with ID: #{{ rand(1000, 9999) }}</li>
                    <li>✓ Our team will review it within 24-48 hours</li>
                    <li>✓ We'll respond to you via email if needed</li>
                    <li>✓ Your feedback helps us improve our service</li>
                </ul>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="space-y-4">
            <a href="{{ url('/') }}" 
               class="inline-block w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Return to Home
            </a>
            
            <p class="text-gray-500 text-sm">
                Need immediate assistance? Call us at <strong>(02) 1234-5678</strong>
            </p>
        </div>
    </div>
</div>
@endsection