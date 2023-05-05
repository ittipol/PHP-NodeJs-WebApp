<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auth\User as Authenticatable;

class Admin extends Authenticatable {

	use HasApiTokens, Notifiable;

	protected $fillable = ['email', 'first_name', 'last_name', 'password', 'level', 'customer_id'];

	protected $hidden = [
		'password', 'remember_token',
	];

}
