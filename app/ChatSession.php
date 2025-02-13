<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    protected $fillable = ['session_id', 'title'];

    public function histories() {
        return $this->hasMany(ChatHistory::class, 'session_id', 'session_id');
    }
}
