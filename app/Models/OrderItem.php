<?php

namespace App\Models;

use App\library\currency;
// use App\library\format;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = ['order_id','item_id','buying_price','buying_quantity','sub_total','vat','summary'];
    public $timestamps  = false;

    public function item() {
      return $this->hasOne('App\Models\Item','id','item_id');
    }

    public function buildDataList() {

    	$item = Item::select('id','title')->find($this->item_id);

    	return [
    		'itemId' => $this->item_id,
    		'name' => $item->title,
    		'image' => $item->getItemImage('xsm_scale'),
    		'price' => Currency::format($this->buying_price),
    		'quantity' => $this->buying_quantity,
    		'subTotal' => Currency::format($this->sub_total),
    		'vat' => Currency::format($this->vat)
    	];

    }

    public function getSellerIncome($seller,$orderId,$format = true) {
      $items = $this->select('order_items.sub_total')
      ->join('items','items.id','=','order_items.item_id')
      ->where([
        ['order_items.order_id','=',$orderId],
        ['items.created_by','=',$seller],
      ])->get();

      $income = 0;
      foreach ($items as $item) {
        $income += $item->sub_total;
      }
  
      if($format) {
        return Currency::format($income);
      }

      return $income;
    }
}
