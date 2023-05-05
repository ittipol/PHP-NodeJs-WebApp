<?php

namespace App\Models;

class ChatRoom extends Model
{
  protected $table = 'chat_rooms';
  protected $fillable = ['model','model_id','room_key','active'];
}
