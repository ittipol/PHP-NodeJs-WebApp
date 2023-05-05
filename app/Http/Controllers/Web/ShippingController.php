<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ShippingConfirmationRequest;
use Illuminate\Http\Request;
use App\library\service;
use App\library\snackbar;
// use App\library\date;
use Redirect;
use Auth;

class ShippingController extends Controller
{
	public function shippingConfirmation(ShippingConfirmationRequest $request,$orderId) {

	  $model = Service::loadModel('OrderRelateToSeller')->select('order_relate_to_seller.*')
	  ->join('orders','orders.id','=','order_relate_to_seller.order_id')
	  ->where([
	    ['order_relate_to_seller.user_id','=',Auth::user()->id],
	    ['order_relate_to_seller.order_id','=',$orderId],
	  ])
	  ->exists();

	  if(!$model) {
	    return $this->error('ไม่พบคำสั่งซื้อ');
	  }

	  $model = Service::loadModel('OrderShippingConfirmation')->where([
	    ['order_id','=',$orderId],
	    ['created_by','=',Auth::user()->id]
	  ])->exists();

	  if($model) {
	    return $this->error('ยืนยันการจัดส่งสินค้าเสร็จสมบูรณ์แล้ว');
	  }

	  $model = Service::loadModel('OrderShippingConfirmation');
	  $model->order_id = $orderId;
	  $model->detail = $request->get('shipping_detail'); 

	  if(!$model->save()) {
	    return Redirect::to('client-order-detail/'.$orderId);
	  }

	  if($request->has('Image')) {
	    Service::loadModel('Image')->__saveRelatedData($model,$request->get('Image'));
	  }

	  Service::addUserLog('OrderShippingConfirmation',$model->id,'confirmation');

	  Snackbar::modal('ยืนยันการจัดส่งสินค้าเสร็จสมบูรณ์','','popup-success');
	  
	  return Redirect::to('client-order-detail/'.$orderId);
	}

	public function shippingDetail($orderId,$userId) {

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

	  $html = view('pages.order._shipping_detail',array(
	    'data' => $data,
	    'orderItems' => $_items,
	    'hasData' => $hasData,
	  ))->render();

	  $result = array(
	    'html' => $html
	  );

	  return response()->json($result);
	}
}
