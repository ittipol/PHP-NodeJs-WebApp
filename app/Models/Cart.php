<?php

namespace App\Models;

use App\library\service;
use App\library\currency;
use App\library\token;
use App\library\cache;
use Auth;

class Cart extends Model
{
	protected $table = 'carts';
	protected $fillable = ['item_id','user_id','quantity','shop_id','token'];

	private $vatRate = 0.07;

	// private $summaryFxList = array(
	// 	'subTotal' => [
	// 		'cart' => 'getCartSubTotal',
	// 		'item' => 'getItemSubTotal',
	// 		// 'format' => true
	// 	],
	// 	// 'shipping' => 'getCartShippingCost',
	// 	// 'saving' => 'getCartSavingPrice',
	// 	'vat' => [
	// 		'cart' => 'getCartVat', // 7%
	// 		'item' => 'getItemVat',
	// 		// 'format' => true
	// 	],
	// 	// 'fee' => [
	// 	// 	'name' => 'getCartFee', //  รูดบัตร + อีก 3%
	// 	// 	'format' => true
	// 	// ],
	// 	'total' => [
	// 		'cart' => 'getCartTotal',
	// 		'item' => 'getItemTotal',
	// 		// 'format' => true
	// 	]
	// );

	private $totalFxList = [
		'getItemSubTotal',
		'getItemVat'
	];

	private $summaryFxList = [
		'subTotal' => [
			'name' => 'getItemSubTotal',
		],
		'vat' => [
			'name' => 'getItemVat',
		],
		'total' => [
			'name' => 'getItemTotal',
		]
	];

	public function item() {
	  return $this->hasOne('App\Models\Item','id','item_id');
	}

	public function addItem($itemId,$quantity) {
		$cart = $this->newInstance();
		$cart->item_id = $itemId;
		$cart->user_id = Auth::user()->id;
		$cart->quantity = $quantity;
		$cart->token = Token::generateSecureKey(64);
		$cart->save();
	}

	public function addOrUpdateItem($itemId,$quantity) {

		// Get Item qty
		$item = Item::select('id','quantity','date_1','date_2','date_type')
		->where([
		  ['id','=',$itemId],
		  ['deleted','=',0]
		])->first();

		$errors = $this->checkItemError($item,$quantity,true);

		if(!empty($errors)) {
		  return [
		    'error' => true,
		    'messages' => $errors
		  ];
		}

		if(Auth::check()) {

		  $cart = $this->where([
		    ['item_id','=',$itemId],
		    ['user_id','=',Auth::user()->id],
		  ])
		  ->select('item_id');

		  if($cart->exists()) {

		  	$cart = $cart->update([
		  		'quantity' => $quantity
		  	]);

		  	// if($remainingItem < ($cart->quantity + $quantity)) {
		  	// 	return [
		  	// 	  'error' => true,
		  	// 	  'message' => 'Not enough item'
		  	// 	];
		  	// }

		    // $cart->increment('quantity', $quantity);
		  }else {
		    $this->addItem($itemId,$quantity);
		  }

		}else {

		  if(session()->has('cart.'.$itemId)) {

		    $item = session()->get('cart.'.$itemId);
		    $item['quantity'] = $quantity;
		    // $item['quantity'] += $quantity;

		    // if($remainingItem < $item['quantity']) {
		    // 	return [
		    // 	  'error' => true,
		    // 	  'message' => 'Not enough item'
		    // 	];
		    // }

		    session()->put('cart.'.$itemId,$item);

		  }else {

		    session(['cart.'.$itemId => [
		      'itemId' => $itemId,
		      'quantity' => $quantity
		    ]]);

		  }

		}

		return [
			'error' => false,
			'messages' => []
		];
	}

	// private function getCartItemToken($itemId = null) {

	// 	$token = '';

	// 	if(empty($itemId)) {
	// 		return $token;
	// 	}

	// 	if(Auth::check()) {
	// 		$token = $this->where([
	// 		  ['item_id','=',$itemId],
	// 		  ['user_id','=',Auth::user()->id],
	// 		])
	// 		->select('token')->first()->token;
	// 	}else if(session()->has('cart.'.$itemId)) {
	// 		$token = 'cart.'.$itemId;
	// 	}

