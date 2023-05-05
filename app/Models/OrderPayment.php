<?php

namespace App\Models;

use App\library\currency;
use App\library\date;

class OrderPayment extends Model
{
	protected $table = 'order_payments';
	protected $fillable = ['order_id','amount','payment_date','payment_method','remark','created_by'];

	public function setUpdatedAt($value) {}

	public function buildDataDetail() {
		return [
		  'id' => $this->id,
		  'amount' => Currency::format($this->amount),
		  'paymentDate' => $this->dateRepo->covertDateTimeToSting($this->payment_date),
		  'paymentMethod' => $this->payment_method,
		];
	}
}
