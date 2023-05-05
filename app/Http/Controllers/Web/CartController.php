<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\library\service;
use Auth;

class CartController extends Controller
{
  private function quantityValidation($quantity) {
    return is_numeric($quantity) && !is_float(100) && (($quantity > 0) && ($quantity <= 1000000)); 
  }

  public function getItemInCart() {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    return [
      'items' => Service::loadModel('Cart')->getItemInCart()
    ];
  }

  public function renderItemInCart() {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $html = view('shared.cart-item-list',array(
      'items' => Service::loadModel('Cart')->getItemInCart(),
    ))->render();

    return [
      'html' => $html
    ];    
  }

  public function addToCart(Request $request,$itemId) {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    if(!$request->has('qty')) {
      return [
        'error' => true,
        'message' => 'Item quantity is invalid'
      ];
    }

    if(!$this->quantityValidation($request->get('qty'))) {
      return [
        'error' => true,
        'message' => 'Invalid'
      ];
    }

    // Get Item qty
    $item = Service::loadModel('Item')->select('id','quantity')
    ->where([
      ['id','=',$itemId],
      ['deleted','=',0]
    ])->first();

    return Service::loadModel('Cart')->addOrUpdateItem($itemId,$request->get('qty'),$item->quantity);    
  }

  public function updateToCart(Request $request,$itemId) {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    if(!$request->has('qty')) {
      return [
        'error' => true,
        'message' => 'Item quantity is invalid'
      ];
    }

    if(!$this->quantityValidation($request->get('qty'))) {
      return [
        'error' => true,
        'message' => 'Invalid'
      ];
    }

    return Service::loadModel('Cart')->addOrUpdateItem($itemId,$request->get('qty'));
  }

  public function deleteToCart(Request $request) {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $cartModel = Service::loadModel('Cart');

    if(Auth::check()) {
      $cartModel->where([
        ['token','=',$request->get('token')],
        ['user_id','=',Auth::user()->id]
      ])->delete();
    }else {

      if(session()->has($request->get('token'))) {
        session()->forget($request->get('token'));
      }

    }

    return [
      'deleted' => true,
      'empty' => $cartModel->getItemAmount() > 0 ? false : true 
    ];
  }

  public function getInfo() {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    return Service::loadModel('Cart')->getSummary();    
  }

  public function checkout(Request $request) {
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $cartModel = Service::loadModel('Cart');

    $buyer = [];
    if(Auth::check()) {
      $buyer = [
        'name' => Auth::user()->name,
        'shippingAddress' => Auth::user()->shipping_address
      ];
    }

    $html = view('shared.modal-cart-checkout',array(
      'items' => $cartModel->getItemInCart(),
      'info' => $cartModel->getSummary(),
      'buyer' => $buyer
      // 'coin' => 0
    ))->render();

    return [
      'html' => $html
    ];

  }

}
