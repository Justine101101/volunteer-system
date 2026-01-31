<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentFilter;

class MessagingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:superadmin');
    }

    /**
     * Display the inbox with all conversations.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all unique users that the current user has exchanged messages with
        $messages = Message::where(function($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();
        
        $conversations = collect();
        
        if ($messages->isNotEmpty()) {
            $conversations = $messages->groupBy(function($message) use ($user) {
                    // Group by the other user in the conversation
                    return $message->sender_id === $user->id 
                        ? $message->receiver_id 
                        : $message->sender_id;
                })
                ->map(function($messages) use ($user) {
                    $latestMessage = $messages->first();
                    if (!$latestMessage) {
                        return null;
                    }
                    
                    $otherUser = $latestMessage->sender_id === $user->id 
                        ? $latestMessage->receiver 
                        : $latestMessage->sender;
                    
                    if (!$otherUser) {
                        return null;
                    }
                    
                    $unreadCount = $messages->where('receiver_id', $user->id)
                        ->whereNull('read_at')
                        ->count();
                    
                    return [
                        'user' => $otherUser,
                        'latest_message' => $latestMessage,
                        'unread_count' => $unreadCount,
                        'total_messages' => $messages->count(),
                    ];
                })
                ->filter()
                ->sortByDesc(function($conversation) {
                    return $conversation['latest_message']->created_at ?? now();
                })
                ->values();
        }

        // Get all users for sending new messages
        $users = User::where('id', '!=', $user->id)
            ->orderBy('name')
            ->get();

        return view('admin.messaging', compact('conversations', 'users'));
    }

    /**
     * Display conversation with a specific user.
     */
    public function chat($user)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($user);

        // Get all messages between current user and the other user
        $messages = Message::where(function($query) use ($currentUser, $otherUser) {
                $query->where(function($q) use ($currentUser, $otherUser) {
                    $q->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $otherUser->id);
                })->orWhere(function($q) use ($currentUser, $otherUser) {
                    $q->where('sender_id', $otherUser->id)
                      ->where('receiver_id', $currentUser->id);
                });
            })
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark all unread messages as read
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Get all users for sending new messages
        $users = User::where('id', '!=', $currentUser->id)
            ->orderBy('name')
            ->get();

        // Get all conversations for the inbox sidebar
        $sidebarMessages = Message::where(function($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id)
                      ->orWhere('receiver_id', $currentUser->id);
            })
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();
        
        $conversations = collect();
        
        if ($sidebarMessages->isNotEmpty()) {
            $conversations = $sidebarMessages->groupBy(function($message) use ($currentUser) {
                    return $message->sender_id === $currentUser->id 
                        ? $message->receiver_id 
                        : $message->sender_id;
                })
                ->map(function($messages) use ($currentUser) {
                    $latestMessage = $messages->first();
                    if (!$latestMessage) {
                        return null;
                    }
                    
                    $otherUser = $latestMessage->sender_id === $currentUser->id 
                        ? $latestMessage->receiver 
                        : $latestMessage->sender;
                    
                    if (!$otherUser) {
                        return null;
                    }
                    
                    $unreadCount = $messages->where('receiver_id', $currentUser->id)
                        ->whereNull('read_at')
                        ->count();
                    
                    return [
                        'user' => $otherUser,
                        'latest_message' => $latestMessage,
                        'unread_count' => $unreadCount,
                        'total_messages' => $messages->count(),
                    ];
                })
                ->filter()
                ->sortByDesc(function($conversation) {
                    return $conversation['latest_message']->created_at ?? now();
                })
                ->values();
        }

        return view('admin.messaging', compact('messages', 'otherUser', 'conversations', 'users'));
    }

    /**
     * Send a new message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $user = Auth::user();

        // Redact profanity/banned words in subject and message
        $subjectFilter = ContentFilter::redact((string)($request->subject ?? ''));
        $messageFilter = ContentFilter::redact((string)$request->message);

        Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'subject' => $subjectFilter['clean'] ?: null,
            'message' => $messageFilter['clean'],
        ]);

        $notice = 'Message sent successfully.';
        if ($subjectFilter['redacted'] || $messageFilter['redacted']) {
            $notice .= ' Note: Some words were redacted.';
        }

        return redirect()->route('admin.messaging.chat', $request->receiver_id)
            ->with('success', $notice);
    }

    /**
     * Update an existing message (only by its sender).
     */
    public function update(Request $request, Message $message)
    {
        $this->authorizeAction($message);

        $validated = $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $subjectFilter = ContentFilter::redact((string)($validated['subject'] ?? ''));
        $messageFilter = ContentFilter::redact((string)$validated['message']);

        $message->update([
            'subject' => $subjectFilter['clean'] ?: null,
            'message' => $messageFilter['clean'],
        ]);

        $notice = 'Message updated.';
        if ($subjectFilter['redacted'] || $messageFilter['redacted']) {
            $notice .= ' Note: Some words were redacted.';
        }

        $otherUserId = $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
        return redirect()->route('admin.messaging.chat', $otherUserId)->with('success', $notice);
    }

    /**
     * Delete a message (only by its sender).
     */
    public function destroy(Request $request, Message $message)
    {
        $this->authorizeAction($message);
        $otherUserId = $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
        $message->delete();
        return redirect()->route('admin.messaging.chat', $otherUserId)->with('success', 'Message deleted.');
    }

    private function authorizeAction(Message $message): void
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'You are not allowed to modify this message.');
        }
    }
}

