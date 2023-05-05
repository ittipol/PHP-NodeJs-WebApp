<?php

namespace App\Models;

// use Auth;

class OrderRelateToSeller extends Model
{
	protected $table = 'order_relate_to_seller';
	protected $fillable = ['order_id','user_id','order_checked','order_checked_date'];
	public $timestamps  = false;

	public function user() {
	  return $this->hasOne('App\Models\User','id','user_id');
	}

	public function order() {
	  return $this->hasOne('App\Models\Order','id','order_id');
	}

	public function checkOrderShipped() {
		return OrderShippingConfirmation::where([
			['order_id','=',$this->order_id],
			['created_by','=',$this->user_id]
		])->exists();
	}
}
