<?php

namespace App\Models;

class PageViewingHistory extends Model
{
  protected $table = 'page_viewing_histories';
  protected $fillable = ['model','model_id','token','page_id','user_id'];
  
  public function setUpdatedAt($value) {}

  public function page() {
    return $this->hasOne('App\Models\Page','id','page_id');
  }
}
