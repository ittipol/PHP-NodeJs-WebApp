<?php

namespace App\Http\Controllers\Web;

use Illuminate\Pagination\Paginator;
use App\Http\Requests\ShopRequest;
use Illuminate\Http\Request;
use App\library\token;
use App\library\service;
use App\library\snackbar;
use App\library\validation;
use Redirect;
use Auth;

class ShopController extends Controller
{
  public function listView(Request $request) {

    $model = Service::loadModel('Shop')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }

    Paginator::currentPageResolver(function() use ($currentPage) {
      return $currentPage;
    });

    // $searchKeywords = array();

    $searching = false;

    if($request->has('q') && !empty($request->get('q'))) {
      $searching = true;

      $_q = trim(strip_tags($request->q));
      $_q = preg_replace('/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/', ' ', $_q);
      $_q = preg_replace('/\s{1,}/', ' ', $_q);

      // $searchKeywords[] = $_q;

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
          $keywords[] = array('name','like','%'.$word.'%');
          // $keywords[] = array('description','like','%#'.$word.'%'); // search only hashtag
        }
      }

      $model->where(function ($query) use ($keywords) {
        foreach ($keywords as $keyword) {
          $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
        }
    
      });

    }

    if(!empty($request->get('category'))) {
      $searching = true;

      $model
      ->join('shop_to_categories', 'shop_to_categories.shop_id', '=', 'shops.id')
      ->whereIn('shop_to_categories.category_id',$request->get('category'));

      // foreach ($request->get('category') as $value) {
      //   $searchKeywords[] = 'ประเภท: '.Service::loadModel('Category')->getCategoryName($value);
      // }

    }

    $locationSearchingData = null;
    if(!empty($request->get('location'))) {
      $searching = true;

      $locationPaths = Service::loadModel('LocationPath')
      ->select('location_id')
      ->where('path_id','=',$request->get('location'))
      ->get();

      $ids = array();
      foreach ($locationPaths as $locationPath) {
        $ids[] = $locationPath->location_id;
      }

      if(!empty($ids)) {
        $model->join('shop_to_locations', 'shop_to_locations.shop_id', '=', 'shops.id');
        $model->whereIn('shop_to_locations.location_id',$ids);
      }

      $paths = Service::loadModel('Location')->getLocationPaths($request->get('location'));

      $locationSearchingData = array(
        'id' => $request->get('location'),
        'path' => json_encode($paths)
      );

    }

    $blockedData = null;
    if(Auth::check()) {

      $blockedData = Service::loadModel('UserBlocking')->getBlockedData(Auth::user()->id);

      $model->where(function($q) use($blockedData) {

        if(!empty($blockedData['user'])) {
          $q->whereNotIn('shops.created_by',$blockedData['user']);
        }

      });

    }

    $model->where('deleted','=',0);

    if($request->has('sort')) {

      switch ($request->sort) {
        case 'post_n':
          $model->orderBy('shops.created_at','desc');
          break;
        
        case 'post_o':
          $model->orderBy('shops.created_at','asc');
          break;

        case 'name_desc':
          $model->orderBy('shops.name','desc');
          break;

        case 'name_asc':
          $model->orderBy('shops.name','asc');
          break;

        default:
          $model->orderBy('shops.created_at','desc');
          break;

        }

    }else {
      $model->orderBy('shops.created_at','desc');
    }

    $hasShop = false;
    if(Auth::check()) {
      $hasShop = Auth::user()->hasShop();
    }

    $model->select('shops.*')->distinct('shops.id');

    $this->setPagination($model,48);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    $this->setData('categories',Service::loadModel('Category')->select('id','name')->where('parent_id','=',null)->get());
    $this->setData('searching',$searching);
    // $this->setData('searchKeywords',$searchKeywords);
    $this->setData('locationSearchingData',$locationSearchingData);

    $this->setData('hasShop',$hasShop);


    if($searching) {
      if($request->has('q')) {
        $this->setMeta('title',$request->get('q').' — ค้นหาร้านขายตั๋ว');
      }else {
        $this->setMeta('title','ค้นหาร้านขายตั๋ว');
      }
    }else {
      $this->setMeta('title','ร้านขายตั๋ว | Ticket Easys');
    }

    return $this->view('pages.shop.list');
  }

  public function item(Request $request, $slug) {

    $shop = Service::loadModel('Shop')
    ->select('id','name','slug','deleted','created_by')
    ->where([
      ['slug','=',$slug],
      ['deleted','=',0]
    ])
    ->first();

    if(empty($shop)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    $now = date('Y-m-d H:i:s');

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
        // $searchKeywords[] = 'พื้นที่: '.$value['name'];
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

    if(!$request->has('expired')) {
      $model->where(function($query) use ($now) {

        $query
        ->where(function($query) {

          $query
          ->where('date_type','=',0)
          ->where('items.date_1','=',null)
          ->where('items.date_2','=',null);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->where('date_type','=',1)
          ->where('items.date_2','>=',$now);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->whereIn('date_type', [2,3])
          ->where('items.date_1','>=',$now);

        }); 

      });
    }

    $blocked = false;
    if(Auth::check() && (Auth::user()->id != $shop->created_by)) {
      $blocked = Service::loadModel('UserBlocking')
      ->where([
        ['model','=','User'],
        ['model_id','=',$shop->created_by],
        ['user_id','=',Auth::user()->id]
      ])->exists();
    }

    $model->where(function($q) use ($shop) {
      $q->where([
        ['cancel_option','=',0],
        // ['expiration_date','>',date('Y-m-d H:i:s')],
        ['approved','=',1],
        ['deleted','=',0],
        ['shop_id','=',$shop->id]
      ]);
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

    $model->select('items.*')->distinct('items.id');

    $this->setPagination($model,48);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    $this->setData('searching',$searching);

    $this->setData('shop',$shop->buildDataDetail());
    $this->setData('blocked',$blocked);

    // SET META
    $this->setMeta('title',$shop->name.' - สินค้า');
    $this->setMeta('description',$shop->getShortDesc());
    $this->setMeta('image',$shop->getCover());

    return $this->view('pages.shop.item_list');

  }

  public function page($slug) {

    $page = Service::loadModel('Shop')->where([
      ['slug','=',$slug],
      ['deleted','=',0]
    ])->first();

    if(empty($page)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    // if($page->deleted == 1) {
    //   return $this->error('ไม่พบร้านขายสินค้า');
    // }

    $now = date('Y-m-d H:i:s');

    // Get Lastest item
    $items = Service::loadModel('Item')
    ->where([
        ['created_by','=',$page->created_by],
        ['cancel_option','=',0],
        // ['expiration_date','>',date('Y-m-d H:i:s')],
        ['approved','=',1],
        ['deleted','=',0]
      ])
    ->where(function($query) use ($now) {

      $query
      ->where(function($query) {

        $query
        ->where('date_type','=',0)
        ->where('items.date_1','=',null)
        ->where('items.date_2','=',null);

      })
      ->orWhere(function($query) use ($now) {

        $query
        ->where('date_type','=',1)
        ->where('items.date_2','>=',$now);

      })
      ->orWhere(function($query) use ($now) {

        $query
        ->whereIn('date_type', [2,3])
        ->where('items.date_1','>=',$now);

      }); 

    })
    ->take(24)
    ->orderBy('created_at', 'desc')
    ->get();



    $blocked = false;
    if(Auth::check() && (Auth::user()->id != $page->created_by)) {
      $blocked = Service::loadModel('UserBlocking')
      ->where([
        ['model','=','User'],
        ['model_id','=',$page->created_by],
        ['user_id','=',Auth::user()->id]
      ])->exists();
    }

    $this->setData('shop',$page->buildDataDetail());
    $this->setData('items',$items);
    $this->setData('blocked',$blocked);

    // SET META
    $this->setMeta('title',$page->name);
    $this->setMeta('description',$page->getShortDesc());
    $this->setMeta('image',$page->getCover());

    return $this->view('pages.shop.page.index');
  }

  public function about($slug) {

    $page = Service::loadModel('Shop')->where([
      ['slug','=',$slug],
      ['deleted','=',0]
    ])->first();

    if(empty($page)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    // if($page->deleted == 1) {
    //   return $this->error('ไม่พบร้านขายสินค้า');
    // }

    $blocked = false;
    if(Auth::check() && (Auth::user()->id != $page->created_by)) {
      $blocked = Service::loadModel('UserBlocking')
      ->where([
        ['model','=','User'],
        ['model_id','=',$page->created_by],
        ['user_id','=',Auth::user()->id]
      ])->exists();
    }

    $this->setData('shop',$page->buildDataDetail());
    $this->setData('blocked',$blocked);

    // SET META
    $this->setMeta('title',$page->name.' - เกี่ยวกับ');
    $this->setMeta('description',$page->getShortDesc());
    $this->setMeta('image',$page->getCover());

    return $this->view('pages.shop.page.about');
  }

  public function create() {

    // Check if user has shop
    $shop = Service::loadModel('Shop')->select('slug')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ]);

    if($shop->exists()) {
      Snackbar::message('บัญชีนี้สร้างร้านขายสินค้าแล้ว ไม่สามารถสร้างร้านขายสินค้าได้');
      return Redirect::to('shop/page/'.$shop->first()->slug);
    }

    $this->setData('categories',Service::loadModel('Category')->select('id','name')->where('parent_id','=',null)->get());

    $this->randomBanner();

    $this->setMeta('title','สร้างร้านของคุณเพื่อใช้ซื้อ-ขาย บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆ | Ticket Easys');

    $this->botDisallowed();

    return $this->view('pages.shop.form.create');
  }

  public function creatingSubmit(ShopRequest $request) {

    // Check if user has shop
    $shop = Service::loadModel('Shop')->select('slug')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ]);

    if($shop->exists()) {
      Snackbar::message('บัญชีนี้สร้างร้านขายสินค้าแล้ว ไม่สามารถสร้างร้านขายสินค้าได้');
      return Redirect::to('shop/page/'.$shop->first()->slug);
    }

    $model = Service::loadModel('Shop');

    $model->name = preg_replace('/\s+/', ' ', strip_tags($request->get('name')));
    $model->description = $request->get('description');
    // $model->contact = strip_tags($request->get('contact'));
    $model->contact = $request->get('contact');

    $slug = preg_replace('/[\s\+\-@#%&=*!?|\\\\\/,<>:;\'"\[\]_^()]+/', '-', $model->name);

    if(substr($slug, strlen($slug)-1) == '-') {
      $slug = substr($slug, 0, strlen($slug)-1);
    }

    $extended = false;
    while ($model->where('slug','=',$slug)->exists()) {

      if(!$extended) {
        $slug .= '-';
        $extended = true;
      }

      $slug .= Token::generateNumber(1);
    }

    // Slug
    $model->slug = $slug;

    if(!$model->save()) {
      return Redirect::back();
    }

    // Add category to item
    if(!empty($request->get('ShopToCategory'))) {
      Service::loadModel('ShopToCategory')->__saveRelatedData($model,$request->get('ShopToCategory'));
    }

    // Add location to item
    if(!empty($request->get('ShopToLocation'))) {
      Service::loadModel('ShopToLocation')->__saveRelatedData($model,$request->get('ShopToLocation'));
    }

    if($request->has('cancel')) { 
      // delete item
      Service::loadModel('Item')->where([
        ['created_by','=',Auth::user()->id],
        ['deleted','=',0]
      ])->update([
        'deleted' => 1
      ]);

      // remove notification (item)
      Service::loadModel('Notification')->where([
        ['model','=','Item'],
        ['receiver_id','=',Auth::user()->id]
      ])->delete();

    }else { // import to shop
      Service::loadModel('Item')->where([
        ['created_by','=',Auth::user()->id],
        ['deleted','=',0]
      ])->update([
        'shop_id' => $model->id
      ]);
    }

    $user = Service::loadModel('User')->select('id')->find(Auth::user()->id);
    $user->update(['upgraded' => 1]);

    // Snackbar::message('ร้านขายตั๋วของคุณพร้อมใช้งานแล้ว');
    Snackbar::modal('ร้านขายตั๋วของคุณพร้อมใช้งานแล้ว','<a class="btn c-btn c-btn-bg" href="/ticket/new">ลงตั๋วในร้านของคุณ</a>','popup-success');

    return Redirect::to('shop/page/'.$model->slug);
  }

  public function profileEdit($slug) {

    // Check Shop owner
    $model = Service::loadModel('Shop')->where([
      ['slug','=',$slug],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถแก้ไขร้านขายสินค้านี้ได้');
      return Redirect::to('/shop/page/'.$slug);
    }

    $model['ShopToCategory'] = array(
      'category_id' => $model->getCategoryIds()
    );

    $this->setData('data',$model);
    $this->setData('locationId',$model->getLocationId());
    $this->setData('locationPaths',json_encode($model->getLocationPaths()));
    $this->setData('categories',Service::loadModel('Category')->select('id','name')->where('parent_id','=',null)->get());

    $this->setMeta('title',$model->name.' - แก้ไขร้านขายสินค้า');

    $this->botDisallowed();

    return $this->view('pages.shop.form.edit');

  }

  public function profileEditingSubmit(ShopRequest $request, $slug) {

    $model = Service::loadModel('Shop')->where([
      ['slug','=',$slug],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถแก้ไขร้านขายสินค้านี้ได้');
      return Redirect::to('/shop/page/'.$slug);
    }

    $model->name = preg_replace('/\s+/', ' ', strip_tags($request->get('name')));
    // $model->description = strip_tags($request->get('description'));
    // $model->contact = strip_tags($request->get('contact'));
    $model->description = $request->get('description');
    $model->contact = $request->get('contact');

    if(!$model->save()) {
      return Redirect::back();
    }

    // Add category to item
    if(!empty($request->get('ShopToCategory'))) {
      Service::loadModel('ShopToCategory')->__saveRelatedData($model,$request->get('ShopToCategory'));
    }

    // Add location to item
    if(!empty($request->get('ShopToLocation'))) {
      Service::loadModel('ShopToLocation')->__saveRelatedData($model,$request->get('ShopToLocation'));
    }

    if(!empty($model->description)) {
      Service::loadModel('HashtagList')->__saveRelatedData($model,$model->description);
    }

    Snackbar::message('แก้ไขเรียบร้อยแล้ว');

    return Redirect::to('shop/page/'.$model->slug);
  }

  public function setting($slug) {

    $shop = Service::loadModel('Shop')->select('name','created_by')->where('slug','=',$slug)->first();

    if(empty($shop)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    if($shop->deleted == 1) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    if($shop->created_by != Auth::user()->id) {
      return $this->error('ไม่สามารถแก้ไขร้านขายสินค้านี้ได้');
    }

    $this->setData('slug',$slug);

    $this->setMeta('title',$shop->name.' - ตั้งค่า');

    $this->botDisallowed();

    return $this->view('pages.shop.setting');
  }

  public function remove($slug) {

    $shop = Service::loadModel('Shop')->select('id','name','created_by')->where([
      ['slug','=',$slug],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($shop)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    if($shop->deleted == 1) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    if($shop->created_by != Auth::user()->id) {
      return $this->error('ไม่สามารถลบร้านขายสินค้านี้ได้');
    }

    $this->setData('id',$shop->id);
    $this->setData('name',$shop->name);
    $this->setData('slug',$slug);

    $this->setMeta('title',$shop->name.' - ลบร้านขายสินค้า');

    $this->botDisallowed();

    return $this->view('pages.shop.remove');
  }
  
}