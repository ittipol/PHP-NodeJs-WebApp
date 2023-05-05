<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ProfileEditRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\library\service;
use App\library\snackbar;
use App\library\validation;
use App\library\themeColor;
use Auth;
use Redirect;

class AccountController extends Controller
{
  public function __construct() {
    $this->botDisallowed();
  }

  public function me() {

    $user = Service::loadModel('User')->find(Auth::user()->id);

    if(empty($user)) {
      return $this->error('ไม่พบโปรไฟล์');
    }

    $_shop = Service::loadModel('Shop')->where([
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    $shop = null;
    if(!empty($_shop)) {
      $shop = $_shop->buildDataDetail();
    }

    $this->setData('data',$user->getAttributes());
    $this->setData('avatar',$user->getProfileImage());
    // $this->setData('blocked',false);
    $this->setData('shop',$shop);
    $this->setData('hasShop',!empty($shop) ? true : false);

    $this->setMeta('title','โปรไฟล์ | Ticket Easys');

    return $this->view('pages.account.me');
  }

  public function profile($userId) {

    $user = Service::loadModel('User')->find($userId);

    if(empty($user)) {
      return $this->error('ไม่พบโปรไฟล์');
    }

    $shop = null;
    $_user = Service::loadModel('User')
    ->select('upgraded')
    ->find($userId);

    if(!empty($userId) && $_user->upgraded) {
      // redirect to page
      $_shop = Service::loadModel('Shop')->where([
        ['deleted','=',0],
        ['created_by','=',$userId]
      ])
      ->first();

      if(!empty($_shop)) {
        $shop = $_shop->buildDataDetail();
      }
    }

    $blocked = false;
    if(Auth::check() && (Auth::user()->id != $userId)) {
      // if(empty($shop)) {
      //   $blocked = Service::loadModel('UserBlocking')
      //   ->where([
      //     ['model','=','User'],
      //     ['model_id','=',$userId],
      //     ['user_id','=',Auth::user()->id]
      //   ])->exists();
      // }else {
      //   $blocked = Service::loadModel('UserBlocking')
      //   ->where([
      //     ['model','=','Shop'],
      //     ['model_id','=',$shop['id']],
      //     ['user_id','=',Auth::user()->id]
      //   ])->exists();
      // }

      $blocked = Service::loadModel('UserBlocking')
      ->where([
        ['model','=','User'],
        ['model_id','=',$userId],
        ['user_id','=',Auth::user()->id]
      ])->exists();
    }

    $_user = $user->getProfile();

    $this->setData('user',$_user);
    // $this->setData('avatar',$user->getProfileImage());
    $this->setData('blocked',$blocked);
    $this->setData('items',Service::loadModel('Item')
    ->where([
        ['created_by','=',$userId],
        ['cancel_option','=',0],
        // ['expiration_date','>',date('Y-m-d H:i:s')],
        ['approved','=',1],
        ['deleted','=',0]
      ])
    ->take(12)
    ->orderBy('created_at', 'desc')
    ->get());
    $this->setData('shop',$shop);

    $this->setMeta('title',$_user['name'].' | Ticket Easys');

    return $this->view('pages.account.profile');
  }

  public function manage(Request $request) {

    $model = Service::loadModel('Item')->query();

    $currentPage = 1;
    if(request()->has('page')) {
      $currentPage = request()->page;
    }
    //set page
    Paginator::currentPageResolver(function() use ($currentPage) {
        return $currentPage;
    });

    if($request->has('sort')) {

      switch ($request->sort) {
        case 'post_n':
          $model->orderBy('items.active_date','desc');
          break;
        
        case 'post_o':
          $model->orderBy('items.active_date','asc');
          break;

        case 'price_h':
          $model->orderBy('items.price','desc');
          break;

        case 'price_l':
          $model->orderBy('items.price','asc');
          break;

        default:
          $model->orderBy('items.active_date','desc');
          break;
      }

    }else {
      $model->orderBy('items.active_date','desc');
    }

    // $model = $model->where([
    //   ['cancel_option','=',0],
    //   ['deleted','=',0],
    //   ['created_by','=',Auth::user()->id]
    // ])->paginate(36);

    $model->where([
      ['cancel_option','=',0],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ]);

    $this->setPagination($model,36);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);

    // SET META
    $this->setMeta('title','รายการขายของฉัน | Ticket Easys');
    
    return $this->view('pages.account.manage');
  }

  public function edit() {
    $user = Service::loadModel('User')->find(Auth::user()->id);

    $image = $user->getRelatedData('Image',array(
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

     $profileImage = null;
    if(!empty($image)) {
      $profileImage = $image->getImageUrl();
    }

    $this->setData('data',$user);
    $this->setData('profileImage',$profileImage);
    $this->setData('themes',ThemeColor::getThemes());

    $this->setMeta('title','แก้ไขโปรไฟล์ | Ticket Easys');

    return $this->view('pages.account.form.profile_edit');
  }

  public function profileEditingSubmit(ProfileEditRequest $request) {
    $user = Service::loadModel('User')->find(Auth::user()->id);

    $user->name = strip_tags($request->name);
    $user->theme_color_id = $request->theme_color_id;

    if(!$user->save()) {
      Snackbar::message('ไม่สามารถบันทึกได้');
      return Redirect::back();
    }

    if($request->has('Avatar')) {

      $avatar = $request->get('Avatar');

      if(!empty($avatar['delete'])) {
        Service::loadModel('Image')->deleteAllImages($user);
      }

      Service::loadModel('Image')->deleteAllImages($user);

      if(!empty($avatar['filename'])) {

        if(empty($avatar['delete'])) {
          Service::loadModel('Image')->deleteAllImages($user);
        }

        Service::loadModel('Image')->addImage($user,array('filename' => $avatar['filename']),array(
          'token' => $avatar['token'],
          'type' => 'avatar',
        ));

      }
    
    }

    Snackbar::message('โปรไฟล์ถูกบันทึกแล้ว');
    return Redirect::to('/me');
  }

public function item(Request $request, $userId) {

    $user = Service::loadModel('User')->find($userId);

    if(empty($user)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    // $shop = null;
    // if(Auth::check() && ($userId != Auth::user()->id)) {
    //   $_user = Service::loadModel('User')
    //   ->select('upgraded')
    //   ->find($userId);

    //   if(!empty($userId) && $_user->upgraded) {
    //     // redirect to page
    //     $_shop = Service::loadModel('Shop')->where([
    //       ['deleted','=',0],
    //       ['created_by','=',$userId]
    //     ])
    //     ->first();

    //     if(!empty($_shop)) {
    //       $shop = $_shop->buildDataDetail();
    //     }
    //   }
    // }

    $model = Service::loadModel('Item')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }

    Paginator::currentPageResolver(function() use ($currentPage) {
      return $currentPage;
    });

    // $searchKeywords = array();
    $searching = false;

    if($request->has('q') && ($request->get('q') != '')) {
      $searching = true;

      $_q = trim(strip_tags($request->q));
      $_q = preg_replace('/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/', ' ', $_q);
      $_q = preg_replace('/\s{1,}/', ' ', $_q);

      $keywords = array();

      foreach (explode(' ', $_q) as $word) {

        $word = str_replace(array('\'','"'), '', $word);
        $word = str_replace('+', ' ', $word);

        $len = mb_strlen($word);

        if($len < 2) { // not search this word
          continue;
        }elseif(substr($_q, 0, 1) === '#') { // search by hashtag
          $keywords[] = array('description','like','%'.$word.'%');
        }else { // default search
          $keywords[] = array('title','like','%'.$word.'%');
          // $keywords[] = array('description','like','%#'.$word.'%'); // search only hashtag
          $keywords[] = array('place_location','like','%'.$word.'%');
        }
      }

      $model->where(function ($query) use ($keywords) {
        foreach ($keywords as $keyword) {
          $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
        }
      });

    }

    // if($request->has('q') && !empty($request->get('q'))) {
    //   $searching = true;

    //   $_q = trim(strip_tags($request->q));
    //   $_q = preg_replace('/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/', ' ', $_q);
    //   $_q = preg_replace('/\s{1,}/', ' ', $_q);

    //   $keywords = array();

    //   foreach (explode(' ', $_q) as $word) {

    //     $word = str_replace(array('\'','"'), '', $word);
    //     $word = str_replace('+', ' ', $word);

    //     $len = mb_strlen($word);

    //     if($len < 2) { // not search this word
    //       continue;
    //     }elseif(substr($_q, 0, 1) === '#') { // search by hashtag
    //       $keywords[] = array('items.description','like','%'.$word.'%');
    //     }else { // default search
    //       $keywords[] = array('items.title','like','%'.$word.'%');
    //       // $keywords[] = array('description','like','%#'.$word.'%'); // search only hashtag
    //     }
    //   }

    //   $model->where(function ($query) use ($keywords) {
    //     foreach ($keywords as $keyword) {
    //       $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
    //     }
    //   });

    // }

    // $locationSearchingData = null;
    // if(!empty($request->get('location'))) {
    //   $searching = true;

    //   $model
    //   ->join('item_to_locations', 'item_to_locations.item_id', '=', 'items.id')
    //   ->where('item_to_locations.location_id','=',$request->get('location')); 

    //   $paths = Service::loadModel('Location')->getLocationPaths($request->get('location'));

    //   $locationSearchingData = array(
    //     'id' => $request->get('location'),
    //     'path' => json_encode($paths)
    //   );

    //   foreach ($paths as $value) {
    //     $searchKeywords[] = 'พื้นที่: '.$value['name'];
    //   }
      
    // }

    if(!empty($request->get('price_start')) || !empty($request->get('price_end'))) {
      $searching = true;

      $conditions = array();

      if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
        // $searchKeywords[] = 'ราคาเริ่มต้น: '.$request->price_start;

        $conditions[] = array('items.price','>=',$request->price_start);
      }

      if($request->has('price_end') && Validation::isCurrency($request->price_end)) {
        // $searchKeywords[] = 'ราคาสูงสุด: '.$request->price_end;

        $conditions[] = array('items.price','<=',$request->price_end);
      }

      if(!empty($conditions)) {
        $model->where(function ($query) use ($conditions) {
          $query->where($conditions);
        });
      }
      
    }

    // if(($request->has('user') && ($request->get('user') == 1)) && ($request->has('shop') && ($request->get('shop') == 1))) {
    //   $searching = true;
    //   $searchKeywords[] = 'จาก: บุคคลทั่วไป';
    //   $searchKeywords[] = 'จาก: ร้านขายสินค้า';
    // }elseif($request->has('user') && ($request->get('user') == 1)) {
    //   $searching = true;
    //   $model->where('items.shop_id','=',null);
    //   $searchKeywords[] = 'จาก: บุคคลทั่วไป';
    // }elseif($request->has('shop') && ($request->get('shop') == 1)) {
    //   $searching = true;
    //   $model->where('items.shop_id','!=',null);
    //   $searchKeywords[] = $value['name'];
    //   $searchKeywords[] = 'จาก: ร้านขายสินค้า';
    // }

    // if(($request->has('sell') && ($request->get('sell') == 1)) && ($request->has('buy') && ($request->get('buy') == 1))) {
    //   $searching = true;
    //   // $searchKeywords[] = 'รายการขาย: ขาย';
    //   // $searchKeywords[] = 'รายการขาย: ซื้อ';
    // }elseif(!empty($request->get('sell'))) {
    //   $searching = true;
    //   $model->where('items.publishing_type','=',1);
    //   // $searchKeywords[] = 'รายการขาย: ขาย';
    // }elseif(!empty($request->get('buy'))) {
    //   $searching = true;
    //   $model->where('items.publishing_type','=',2);
    //   // $searchKeywords[] = 'รายการขาย: ซื้อ';
    // }

    // if(($request->has('new') && ($request->get('new') == 1)) && ($request->has('old') && ($request->get('old') == 1))) {
    //   $searching = true;
    //   $searchKeywords[] = 'สิค้า: ใหม่';
    //   $searchKeywords[] = 'รายการขาย: มือสอง';
    // }elseif(!empty($request->get('new'))) {
    //   $searching = true;
    //   $model->where('items.grading','=',1);
    //   $searchKeywords[] = 'สิค้า: ใหม่';
    // }elseif(!empty($request->get('old'))) {
    //   $searching = true;
    //   $model->where('items.grading','=',2);
    //   $searchKeywords[] = 'รายการขาย: มือสอง';
    // }

    // $model->where(function($q) use ($userId) {
    //   $q->where([
    //     ['created_by','=',$userId],
    //     ['cancel_option','=',0],
    //     ['expiration_date','>',date('Y-m-d H:i:s')],
    //     ['approved','=',1],
    //     ['deleted','=',0]
    //   ]);
    // });

    $model->where([
      ['cancel_option','=',0],
      // ['expiration_date','>',date('Y-m-d H:i:s')],
      ['approved','=',1],
      ['deleted','=',0],
      ['created_by','=',$userId]
    ]);

    if($request->has('sort')) {

      switch ($request->sort) {
        case 'post_n':
          $model->orderBy('items.active_date','desc');
          break;
        
        case 'post_o':
          $model->orderBy('items.active_date','asc');
          break;

        case 'price_h':
          $model->orderBy('items.price','desc');
          break;

        case 'price_l':
          $model->orderBy('items.price','asc');
          break;

        default:
          $model->orderBy('items.active_date','desc');
          break;
      }

    }else {
      $model->orderBy('items.active_date','desc');
    }

    // $blocked = false;
    // if(Auth::check() && (Auth::user()->id != $userId)) {
    //   if(empty($shop)) {
    //     $blocked = Service::loadModel('UserBlocking')
    //     ->where([
    //       ['model','=','User'],
    //       ['model_id','=',$userId],
    //       ['user_id','=',Auth::user()->id]
    //     ])->exists();
    //   }else {
    //     $blocked = Service::loadModel('UserBlocking')
    //     ->where([
    //       ['model','=','Shop'],
    //       ['model_id','=',$shop['id']],
    //       ['user_id','=',Auth::user()->id]
    //     ])->exists();
    //   }
    // }

    $shop = null;
    $_user = Service::loadModel('User')
    ->select('upgraded')
    ->find($userId);

    if(!empty($userId) && $_user->upgraded) {
      // redirect to page
      $_shop = Service::loadModel('Shop')->where([
        ['deleted','=',0],
        ['created_by','=',$userId]
      ])
      ->first();

      if(!empty($_shop)) {
        $shop = $_shop->buildDataDetail();
      }
    }
    
    $blocked = false;
    if(Auth::check() && ($userId != Auth::user()->id)) {
      // if(empty($shop)) {
      //   $blocked = Service::loadModel('UserBlocking')
      //   ->where([
      //     ['model','=','User'],
      //     ['model_id','=',$userId],
      //     ['user_id','=',Auth::user()->id]
      //   ])->exists();
      // }else {
      //   $blocked = Service::loadModel('UserBlocking')
      //   ->where([
      //     ['model','=','Shop'],
      //     ['model_id','=',$shop['id']],
      //     ['user_id','=',Auth::user()->id]
      //   ])->exists();
      // }

      $blocked = Service::loadModel('UserBlocking')
      ->where([
        ['model','=','User'],
        ['model_id','=',$userId],
        ['user_id','=',Auth::user()->id]
      ])->exists();
    }

    $_user = $user->getProfile();

    $model->select('items.*')->distinct('items.id');

    $this->setPagination($model,48);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    // $this->setData('data',$model->select('items.*')->distinct('items.id')->paginate(48));
    $this->setData('avatar',$user->getProfileImage());
    $this->setData('searching',$searching);
    // $this->setData('searchKeywords',$searchKeywords);

    $this->setData('user',$_user);
    $this->setData('shop',$shop);

    $this->setData('blocked',$blocked);

    $this->botDisallowed = false;

    // meta
    $this->setMeta('title','รายการขายของ '.$_user['name'].' | Ticket Easys');

    return $this->view('pages.account.item_list');
  }

  public function blocking() {

    $blockedData = Service::loadModel('UserBlocking')->where('user_id','=',Auth::user()->id)->get();

    $this->setData('blockedData',$blockedData);

    // meta
    $this->setMeta('title','รายการที่ไม่สนใจ');

    return $this->view('pages.account.blocking');
  }

  public function coin() {

    // GET coin from user

    // $model = Service::loadMode('CoinExchange');

    return $this->view('pages.account.coin');

  }

  public function exchangeList() {
    return $this->view('pages.account.exchange_list');

  }

  public function exchangeDetail() {

    return $this->view('pages.account.exchange_detail');

  }

  public function exchange() {

    $hour = [];
    for ($i=0; $i <= 59; $i++) { 
      $hour[$i] = $i;
    }

    $min = [];
    for ($i=0; $i <= 59; $i++) {

      $_min = $i;
      if(strlen($i) == 1) {
        $_min = '0'.$i;
      }

      $min[$i] = $_min;
    }

    $this->setData('hour',$hour);
    $this->setData('min',$min);

    return $this->view('pages.account.form.exchange');
  }

  public function exchangeSubmit(Request $request) {

    $model = Service::loadMode('CoinExchange');

    $model->amount = $request->get('amount');
    $model->method = $request->get('method');
    $model->status = 1;

    if(!!$model->description) {
      $model->description = trim(strip_tags($request->get('description')));
    }

    if(!$model->save()) {
      return Redirect::back();
    }

    // User log
    Service::addUserLog('CoinExchange',$model->id,'เพิ่มเหรียญ จำนวน'.$model->amount);

    // Go to History of this exchange page
    // this page show detail of this transaction
    // current status 

    // Session flash

    Snackbar::message('รายการของคุณได้อยู่ระหว่งการตรวจสอบความถูกต้อง');
    return Redirect::to('account/exchange/detail/'.$model->id);

  }

  public function myOrder() {

  }

  public function myOrderDetail() {

  }

  public function buyerOrder() {

  }

  public function buyerOrderDetail() {

  }

}
