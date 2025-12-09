@extends('admin.layout')

@section('title', 'Message Details')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Message Details</h1>
            <p class="text-gray-600 mt-1">View and manage customer message</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.messages.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-150">
                ← Back to Messages
            </a>
            
            @if($message->status !== 'replied')
            <form action="{{ route('admin.messages.update', $message) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="replied">
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-150 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mark as Replied
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Message Card --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            {{-- Message Header --}}
            <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-3 md:mb-0">
                        <h2 class="text-xl font-bold text-gray-800">{{ $message->subject }}</h2>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            {{-- Status Badge --}}
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if($message->status == 'unread') bg-red-100 text-red-800 border border-red-200
                                    @elseif($message->status == 'read') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-green-100 text-green-800 border border-green-200 @endif">
                                    <span class="w-2 h-2 rounded-full mr-2 
                                        @if($message->status == 'unread') bg-red-500
                                        @elseif($message->status == 'read') bg-yellow-500
                                        @else bg-green-500 @endif"></span>
                                    {{ ucfirst($message->status) }}
                                </span>
                            </div>
                            
                            {{-- Type Badge --}}
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if($message->type == 'feedback') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($message->type == 'inquiry') bg-purple-100 text-purple-800 border border-purple-200
                                    @elseif($message->type == 'complaint') bg-red-100 text-red-800 border border-red-200
                                    @else bg-green-100 text-green-800 border border-green-200 @endif">
                                    @if($message->type == 'feedback')
                                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                                    </svg>
                                    @elseif($message->type == 'inquiry')
                                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                    </svg>
                                    @elseif($message->type == 'complaint')
                                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    @else
                                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                    {{ ucfirst($message->type) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs font-medium text-gray-500 uppercase tracking-wider">Message ID</div>
                        <div class="text-lg font-bold text-gray-800">#{{ $message->id }}</div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $message->created_at->format('M d, Y • h:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sender Info --}}
            <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-14 h-14 bg-white border-2 border-blue-200 rounded-full flex items-center justify-center shadow-sm">
                        <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-bold text-gray-800">{{ $message->name }}</h3>
                        <div class="flex items-center mt-1">
                            <svg class="w-4 h-4 text-gray-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            <a href="mailto:{{ $message->email }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $message->email }}
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sent {{ $message->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Message Content --}}
            <div class="p-6">
                <div class="mb-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Message Content
                    </h4>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <div class="prose max-w-none">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $message->message }}</p>
                        </div>
                    </div>
                </div>

                {{-- Message Metadata --}}
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h5 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Message Details</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase font-medium mb-1">Created</div>
                            <div class="font-medium text-gray-800">{{ $message->created_at->format('F d, Y') }}</div>
                            <div class="text-sm text-gray-600">{{ $message->created_at->format('h:i A') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase font-medium mb-1">Last Updated</div>
                            <div class="font-medium text-gray-800">{{ $message->updated_at->format('F d, Y') }}</div>
                            <div class="text-sm text-gray-600">{{ $message->updated_at->format('h:i A') }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-xs text-gray-500 uppercase font-medium mb-1">Message Age</div>
                            <div class="font-medium text-gray-800">{{ $message->created_at->diffForHumans() }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $message->created_at->diffInDays() }} days ago
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sidebar Actions --}}
    <div class="space-y-6">
        {{-- Customer Quick Info --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Customer Information
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</label>
                    <p class="mt-1 font-medium text-gray-800">{{ $message->name }}</p>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email Address</label>
                    <p class="mt-1 font-medium text-gray-800">{{ $message->email }}</p>
                </div>
                <div class="pt-4">
                    <a href="mailto:{{ $message->email }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition duration-150 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Reply via Email
                    </a>
                </div>
            </div>
        </div>

        {{-- Message Actions --}}
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Message Actions
            </h3>
            <div class="space-y-3">
                @if($message->status == 'unread')
                <form action="{{ route('admin.messages.update', $message) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="read">
                    <button type="submit" 
                            class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 border border-blue-200 transition duration-150">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <div class="text-left">
                                <div class="font-medium">Mark as Read</div>
                                <div class="text-xs text-blue-600">Remove unread status</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.messages.update', $message) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="replied">
                    <button type="submit" 
                            class="w-full flex items-center justify-between px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 border border-green-200 transition duration-150">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            <div class="text-left">
                                <div class="font-medium">Mark as Replied</div>
                                <div class="text-xs text-green-600">Mark as responded</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>

                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this message?')"
                            class="w-full flex items-center justify-between px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 border border-red-200 transition duration-150">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <div class="text-left">
                                <div class="font-medium">Delete Message</div>
                                <div class="text-xs text-red-600">Permanently remove</div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Message History --}}
        @php
            $similarMessages = \App\Models\Message::where('email', $message->email)
                ->where('id', '!=', $message->id)
                ->latest()
                ->take(3)
                ->get();
        @endphp
        
        @if($similarMessages->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Message History
            </h3>
            <div class="space-y-3">
                @foreach($similarMessages as $similar)
                <a href="{{ route('admin.messages.show', $similar) }}" 
                   class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150 group">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="font-medium text-gray-800 group-hover:text-blue-600">{{ Str::limit($similar->subject, 40) }}</div>
                            <div class="text-sm text-gray-500 mt-1">{{ $similar->created_at->format('M d, Y') }}</div>
                        </div>
                        <span class="ml-3 px-2 py-1 text-xs rounded-full font-medium
                            @if($similar->status == 'unread') bg-red-100 text-red-800
                            @elseif($similar->status == 'read') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($similar->status) }}
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection