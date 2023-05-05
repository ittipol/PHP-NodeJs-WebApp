<?php

namespace App\Models;

class UserOpenItemContact extends Model
{
	protected $table = 'user_open_item_contacts';
	protected $fillable = ['item_id','user_id','ip_address','token'];

	public function setUpdatedAt($value) {}
}
