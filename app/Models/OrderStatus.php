<?php

namespace App\Models;

class OrderStatus extends Model
{
	protected $table = 'order_statuses';
	protected $fillable = ['label','alias','sort','default_value'];

	public function getIcon() {
		$icon = '';

		switch ($this->id) {
			case 1:
				$icon = 'fas fa-receipt';
				break;
			
			case 2:
				$icon = 'fas fa-money-bill';
				break;

			case 3:
				$icon = 'fas fa-truck-moving';
				break;

			case 4:
				$icon = 'fas fa-clipboard-check';
				break;

			case 5:
				$icon = 'fas fa-check';
				break;
		}

		return $icon;
	}
}
