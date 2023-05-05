<?php

namespace App\Http\Controllers\Web;

use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\library\service;
use App\library\snackbar;
use App\library\date;
use Redirect;
use Auth;

class OrderController extends Controller
{
  public function __construct() {
    $this->botDisallowed();
  }

	public function myOrderListView(Request $request) {

    $searching = false;

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }

    Paginator::currentPageResolver(function() use ($currentPage) {
      return $currentPage;
    });

    $model = Service::loadModel('Order')->query();

    if(!empty($request->get('order_status'))) {

      $searching = true;
      
      $model->where(function($query) use ($request) {
        foreach ($request->get('order_status') as $orderStatus) {
          $query->orWhere('orders.order_status_id','=',$orderStatus);
        }
      });
    }

    $model->where('created_by','=',Auth::user()->id)->orderBy('id', 'desc');

    $this->setPagination($model,24);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    $this->setData('orderStatuses',Service::loadModel('OrderStatus')->select('id','label')->where('default_value','=',1)->get());
    $this->setData('searching',$searching);

    $this->setMeta('title','รายการสั่งซื้อสินค้าของฉัน | Ticket Easys');

    return $this->view('pages.order.my-order-list');
	}

  public function myOrderDetail($orderId) {

  	$model = Service::loadModel('Order')->where([
  		['id','=',$orderId],
  		['created_by','=',Auth::user()->id]
  	])->first();

  	if(empty($model)) {
  	  return $this->error('ไม่พบคำสั่งซื้อ');
  	}

    // Get name of seller
    $sellers = Service::loadModel('OrderRelateToSeller')->where('order_id','=',$orderId)->get();

    $_sellers = [];
    foreach ($sellers as $seller) {

      $orderItems = Service::loadModel('OrderItem')->select('order_items.*')
      ->join('items','items.id','=','order_items.item_id')
      ->where([
        ['order_items.order_id','=',$orderId],
        ['items.created_by','=',$seller->user->id],
      ])->get();

      $_orderItems = [];
      foreach ($orderItems as $item) {
        $_orderItems[] = $item->buildDataList();
      }

      $_sellers[] = [
        'id' => $seller->user->id,
        'name' => $seller->user->getUserOrShopName(),
        'items' => $_orderItems,
        'orderchecked' => $seller->order_checked,
        'ordercheckedDate' => !empty($seller->order_checked_date) ? $this->dateRepo->covertDateTimeToSting($seller->order_checked_date) : '',
        'orderShipped' => $seller->checkOrderShipped()
      ];
    }

    // Order payment detail
    $orderPayment = Service::loadModel('OrderPayment')->where([
      ['order_id','=',$orderId],
      ['created_by','=',Auth::user()->id]
    ]);

  	$this->setData('data',$model->buildDataDetail());
  	// $this->setData('orderItems',$_orderItems);
    $this->setData('timelines',$model->orderStatusTimeline());
    $this->setData('percent',$model->orderStatusProgress());
    $this->setData('sellers',$_sellers);

    if($orderPayment->exists()) {
      $this->setData('orderPayment',$orderPayment->first()->buildDataDetail());
    }
    $this->setData('paid',$orderPayment->exists());
    

  	$this->setMeta('title','คำสั่งซื้อหมายเลข '.$model->id . ' | Ticket Easys');

  	return $this->view('pages.order.my-order-detail');
  }

  public function clientOrderListView(Request $request) {

    $searching = false;

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }

    Paginator::currentPageResolver(function() use ($currentPage) {
      return $currentPage;
    });

    $model = Service::loadModel('OrderRelateToSeller')->query();

    if(!empty($request->get('order_status'))) {

      $searching = true;

      $model->where(function($query) use ($request) {
        foreach ($request->get('order_status') as $orderStatus) {
          $query->orWhere('orders.order_status_id','=',$orderStatus);
        }
      });
    }

    $model->select('order_relate_to_seller.*')
    ->join('orders','orders.id','=','order_relate_to_seller.order_id')
    ->where('order_relate_to_seller.user_id','=',Auth::user()->id)
    ->orderBy('id', 'desc');

    $this->setPagination($model,24);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    $this->setData('orderStatuses',Service::loadModel('OrderStatus')->select('id','label')->where('default_value','=',1)->get());
    $this->setData('searching',$searching);

    $this->setMeta('title','คำสั่งซื้อจากลูกค้า | Ticket Easys');

    return $this->view('pages.order.client-order-list');
  }

  public function clientOrderDetail($orderId) {

    $model = Service::loadModel('OrderRelateToSeller')->select('order_relate_to_seller.*')
    ->join('orders','orders.id','=','order_relate_to_seller.order_id')
    ->where([
      ['order_relate_to_seller.user_id','=',Auth::user()->id],
      ['order_relate_to_seller.order_id','=',$orderId],
    ])
    ->first();

    if(empty($model)) {
      return $this->error('ไม่พบคำสั่งซื้อ');
    }

    // get order item with related item with seller
    $orderItems = Service::loadModel('OrderItem')->select('order_items.*')
    ->join('items','items.id','=','order_items.item_id')
    ->where([
      ['order_items.order_id','=',$orderId],
      ['items.created_by','=',Auth::user()->id],
    ])->get();

    $_items = [];
    foreach ($orderItems as $item) {
      $_items[] = $item->buildDataList();
    }

    $shippingConfirmed = Service::loadModel('OrderShippingConfirmation')->where([
      ['order_id','=',$orderId],
      ['created_by','=',Auth::user()->id]
    ])->exists();

    $order = $model->order;

    // Order payment detail
    $orderPayment = Service::loadModel('OrderPayment')->where([
      ['order_id','=',$orderId],
      ['created_by','=',$order->created_by]
    ]);

    $this->setData('order',$order);
    $this->setData('orderItems',$_items);
    $this->setData('orderSummary',$order->getOrderSummary());
    $this->setData('timelines',$order->orderStatusTimeline());
    $this->setData('percent',$order->orderStatusProgress());
    // $this->setData('client',$model->order->user);
    $this->setData('income',Service::loadModel('OrderItem')->getSellerIncome(Auth::user()->id,$orderId));
    $this->setData('shippingConfirmed',$shippingConfirmed);

    // if($orderPayment->exists()) {
    //   $this->setData('orderPayment',$orderPayment->first()->buildDataDetail());
    // }
    $this->setData('paid',$orderPayment->exists());

    return $this->view('pages.order.client-order-detail');
  }

  public function itemReceivingDetail($orderId,$userId) {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $model = Service::loadModel('OrderRelateToSeller')->select('order_relate_to_seller.*')
    ->join('orders','orders.id','=','order_relate_to_seller.order_id')
    ->where([
      ['order_relate_to_seller.user_id','=',$userId],
      ['order_relate_to_seller.order_id','=',$orderId],
    ])
    ->first();

    if(empty($model)) {
      return []; // ไม่พบคำสั่งซื้อ
    }

    $model = Service::loadModel('OrderShippingConfirmation')->where([
      ['order_id','=',$orderId],
      ['created_by','=',$userId]
    ]);

    $hasData = false;
    $data = [];
    if($model->exists()) {
      $hasData = true;
      $data = $model->first()->buildDataDetail();
    }

    // get order item with related item with seller
    $items = Service::loadModel('OrderItem')->select('order_items.*')
    ->join('items','items.id','=','order_items.item_id')
    ->where([
      ['order_items.order_id','=',$orderId],
      ['items.created_by','=',$userId],
    ])->get();

    $_items = [];
    foreach ($items as $item) {
      $_items[] = $item->buildDataList();
    }

    $html = view('pages.order._item_receiving_detail',array(
      'data' => $data,
      'orderItems' => $_items,
      'hasData' => $hasData,
      'orderId' => $orderId,
      'userId' => $userId
    ))->render();

    $result = array(
      'html' => $html
    );

    return response()->json($result);
  }

  public function itemReceivingSummit(Request $request) {

    $model = Service::loadModel('OrderRelateToSeller')->where([
      ['order_id','=',$request->order_id],
      ['user_id','=',$request->user_id],
    ]);

    if(!$model->exists() || $model->first()->order_checked) {
      return $this->error('ไม่สามารถยืนยันการรับสินค้าได้ หรือ ได้ยืนยันการรับสินค้าเสร็จสมบูรณ์แล้ว');
    }

    $orderShippingConfirmation = Service::loadModel('OrderShippingConfirmation')->where([
      ['order_id','=',$request->order_id],
      ['created_by','=',$request->user_id]
    ]);

    if(!$orderShippingConfirmation->exists()) {
      return $this->error('ไม่สามารถยืนยันการรับสินค้าได้');
    }

    $model->update([
      'order_checked' => 1,
      'order_checked_date' => date('Y-m-d H:i:s')
    ]);

    $model->first()->order->orderItemReceived();

    Service::addUserLog('Order',$request->order_id,'Item receiving confirmation',[
      'model' => 'OrderRelateToSeller',
      'ids' => [
        'order_id' => $request->order_id,
        'user_id' => $request->user_id
      ]
    ]);

    Snackbar::modal('ยืนยันการรับสินค้าเสร็จสมบูรณ์','คุณได้ยืนยันการรับสินค้าจากผู้ขาย '.$model->first()->user->getUserOrShopName().' เสร็จสมบูรณ์แล้ว','popup-success');
    
    return Redirect::to('order-detail/'.$request->order_id);
  }

  // public function hasOrder() {

  //   if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
  //     return response('', 500)->header('Content-Type', 'text/plain');
  //   }

  //   $orderExist = Service::loadModel('Order')->where('created_by','=',Auth::user()->id)
  //     ->whereBetween('order_status_id',[1,5])
  //     ->exists();

  //   $clientOrderExist = Service::loadModel('OrderRelateToSeller')->select('order_relate_to_seller.*')
  //     ->join('orders','orders.id','=','order_relate_to_seller.order_id')
  //     ->where('order_relate_to_seller.user_id','=',Auth::user()->id)
  //     ->whereBetween('orders.order_status_id',[1,5])
  //     ->exists();

  //   $hasOrder = false;
  //   if($orderExist || $clientOrderExist) {
  //     $hasOrder = true;
  //   }

  //   return [
  //     'has' => $hasOrder
  //   ];

  // }

}
