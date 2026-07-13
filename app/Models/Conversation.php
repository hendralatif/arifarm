<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_one_id',
        'user_two_id'
    ];

    public function userOne()
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    public function userTwo()
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->orderBy('id', 'desc');
    }

    /**
     * Get the other user participating in this conversation.
     */
    public function getOtherParticipantAttribute()
    {
        $currentUserId = auth()->id();
        
        if ($this->user_one_id === $currentUserId) {
            return $this->userTwo;
        }
        
        return $this->userOne;
    }
}
