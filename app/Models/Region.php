<?php

namespace App\Models;

class Region extends Model
{
  protected $table = 'regions';
  protected $fillable = ['name'];
  public $timestamps  = false;
}
