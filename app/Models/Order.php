<?php

namespace App\Models;

use App\library\service;
use App\library\format;
use App\library\currency;
use App\library\date;

class Order extends Model
{
  protected $table = 'orders';
  protected $fillable = ['slug','sub_total','total','vat','summary','total_quantity','buyer_name','shipping_address','order_status_id','created_by'];

  public function orderItem() {
    return $this->hasMany('App\Models\OrderItem','order_id','id');
  }

  public function orderStatus() {
    return $this->hasOne('App\Models\OrderStatus','id','order_status_id');
  }

  public function user() {
    return $this->hasOne('App\Models\User','id','created_by');
  }

  public function buildDataDetail() {

    return [
      'id' => $this->id,
      'summary' => $this->getOrderSummary(),
      'total_quantity' => $this->total_quantity,
      'buyer_name' => $this->buyer_name,
      'shipping_address' => $this->shipping_address,
      'total' => Currency::format($this->total),
      'order_status' => $this->order_status_id
    ];
  }

  public function orderStatusProgress() {
    // 
    // 1 = 0%
    // 2 = 25%
    // 3 = 50%
    // 4 = 75%
    // 5 = 100%

    return (($this->order_status_id * 25) - 25).'%';
  }

  public function orderStatusTimeline() {

    $orderStatuses = OrderStatus::select('id','label')->where('default_value','=',1)->get();

    $orderHistories = OrderHistory::select('order_status_id','created_at')->where('order_id','=',$this->id)->get();

    $_orderStatuses = [];
    foreach ($orderHistories as $orderHistory) {
      $_orderStatuses[$orderHistory->order_status_id] = $orderHistory->created_at;
    }

    $orderStatusTimelines = [];
    foreach ($orderStatuses as $orderStatus) {

      switch ($orderStatus->id) {
        // case 2:
        //   $label = $orderStatus->label.' ('.Currency::format($this->total).')';
        //   break;
        
        case 4:

          $orderRelateToSellers = OrderRelateToSeller::select('order_checked')->where('order_id','=',$this->id);

          $_count = 0;
          foreach ($orderRelateToSellers->get() as $orderRelateToSeller) {
            if($orderRelateToSeller->order_checked) {
              ++$_count;
            }
          }

          $label = $orderStatus->label.'(ตรวจสอบแล้ว '.$_count.'/'.$orderRelateToSellers->count().' ผู้ขาย)';
          break;

        default:
          $label = $orderStatus->label;
          break;
      }

      $orderStatusTimelines[] = [
        'label' => $label,
        'succeeded' => ($this->order_status_id >= $orderStatus->id),
        'icon' => $orderStatus->getIcon(),
        'date' => !empty($_orderStatuses[$orderStatus->id]) ? $this->dateRepo->covertDateTimeToSting($_orderStatuses[$orderStatus->id]) : ''
      ];
    }

    return $orderStatusTimelines;
  }

  public function buildDataList() {

    $item = $this->orderItem->first()->item;

    return [
      'id' => $this->id,
      'quantity' => $this->total_quantity,
      'orderItemAmount' => $this->orderItem->count(),
      'total' => Currency::format($this->total),
      'images' => $item->getItemImage('xsm_scale'),
      'orderStatusLabel' => $this->orderStatus->label
    ];

  }

  public function getOrderSummary() {
    
    $summaries = [];
    foreach (json_decode($this->summary,true) as $alias => $value) {
      $summaries[] =[
        'value' => Currency::format($value),
        'title' => Service::getSummaryTitle($alias),
        'class' => Service::getSummaryClass($alias)
      ];
    }

    return $summaries;
  }

  public function orderShippingConfirmed() {
    
    $orderRelateToSellers = OrderRelateToSeller::select('user_id')->where('order_id','=',$this->id);

    $total = 0;

    foreach ($orderRelateToSellers->get() as $orderRelateToSeller) {
      
      $exist = OrderShippingConfirmation::where([
        ['order_id','=',$this->id],
        ['created_by','=',$orderRelateToSeller->user_id]
      ])->exists();

      if($exist) {
        $total++;
      }

    }

    if($total != $orderRelateToSellers->count()) {
      return false;
    }

    $this->update([
      'order_status_id' => 3
    ]);

    OrderHistory::create([
      'order_id' => $this->id,
      'order_status_id' => 3,
    ]);

    return true;
  }

  public function orderItemReceived() {

    $orderRelateToSellers = OrderRelateToSeller::select('order_checked')->where('order_id','=',$this->id);

    $total = 0;

    foreach ($orderRelateToSellers->get() as $orderRelateToSeller) {

      if($orderRelateToSeller->order_checked) {
        $total++;
      }

    }

    if($total != $orderRelateToSellers->count()) {
      return false;
    }

    $this->update([
      'order_status_id' => 4
    ]);

    OrderHistory::create([
      'order_id' => $this->id,
      'order_status_id' => 4,
    ]);

    return true;
  }

}
