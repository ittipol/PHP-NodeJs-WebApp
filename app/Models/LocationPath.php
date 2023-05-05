<?php

namespace App\Models;

class LocationPath extends Model
{
  protected $table = 'location_paths';
  protected $fillable = ['location_id','path_id','level'];
  public $timestamps  = false;

  public function category() {
    return $this->hasOne('App\Models\Location','id','location_id');
  }

  public function path() {
    return $this->hasOne('App\Models\Location','id','path_id');
  }
}
