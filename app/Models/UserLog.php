<?php

namespace App\Models;

class UserLog extends Model
{
  protected $table = 'user_logs';
  protected $fillable = ['model','model_id','related_model','related_ids','action','description','ip_address','user_id'];

  public function setUpdatedAt($value) {}
}
