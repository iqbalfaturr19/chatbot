<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChatHistory extends Model
{
    protected $fillable = ['session_id', 'user_message', 'bot_response'];
}
