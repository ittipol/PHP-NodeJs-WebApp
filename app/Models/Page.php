<?php

namespace App\Models;

class Page extends Model
{
	protected $table = 'pages';
	protected $fillable = ['name'];
	public $timestamps  = false;
}
