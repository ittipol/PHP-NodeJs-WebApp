<?php

namespace App\Models;

class OrderTotal extends Model
{
  protected $table = 'order_totals';
  protected $fillable = ['order_id','alias','value'];
  public $timestamps  = false; 
}
