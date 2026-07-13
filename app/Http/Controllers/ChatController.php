<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Fetch message history between logged in customer and admin.
     */
    public function fetchMessages()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json([]);
        }

        // Fetch conversation
        $messages = Message::where(function ($query) use ($user, $admin) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $admin->id);
        })->orWhere(function ($query) use ($user, $admin) {
            $query->where('sender_id', $admin->id)
                  ->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();

        // Mark incoming messages as read
        Message::where('sender_id', $admin->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Map messages for easier rendering
        $formatted = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'is_sender' => $msg->sender_id === $user->id,
                'time' => $msg->created_at->format('H:i'),
                'date' => $msg->created_at->format('d M Y'),
                'is_read' => $msg->is_read,
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Send a message to the admin.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json(['error' => 'Admin not available at this moment'], 404);
        }

        $userOneId = min($user->id, $admin->id);
        $userTwoId = max($user->id, $admin->id);
        $conversation = \App\Models\Conversation::firstOrCreate([
            'user_one_id' => $userOneId,
            'user_two_id' => $userTwoId
        ]);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $admin->id,
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
                'is_read' => $msg->is_read,
            ]
        ]);
    }
}
