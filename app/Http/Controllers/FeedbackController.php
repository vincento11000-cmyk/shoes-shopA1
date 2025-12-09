<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class FeedbackController extends Controller
{
    // Show feedback form - THIS WAS MISSING OR MISNAMED
    public function create()
    {
        return view('feedback.create');
    }

    // Store feedback/message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'type' => 'required|in:feedback,inquiry,complaint,suggestion'
        ]);

        // Save message to database
        $message = Message::create($validated);

        return redirect()->route('feedback.thankyou')
                         ->with('success', 'Thank you for your message! We will get back to you soon.');
    }

    // Thank you page
    public function thankyou()
    {
        return view('feedback.thankyou');
    }

    // Admin: View all messages
    public function index(Request $request)
    {
        $query = Message::latest();
        
        // Apply filters
        if ($request->has('filter')) {
            $filter = $request->filter;
            
            if ($filter === 'unread') {
                $query->where('status', 'unread');
            } elseif (in_array($filter, ['feedback', 'inquiry', 'complaint', 'suggestion'])) {
                $query->where('type', $filter);
            }
        }
        
        $messages = $query->paginate(20);
        
        // Statistics
        $unreadCount = Message::where('status', 'unread')->count();
        $repliedCount = Message::where('status', 'replied')->count();
        $todayCount = Message::whereDate('created_at', today())->count();
        
        return view('admin.messages.index', compact('messages', 'unreadCount', 'repliedCount', 'todayCount'));
    }

    // Admin: View single message
    public function show(Message $message)
    {
        // Mark as read when viewed (if not already read)
        if ($message->status === 'unread') {
            $message->update(['status' => 'read']);
        }
        
        return view('admin.messages.show', compact('message'));
    }

    // Admin: Update message status
    public function update(Request $request, Message $message)
    {
        $request->validate([
            'status' => 'required|in:read,replied'
        ]);
        
        $message->update(['status' => $request->status]);
        
        return back()->with('success', 'Message status updated successfully.');
    }

    // Admin: Delete message
    public function destroy(Message $message)
    {
        $message->delete();
        
        return redirect()->route('admin.messages.index')
                         ->with('success', 'Message deleted successfully.');
    }
}