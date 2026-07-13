<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    /**
     * Display the WhatsApp Web style chat main page.
     */
    public function index()
    {
        return view('chats.index');
    }

    /**
     * Fetch all conversations for the authenticated user.
     */
    public function fetchConversations()
    {
        $userId = auth()->id();

        // Get conversations involving the user
        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'lastMessage'])
            ->get();

        $formatted = $conversations->map(function ($conv) use ($userId) {
            $otherUser = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
            
            // Calculate unread count (messages sent by the other user to the logged-in user that are not read)
            $unreadCount = Message::where('conversation_id', $conv->id)
                ->where('sender_id', $otherUser->id)
                ->where('is_read', false)
                ->count();

            $lastMsg = $conv->lastMessage;

            // Simple active/online indicator (just fallback to true/false, in production can link to session/activity)
            $isOnline = $otherUser->role === 'admin'; // For demo, let's treat admin as online

            return [
                'id' => $conv->id,
                'user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'initials' => strtoupper(substr($otherUser->name, 0, 2)),
                    'is_online' => $isOnline
                ],
                'last_message' => $lastMsg ? $lastMsg->message : 'Belum ada pesan',
                'last_message_time' => $lastMsg ? $lastMsg->created_at->diffForHumans() : '',
                'last_message_timestamp' => $lastMsg ? $lastMsg->created_at->timestamp : 0,
                'unread_count' => $unreadCount
            ];
        })->sortByDesc('last_message_timestamp')->values();

        return response()->json($formatted);
    }

    /**
     * Get total unread messages count for the authenticated user.
     */
    public function unreadCount()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }

        $count = Message::where('receiver_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Fetch message history for a specific conversation.
     */
    public function fetchMessages($conversationId)
    {
        $userId = auth()->id();

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
            })
            ->firstOrFail();

        // Mark incoming messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get messages
        $messages = Message::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $messages->map(function ($msg) use ($userId) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_sender' => $msg->sender_id === $userId,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->format('d M Y'),
                'is_read' => $msg->is_read
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Send a message inside a conversation.
     */
    public function sendMessage(Request $request, $conversationId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $userId = auth()->id();

        $conversation = Conversation::where('id', $conversationId)
            ->where(function ($q) use ($userId) {
                $q->where('user_one_id', $userId)
                  ->orWhere('user_two_id', $userId);
            })
            ->firstOrFail();

        $otherUserId = $conversation->user_one_id === $userId 
            ? $conversation->user_two_id 
            : $conversation->user_one_id;

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'receiver_id' => $otherUserId,
            'message' => $request->message,
            'is_read' => false
        ]);

        // Touch the conversation to update its updated_at column (triggers re-sorting in conversations list)
        $conversation->touch();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_sender' => true,
                'time' => $msg->created_at->format('H:i'),
                'is_read' => $msg->is_read
            ]
        ]);
    }

    /**
     * Start conversation with a seller from detail page.
     */
    public function startConversation($sellerId)
    {
        $userId = auth()->id();

        if ($userId == $sellerId) {
            return redirect()->route('chats.index')->with('warning', 'Anda tidak dapat memulai obrolan dengan diri sendiri.');
        }

        $userOneId = min($userId, $sellerId);
        $userTwoId = max($userId, $sellerId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId
        ]);

        return redirect()->route('chats.index', ['select' => $conversation->id]);
    }

    /**
     * Search users by name or email to start a new chat.
     */
    public function searchUsers(Request $request)
    {
        $query = $request->query('query');
        if (!$query) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        $formatted = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'initials' => strtoupper(substr($user->name, 0, 2))
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Create conversation with search-selected user.
     */
    public function createConversationWithUser($userId)
    {
        $currentUserId = auth()->id();

        if ($currentUserId == $userId) {
            return response()->json(['error' => 'Self chat not allowed'], 400);
        }

        $userOneId = min($currentUserId, $userId);
        $userTwoId = max($currentUserId, $userId);

        $conversation = Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id
        ]);
    }
}
