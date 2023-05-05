<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\CheckoutRequest;
use App\Http\Repositories\NotificationRepository;
use App\library\service;
use App\library\snackbar;
use Auth;
use Redirect;

class CheckoutController extends Controller
{
	public function checkoutSubmit(CheckoutRequest $request) {

	  $itemModel = Service::loadModel('Item');
	  $cartModel = Service::loadModel('Cart');

	  $receivers = [];

	  $cart = $cartModel->getItemForCheckout();

	  $_items = [];
	  foreach ($cart['items'] as $item) {

	  	if($cartModel->checkItemError($item['obj'],$item['quantity'])) {
	  		$this->checkRollBack($_items);
	  		Snackbar::modal('เราตรวจพบสินค้าบางรายการไม่สามารถสั่งซื้อได้','โปรดตรวจสอบสินค้าในตระกร้าสินค้าของคุณ','popup-error');
	  		return Redirect::to('/');
	  	}

	  	$item['obj']->decrement('quantity',$item['quantity']);

	  	$_items[] = [
	  		'item' => $item['obj'],
	  		'quantity' => $item['quantity'],
	  	];

	  }
	  
	  $order = Service::loadModel('Order');
	  $order->sub_total = $cart['summaries']['subTotal'];
	  $order->total = $cart['summaries']['total'];
	  $order->vat = $cart['summaries']['vat'];
	  $order->summary = json_encode($cart['summaries']);
	  $order->total_quantity = $cart['quantity'];
	  $order->buyer_name = $request->get('buyer_name');
	  $order->shipping_address = $request->get('shipping_address');
	  $order->order_status_id = 1;
	  $order->save();

	  $orderItem = [];
	  $orderReleteToSeller = [];
	  foreach ($cart['items'] as $item) {

	  	if(!array_key_exists($item['obj']->created_by,$orderReleteToSeller)) {
	  		$orderReleteToSeller[$item['obj']->created_by] = [
	  			'order_id' => $order->id,
	  			'user_id' => $item['obj']->created_by,
	  		];

	  		$receivers[] = $item['obj']->created_by; 
	  	}

	  	$orderItem[] = [
	  		'order_id' => $order->id,
	  		'item_id' => $item['obj']->id,
	  		'buying_price' => $item['obj']->price,
	  		'buying_quantity' => $item['quantity'],
	  		'sub_total' => $item['summaries']['subTotal'],
	  		'vat' => $item['summaries']['vat'],
	  		'summary' => json_encode($item['summaries'])
	  	];
	  }
	  Service::loadModel('OrderItem')->insert($orderItem);

	  // Order total;
	  $orderTotal = [];
	  foreach ($cart['summaries'] as $alias => $summary) {
	  	$orderTotal[] = [
	  		'order_id' => $order->id,
	  		'alias' => $alias,
	  		'value' => $summary,
	  	];
	  }
	  Service::loadModel('OrderTotal')->insert($orderTotal);

	  Service::loadModel('OrderRelateToSeller')->insert($orderReleteToSeller);

	  Service::loadModel('OrderHistory')->create([
	  	'order_id' => $order->id,
	  	'order_status_id' => 1,
	  ]);

	  // remove item out of cart
	  Service::loadModel('Cart')->where('user_id','=',Auth::user()->id)->delete();

	  $_user = [];
	  if($request->has('update_buyer_name') && ($request->get('update_buyer_name') == 1)) {
	  	$_user['name'] = $request->get('buyer_name');
	  }

	  if($request->has('update_shipping_address') && ($request->get('update_shipping_address') == 1)) {
	  	$_user['shipping_address'] = $request->get('shipping_address');
	  }

	  if(!empty($_user)) {
	  	Service::loadModel('User')->find(Auth::user()->id)->update($_user);
	  }

	  // notification
	  foreach ($receivers as $receiver) {
	  	$notificationRepository = new NotificationRepository;
		  $notificationRepository->clientOrderNotification('มีคำสั่งซื้อใหม่ #'.$order->id,$order->id,$receiver);
	  }

  	$notificationRepository = new NotificationRepository;
  	$notificationRepository->notified();
	  $notificationRepository->orderNotification('คำสั่งซื้อสินค้าถูกดำเนินการแล้ว',$order->id,Auth::user()->id);

	  // User log
	  Service::addUserLog('Order',$order->id,'checkout');

	  Snackbar::modal('คำสั่งซื้อสินค้าถูกดำเนินการแล้ว','','popup-success');
	  // Snackbar::message('คำสั่งซื้อสินค้าถูกดำเนินการแล้ว');

	  return Redirect::to('order-detail/'.$order->id);
	}

	private function checkRollBack($items) {
		foreach ($items as $item) {
			$item['obj']->increment('quantity',$item['quantity']);
		}
	}
}
