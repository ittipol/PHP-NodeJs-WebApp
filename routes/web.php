<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'ItemController@__listView');
Route::get('ticket-list', 'ItemController@__listViewChuck');

Route::get('ticket/view/{itemId}', 'ItemController@detail');
Route::get('ticket/v/{slug}', 'ItemController@v_detail');
Route::get('v/{slug}', 'ItemController@v_detail');
Route::get('/get-item-contact/{itemId}', 'ItemController@getItemContact');
// Route::get('ticket/relate/{itemId}', 'ItemController@getRelatedItem');

// Route::post('ticket-detail', 'ItemController@_detail'); // API

Route::get('profile/{userId}', 'AccountController@profile');
Route::get('profile/{userId}/item', 'AccountController@item');

Route::get('shop', 'ShopController@listView'); // shop list

Route::get('shop/page/{slug}', 'ShopController@page');
Route::get('shop/page/{slug}/item', 'ShopController@item');
Route::get('shop/page/{slug}/about', 'ShopController@about');

Route::get('hashtag/{hashTag}', 'HashtagController@index');

Route::get('support/{page}', 'SupportController@index');

Route::group(['middleware' => 'guest'], function () {
  Route::get('login', array('as' => 'login', 'uses' => 'UserController@login'));
  Route::post('login', 'UserController@authenticate');

  Route::get('facebook/login', 'UserController@socialCallback');

  Route::get('subscribe', 'UserController@register');
  Route::post('subscribe', 'UserController@registering');

  Route::get('subscribe/success','UserController@registeringSuccess');

  // Route::get('account/verify','UserController@verify');

  Route::get('account/identify','UserController@forgottenPassword');
  Route::post('account/identify','UserController@forgottenPasswordSubmit');

  Route::get('account/recover','UserController@recover');
  Route::post('account/recover','UserController@recoverSubmit');
});

Route::group(['middleware' => 'auth'], function () {

  Route::get('logout', 'UserController@logout');

  Route::get('me', 'AccountController@me');

  Route::get('account/edit', 'AccountController@edit');
  Route::patch('account/edit', 'AccountController@profileEditingSubmit');

  Route::get('account/blocking', 'AccountController@blocking');

  Route::get('account/sale', 'AccountController@manage');
  Route::get('account/coin', 'AccountController@coin');

  Route::get('account/exchange', 'AccountController@exchange');
  Route::post('account/exchange', 'CoinExchangeController@exchangeSubmit');

  Route::get('account/exchange/detail/{exchangeId}', 'CoinExchangeController@exchangeDetail');

  // Route::get('order', 'OrderController@orderOverview');

  Route::get('order', 'OrderController@myOrderListView');
  Route::get('order-detail/{orderId}', 'OrderController@myOrderDetail');

  Route::get('client-order', 'OrderController@clientOrderListView');
  Route::get('client-order-detail/{orderId}', 'OrderController@clientOrderDetail');

  Route::get('item-receiving-confirmation/{orderId}/{userId}', 'OrderController@itemReceivingDetail');
  Route::post('item-receiving-confirmation', 'OrderController@itemReceivingSummit');
  
  Route::get('get-shipping-detail/{orderId}/{userId}', 'ShippingController@shippingDetail');
  Route::post('shipping-confirmation/{orderId}', 'ShippingController@shippingConfirmation');

  // Payment
  Route::get('order/payment/{orderId}', 'PaymentController@payment');
  Route::post('order/payment/{orderId}', 'PaymentController@paymentSubmit');

  Route::get('ticket/new', 'ItemController@add');
  Route::post('ticket/new', 'ItemController@addingSubmit');
  Route::get('ticket/edit/{itemId}', 'ItemController@edit');
  Route::patch('ticket/edit/{itemId}', 'ItemController@editingSubmit');
  Route::post('ticket/cancel', 'ItemController@cancel');
  Route::get('ticket/pull/{itemId}', 'ItemController@pullPost');
  // Route::get('ticket/extend/{itemId}', 'ItemController@extendExpire');
  // Route::get('ticket/overview/{itemId}', 'ItemController@overview');
  // Route::get('ticket/chat/{modelId}', 'ChatController@itemChat');

  Route::get('shop/create', 'ShopController@create'); // shop/open
  Route::post('shop/create', 'ShopController@creatingSubmit');

  // Route::get('shop/page/{slug}/chat', 'ChatController@shopChatBySlug');

  Route::get('shop/page/{slug}/setting', 'ShopController@setting');

  Route::get('shop/page/{slug}/remove', 'ShopController@remove');

  Route::get('shop/page/{slug}/edit', 'ShopController@profileEdit');
  Route::patch('shop/page/{slug}/edit', 'ShopController@profileEditingSubmit');

  // Route::get('chat/{roomId}', 'ChatController@chatRoom');

  Route::post('upload/image', 'ImageController@upload');
  Route::post('upload/avatar', 'ImageController@avatarUpload');
  Route::post('upload/banner', 'ImageController@bannerUpload');
  Route::post('upload/preview', 'ImageController@previewUpload');
  Route::post('upload/page/image', 'ImageController@uploadPageImage');

  // page viewing
  Route::post('page-record', 'PageController@record');
});

Route::post('chat', 'ChatController@_chatRoom');

Route::get('avatar/{userId?}/{filename?}', 'StaticFileController@userAvatar');
Route::get('shop/{slug}/avatar', 'StaticFileController@shopAvatar');
Route::get('get_image/{filename}', 'StaticFileController@serveImages');
Route::get('t_image/{filename}', 'StaticFileController@temp');

// Exchange to coin page (transfer)


// Cart
// Route::get('cart', 'CartController@getItemInCart');
Route::get('cart', 'CartController@renderItemInCart');

Route::get('cart/info', 'CartController@getInfo');

Route::post('cart/add/{itemId}','CartController@addToCart');
Route::post('cart/update/{itemId}', 'CartController@updateToCart');
Route::post('cart/delete', 'CartController@deleteToCart');

// Item Checkout
Route::get('cart/checkout', 'CartController@checkout');

Route::group(['middleware' => 'auth'], function () {
  Route::post('checkout', 'CheckoutController@checkoutSubmit');
});