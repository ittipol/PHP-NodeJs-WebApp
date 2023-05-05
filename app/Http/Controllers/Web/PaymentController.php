<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\library\service;
use App\library\snackbar;
// use App\library\date;
use Redirect;
use Auth;

class PaymentController extends Controller
{
	public function payment($orderId) {

		if(Service::loadModel('OrderPayment')->where([
			['order_id','=',$orderId],
			['created_by','=',Auth::user()->id]
		])->exists()) {
			Snackbar::message('คุณได้ชำระเงิน คำสั่งซื้อ #'.$orderId.' แล้ว');
			return Redirect::to('/order-detail/'.$orderId);
		}

		$order = Service::loadModel('Order')->where([
			['id','=',$orderId],
			['created_by','=',Auth::user()->id]
		])->first();

		if(empty($order)) {
		  Snackbar::message('ไม่พบคำสั่งซื้อ');
		  return Redirect::to('/');
		}

		$this->setData('order',$order->buildDataDetail());

		return $this->view('pages.payment.form.payment');
	}

	public function paymentSubmit(Request $request, $orderId) {

		if(Service::loadModel('OrderPayment')->where([
			['order_id','=',$orderId],
			['created_by','=',Auth::user()->id]
		])->exists()) {
			Snackbar::message('คุณได้ชำระเงิน คำสั่งซื้อ #'.$orderId.' แล้ว');
			return Redirect::to('/order-detail/'.$orderId);
		}

		$order = Service::loadModel('Order')->where([
			['id','=',$orderId],
			['created_by','=',Auth::user()->id]
		])->first();

		if(empty($order)) {
		  Snackbar::message('ไม่พบคำสั่งซื้อ');
		  return Redirect::to('/');
		}

		$model = Service::loadModel('OrderPayment');
		$model->order_id = $orderId;
		$model->amount = 1000;
		$model->payment_method = 1;
		// $model->amount = $request->get('amount');
		// $model->payment_method = $request->get('payment_method');
		// $model->remark = $request->get('remark');
		$model->payment_date = date('Y-m-d H:i:s');
		$model->save();

		$order->update([
			'order_status_id' => 2
		]);

		Service::loadModel('OrderHistory')->create([
			'order_id' => $orderId,
			'order_status_id' => 2,
		]);

		// Check All sellers have confirmed shipping
		// call function orderShippingConfirmed()
		// if all seller comfirmed
		// then update order status id = 3
		$order->orderShippingConfirmed();

		Service::addUserLog('OrderPayment',$model->id,'payment');

		Snackbar::modal('การชำระเงินเสร็จสิ้น','','popup-success');

		return Redirect::to('order-detail/'.$orderId);
	}
}
