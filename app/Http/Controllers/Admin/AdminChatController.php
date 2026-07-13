<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    /**
     * Display the admin chat room main page.
     */
    public function index()
    {
        return view('admin.chats.index');
    }

    /**
     * Fetch all conversations list for the admin.
     */
    public function fetchConversations()
    {
        $admin = auth()->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Fetch distinct customers who have sent or received messages
        $messages = Message::where('sender_id', $admin->id)
            ->orWhere('receiver_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $userIds = $messages->map(function ($msg) use ($admin) {
            return $msg->sender_id === $admin->id ? $msg->receiver_id : $msg->sender_id;
        })->unique()->filter(function ($id) use ($admin) {
            return $id !== $admin->id;
        });

        $users = User::whereIn('id', $userIds)->where('role', '!=', 'admin')->get();

        $conversations = $users->map(function ($user) use ($admin) {
            $lastMsg = Message::where(function ($q) use ($admin, $user) {
                $q->where('sender_id', $admin->id)->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($admin, $user) {
                $q->where('sender_id', $user->id)->where('receiver_id', $admin->id);
            })->orderBy('created_at', 'desc')->first();

            $unreadCount = Message::where('sender_id', $user->id)
                ->where('receiver_id', $admin->id)
                ->where('is_read', false)
                ->count();

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'last_message' => $lastMsg ? $lastMsg->message : 'Belum ada pesan',
                'last_message_time' => $lastMsg ? $lastMsg->created_at->diffForHumans() : '',
                'last_message_timestamp' => $lastMsg ? $lastMsg->created_at->timestamp : 0,
                'unread_count' => $unreadCount,
            ];
        })->sortByDesc('last_message_timestamp')->values();

        return response()->json($conversations);
    }

    /**
     * Fetch message history with a specific customer.
     */
    public function fetchMessages($customerId)
    {
        $admin = auth()->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // Mark incoming messages as read
        Message::where('sender_id', $customerId)
            ->where('receiver_id', $admin->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Fetch messages
        $messages = Message::where(function ($query) use ($admin, $customerId) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $customerId);
        })->orWhere(function ($query) use ($admin, $customerId) {
            $query->where('sender_id', $customerId)
                  ->where('receiver_id', $admin->id);
        })->orderBy('created_at', 'asc')->get();

        $formatted = $messages->map(function ($msg) use ($admin) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_sender' => $msg->sender_id === $admin->id,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->format('d M Y'),
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Send message from admin to customer.
     */
    public function sendMessage(Request $request, $customerId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $admin = auth()->user();
        if (!$admin) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $customer = User::find($customerId);
        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        $userOneId = min($admin->id, $customerId);
        $userTwoId = max($admin->id, $customerId);
        $conversation = \App\Models\Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $admin->id,
            'receiver_id' => $customerId,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_sender' => true,
                'time' => $msg->created_at->format('H:i'),
            ]
        ]);
    }
}
