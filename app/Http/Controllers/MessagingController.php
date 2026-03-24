<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ContentFilter;

class MessagingController extends Controller
{
    private const CHAT_MESSAGE_LIMIT = 150;
    private const SIDEBAR_SCAN_LIMIT = 600;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the inbox with all conversations.
     */
    public function index()
    {
        $user = Auth::user();
        $conversations = $this->buildConversations($user->id);

        // Get all users for sending new messages
        $users = User::where('id', '!=', $user->id)
            ->select('id', 'name', 'photo_url')
            ->orderBy('name')
            ->get();

        return view('messaging.index', compact('conversations', 'users'));
    }

    /**
     * Display conversation with a specific user.
     */
    public function chat($user)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($user);

        // Load only the newest chunk to keep response time fast on Cloud.
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
            ->latest('created_at')
            ->limit(self::CHAT_MESSAGE_LIMIT)
            ->get();
        $messages = $messages->sortBy('created_at')->values();

        // Mark all unread messages as read
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Get all users for sending new messages
        $users = User::where('id', '!=', $currentUser->id)
            ->select('id', 'name', 'photo_url')
            ->orderBy('name')
            ->get();

        $conversations = $this->buildConversations($currentUser->id);

        return view('messaging.index', compact('messages', 'otherUser', 'conversations', 'users'));
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

        return redirect()->route('messaging.chat', $request->receiver_id)
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
        return redirect()->route('messaging.chat', $otherUserId)->with('success', $notice);
    }

    /**
     * Delete a message (only by its sender).
     */
    public function destroy(Request $request, Message $message)
    {
        $this->authorizeAction($message);
        $otherUserId = $message->sender_id === auth()->id() ? $message->receiver_id : $message->sender_id;
        $message->delete();
        return redirect()->route('messaging.chat', $otherUserId)->with('success', 'Message deleted.');
    }

    private function authorizeAction(Message $message): void
    {
        if ($message->sender_id !== auth()->id()) {
            abort(403, 'You are not allowed to modify this message.');
        }
    }

    private function buildConversations(int $userId)
    {
        $messages = Message::where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->with(['sender', 'receiver'])
            ->latest('created_at')
            ->limit(self::SIDEBAR_SCAN_LIMIT)
            ->get();

        if ($messages->isEmpty()) {
            return collect();
        }

        return $messages->groupBy(function ($message) use ($userId) {
                return $message->sender_id === $userId ? $message->receiver_id : $message->sender_id;
            })
            ->map(function ($messages) use ($userId) {
                $latestMessage = $messages->first();
                if (!$latestMessage) {
                    return null;
                }

                $otherUser = $latestMessage->sender_id === $userId ? $latestMessage->receiver : $latestMessage->sender;
                if (!$otherUser) {
                    return null;
                }

                return [
                    'user' => $otherUser,
                    'latest_message' => $latestMessage,
                    'unread_count' => $messages->where('receiver_id', $userId)->whereNull('read_at')->count(),
                    'total_messages' => $messages->count(),
                ];
            })
            ->filter()
            ->sortByDesc(fn ($conversation) => $conversation['latest_message']->created_at ?? now())
            ->values();
    }
}

