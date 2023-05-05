<?php

namespace App\Models;

class CategoryPath extends Model
{
  protected $table = 'category_paths';
  protected $fillable = ['category_id','path_id','level'];
  public $timestamps  = false;

  public function category() {
    return $this->hasOne('App\Models\Category','id','category_id');
  }

  public function path() {
    return $this->hasOne('App\Models\Category','id','path_id');
  }
}