	// 	return $token;
	// }

	private function getItemObj($fields = 'carts.*') {
		$cart = $this->select($fields)
    		 ->join('items', 'items.id', '=', 'carts.item_id')
    		 ->where([
    		 	['carts.user_id','=',Auth::user()->id],
    		 	['items.deleted','=',0],
    		 	['items.cancel_option','=',0],
    		 	['items.date_2','>=',date('Y-m-d H:i:s')]
    		 ]);

		if($cart->exists()) {
			return $cart->get();
		}

		return [];
	}

	private function __getItemInCart($includeErrorItem = true) {

		$itemModel = new Item;

		$itemsInCart = [];

		if(Auth::check()) {

		 	foreach ($this->getItemObj(['carts.item_id','carts.quantity','carts.token']) as $cart) {

		 		$itemObj = $itemModel->getExistingItem($cart->item_id,['id','title','price','original_price','quantity','date_1','date_2','date_type']);

		 		if(empty($itemObj) || (!$includeErrorItem && $this->checkItemError($itemObj,$cart->quantity))) {
		 			continue;
		 		}

		 		$itemsInCart[] = [
		 			'obj' => $itemObj,
		 			'quantity' => $cart->quantity,
		 			'token' => $cart->token
		 		];
		 	}

		}else {

		  if(session()->has('cart')) {
		  	foreach (session()->get('cart') as $item) {

		  		$itemObj = $itemModel->getExistingItem($item['itemId'],['id','title','price','original_price','quantity']);

		  		if(empty($itemObj)) {
		  			continue;
		  		}

		  		$itemsInCart[] = [
		  			'obj' => $itemObj,
		  			'quantity' => $item['quantity'],
		  			'token' => 'cart.'.$item['itemId']
		  		];
		  	}
		  }

		}

		return $itemsInCart;
	}

	public function getItemInCart() {

		$items = [];
		foreach ($this->__getItemInCart() as $item) {
			$items[] = array_merge(
				$item['obj']->buildForCart(),
				[
					'quantity' => $item['quantity'],
					'summary' => [
					  'total' => $this->getItemTotal($item['obj'],$item['quantity'],true)
					],
					'token' => $item['token'],
					'errors' => $this->checkItemError($item['obj'],$item['quantity'],true)
				]
			);
		}

		return $items;
	}

	public function getSummary() {

		$items = $this->__getItemInCart(false);

		return [
			'quantity' => $this->getItemQuantity($items),
			'amount' => $this->getItemAmount($items),
			'summary' => $this->getCartSummary($items)
		];
	}

	public function getItemQuantity($items = null) {
		$quantity = 0;

		if(empty($items)) {
			$items = $this->__getItemInCart(false);
		}

		foreach ($items as $item) {
			$quantity += $item['quantity'];
		}

		return $quantity;
	}

	public function getItemAmount($items = null) {

		if(empty($items)) {
			$items = $this->__getItemInCart(false);
		}

		$amount = 0;
		foreach ($items as $item) {
			++$amount;
		}

		return $amount;
	}

	public function getCartSummary($items = null,$print = true,$format = true) {

		if(empty($items)) {
			$items = $this->__getItemInCart(false);
		}

		$result = [];
		foreach ($items as $item) {
			$result[] = $this->getItemSummary($item['obj'],$item['quantity'],false);
		}

		$summaries = [];
		foreach ($result as $summary) {
			foreach ($summary as $alias => $value) {
		
				if(!isset($summaries[$alias])) {
					$summaries[$alias] = 0;
				}

				$summaries[$alias] += $value;
			}
		}

		if(!$print) {
			return $summaries;
		}

		$_summaries = [];
		foreach ($summaries as $alias => $value) {
			$_summaries[] = [
				'value' => Currency::format($value),
				'title' => Service::getSummaryTitle($alias),
				'class' => Service::getSummaryClass($alias),
			];
		}

		return $_summaries;
	}

