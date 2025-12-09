@extends('admin.layout')

@section('title', 'Customer Messages')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold">Customer Messages</h1>
        <p class="text-gray-600 mt-1">View and manage customer feedback, inquiries, and complaints</p>
    </div>
    
    <div class="flex items-center space-x-4">
        {{-- Unread Counter --}}
        <div class="bg-blue-50 px-4 py-2 rounded-lg">
            <span class="text-sm text-gray-600">Unread:</span>
            <span class="ml-2 font-bold text-blue-600">{{ $unreadCount }}</span>
        </div>
        
        {{-- Filter --}}
        <select id="filterType" class="border border-gray-300 rounded px-3 py-2 text-sm">
            <option value="all">All Messages</option>
            <option value="unread">Unread Only</option>
            <option value="feedback">Feedback</option>
            <option value="inquiry">Inquiries</option>
            <option value="complaint">Complaints</option>
            <option value="suggestion">Suggestions</option>
        </select>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Messages</p>
                <p class="text-2xl font-bold">{{ $messages->count() }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-red-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600">Unread</p>
                <p class="text-2xl font-bold">{{ $unreadCount }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600">Replied</p>
                <p class="text-2xl font-bold">{{ $repliedCount }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-600">Today's Messages</p>
                <p class="text-2xl font-bold">{{ $todayCount }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Messages Table --}}
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sender</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                <tr class="{{ $message->status == 'unread' ? 'bg-blue-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">#{{ $message->id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $message->name }}</div>
                        <div class="text-sm text-gray-500">{{ $message->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 font-medium">{{ $message->subject }}</div>
                        <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($message->message, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $typeColors = [
                                'feedback' => 'bg-blue-100 text-blue-800',
                                'inquiry' => 'bg-purple-100 text-purple-800',
                                'complaint' => 'bg-red-100 text-red-800',
                                'suggestion' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$message->type] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($message->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusColors = [
                                'unread' => 'bg-red-100 text-red-800',
                                'read' => 'bg-yellow-100 text-yellow-800',
                                'replied' => 'bg-green-100 text-green-800',
                            ];
                        @endphp
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$message->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($message->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $message->created_at->format('M d, Y') }}<br>
                        <span class="text-gray-400">{{ $message->created_at->format('h:i A') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.messages.show', $message) }}" 
                               class="text-blue-600 hover:text-blue-900 px-3 py-1 bg-blue-50 rounded hover:bg-blue-100">
                                View
                            </a>
                            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to delete this message?')"
                                        class="text-red-600 hover:text-red-900 px-3 py-1 bg-red-50 rounded hover:bg-red-100">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-600">No messages yet</h3>
                        <p class="text-gray-500 mt-1">Customer messages will appear here when they contact you.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination --}}
    @if($messages->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $messages->links() }}
    </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filterType');
    
    filterSelect.addEventListener('change', function() {
        const filter = this.value;
        let url = new URL(window.location.href);
        
        if (filter === 'all') {
            url.searchParams.delete('filter');
        } else {
            url.searchParams.set('filter', filter);
        }
        
        window.location.href = url.toString();
    });
    
    // Set current filter in select
    const urlParams = new URLSearchParams(window.location.search);
    const currentFilter = urlParams.get('filter') || 'all';
    filterSelect.value = currentFilter;
});
</script>
@endsection