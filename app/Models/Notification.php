<?php

namespace App\Models;

class Notification extends Model
{
  protected $table = 'notifications';
  protected $fillable = ['model','model_id','identity_key','message','description','url','action_link','seen','queued','active_date','receiver_id'];

  public function setUpdatedAt($value) {}
}