	// public function getCartSubTotal($items = [],$format = false) {

	// 	$subTotal = 0;

	// 	foreach ($items as $item) {
	// 		$subTotal += $this->getItemSubTotal($item['obj'],$item['quantity']);
	// 	}

	// 	if($format) {
	// 	  return Currency::format($subTotal);
	// 	}

	// 	return $subTotal;
	// }

	// public function getCartVat($items = [],$format = false) {
	// 	$vat = 0;

	// 	foreach ($items as $item) {
	// 		$vat += $this->getItemVat($item['obj'],$item['quantity']);
	// 	}

	// 	if($format) {
	// 	  return Currency::format($vat);
	// 	}

	// 	return $vat;
	// }

	// public function getCartTotal($items = [],$format = false) {

	// 	$total = 0;

	// 	foreach ($items as $item) {
	// 		$total += $this->getItemTotal($item['obj'],$item['quantity']);
	// 	}

	// 	if($format) {
	// 	  return Currency::format($total);
	// 	}

	// 	return $total;
	// }

	// Cal item Summary =====================================

	public function getItemSubTotal($item,$quantity,$format = false) {

	  if($format) {
	    return Currency::format($item->price * $quantity);
	  }

	  return $item->price * $quantity;
	}

	public function getItemVat($item,$quantity,$format = false) {

		$vat = Currency::vatRound($item->price * $this->vatRate) * $quantity;

		if($format) {
		  return Currency::format($vat);
		}

		return $vat;
	}

	public function getItemTotal($item,$quantity,$format = false) {

		$total = 0;

		foreach ($this->totalFxList as $fx) {
			$total += $this->{$fx}($item,$quantity);
		}

		if($format) {
		  return Currency::format($total);
		}

		return $total;
	}

	public function getItemSummary($item,$quantity,$print = true) {

		$summaries = []; 

		if($print) {
			foreach ($this->summaryFxList as $alias => $fx) {
			  $summaries[] = [
			  	'value' => $this->{$fx['name']}($item,$quantity),
			  	'title' => Service::getSummaryTitle($alias),
			  	'class' => Service::getSummaryClass($alias),
			  ];
			}
		}else {
			foreach ($this->summaryFxList as $alias => $fx) {
			  $summaries[$alias] = $this->{$fx['name']}($item,$quantity);
			}
		}

		return $summaries;
	}

	// =================================

	public function getItemForCheckout() {

		$itemModel = new Item;

		$checkout = [
			'items' => [],
			'summaries' => [],
			'quantity' => 0,
			'amount' => 0
		];

		// Get Item
		foreach ($this->getItemObj(['carts.item_id','carts.quantity']) as $cart) {

			$itemObj = $itemModel->getExistingItem($cart->item_id,['id','title','price','quantity','date_1','date_2','date_type','created_by']);

			if(empty($itemObj) || $this->checkItemError($itemObj,$cart->quantity)) {
				continue;
			}

			$checkout['items'][] = [
				'obj' => $itemObj,
				'quantity' => $cart->quantity,
				'summaries' => $this->getItemSummary($itemObj,$cart->quantity,false),
			];
		}

		$checkout['summaries'] = $this->getCartSummary($checkout['items'],false);
		$checkout['quantity'] = $this->getItemQuantity($checkout['items']);
		$checkout['amount'] = $this->getItemAmount($checkout['items']);

		return $checkout;
	}

	public function checkItemError($itemObj,$quantity,$message = false) {

		$errors = [];

		if($itemObj->isExpired()) {
			$errors[] = [
			  'no' => 3,
			  'message' => 'บัตรหมดอายุการใช้งาน'
			];
		}elseif($itemObj->quantity == 0) {
			$errors[] = [
			  'no' => 1,
			  'message' => 'สินค้าหมด'
			];
		}elseif($itemObj->quantity < $quantity) {
		  $errors[] = [
		    'no' => 2,
		    'message' => 'จำนวนสินค้าไม่พอต่อการสั่งซื้อ'
		  ];
		}

		if($message) {
			return $errors;			
		}

		return !empty($errors) ? true : false;
	}

}
