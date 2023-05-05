<?php

namespace App\Models;

class Hashtag extends Model
{
  protected $table = 'hashtags';
  protected $fillable = ['hashtag','last_input'];
  public $timestamps  = false;
}