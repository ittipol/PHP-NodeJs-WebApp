<?php

namespace App\Models;

class OrderHistory extends Model
{
	protected $table = 'order_histories';
	protected $fillable = ['order_id','order_status_id','description'];

	public function setUpdatedAt($value) {}
}
