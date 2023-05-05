<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ItemRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\library\service;
use App\library\token;
use App\library\validation;
use App\library\snackbar;
use App\library\toastNotification;
use App\library\stringHelper;
use App\library\themeColor;
use Redirect;
use Cookie;
use Auth;

// use App\Http\Repositories\NotificationRepository;

class ItemController extends Controller
{
  public function __listView(Request $request) {
    // GET Pinned item
    // $pinnedItems = Service::loadModel('Item')
    //   ->where('cancel_option','=',0)
    //   ->where('deleted','=',0)
    //   ->where('pinned','=',1)
    //   ->get();

    $time = time();

    $start = date('Y-m-d H:i:s',$time);
    $end = date('Y-m-d H:i:s',$time+1728000); // 20 days

    $take = 6;

    $model = Service::loadModel('Item')->query();

    $model->where(function($query) use ($start,$end) {

      $query
      ->Where(function($query) use ($start,$end) {

        $query
        ->where('date_type','=',1)
        ->where('date_2', '>=', $start);

      })
      ->orWhere(function($query) use ($start,$end) {

        $query
        ->whereIn('date_type', [2,3])
        ->where('date_1', '>=', $start);

      }); 

    });

    $model->where([
      ['cancel_option','=',0],
      ['deleted','=',0]
    ]);

    if($model->count() > $take) {
      $model->take($take)->skip(rand(1,$model->count() - $take));
    }

    // random items (interesting item) =================================================

    $end = date('Y-m-d H:i:s',$time+5184000); // 60 days

    $item = Service::loadModel('Item')->query();

    /*$item->where(function($query) use ($start,$end) {

      $query
      ->Where(function($query) use ($start,$end) {

        $query
        ->where('date_type','=',1)
        ->where('date_2', '>=', $start)
        ->where('date_2', '<=', $end);

      })
      ->orWhere(function($query) use ($start,$end) {

        $query
        ->whereIn('date_type', [2,3])
        ->where('date_1', '>=', $start)
        ->where('date_1', '<=', $end);

      }); 

    });*/

    $item->where([
      ['cancel_option','=',0],
      ['deleted','=',0],
    ]);

    if($item->count() > 1) {
      $item->take(1)->skip(rand(1,$item->count() - 1));
    }

    $hasShop = Auth::check() && Auth::user()->hasShop();

    // Get Recommeneded shop
    $recommendedShop = Service::loadModel('Shop')->orderBy('id','desc')->first()->buildDataDetail();

    $this->setData('itemDetail',$item->first()->buildDataDetail());
    $this->setData('pinnedItems',$model->get()); 

    $this->setData('categories',Service::loadModel('Category')->get());
    $this->setData('recommendedShop',$recommendedShop);
    $this->setData('hasShop',$hasShop);

    // $this->randomBanner();
    // $this->randomBanner('xl');

    $this->showAricleNotification();

    return $this->view('pages.item.list_no_canvas');
  }

  public function __listViewChuck(Request $request) {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $model = Service::loadModel('Item')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }

    Paginator::currentPageResolver(function() use ($currentPage) {
      return $currentPage;
    });

    $now = date('Y-m-d H:i:s');

    $searching = false;

    if($request->has('q') && ($request->get('q') != '')) {
      $searching = true;

      $_q = trim(strip_tags($request->q));
      $_q = preg_replace('/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/', ' ', $_q);
      $_q = preg_replace('/\s{1,}/', ' ', $_q);

      $keywords = array();
      // $wordIds = array();

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

    if(!empty($request->get('category'))) {
      $searching = true;

      $model
      ->join('item_to_categories', 'item_to_categories.item_id', '=', 'items.id')
      ->whereIn('item_to_categories.category_id',$request->category);
    }

    if(!empty($request->get('price_start')) || !empty($request->get('price_end'))) {
      $searching = true;

      $model->where(function ($query) use ($request) {
        if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
          $query->where('items.price','>=',$request->price_start);
        }

        if($request->has('price_end') && ($request->price_end > 0) && Validation::isCurrency($request->price_end)) {
          $query->where('items.price','<=',$request->price_end);
        }
      });

    }

    if(!empty($request->get('location'))) {
      $searching = true;

      $model
      ->join('item_to_locations', 'item_to_locations.item_id', '=', 'items.id')
      ->where('item_to_locations.location_id','=',$request->get('location')); 
    }

    if(($request->has('start_date') && ($request->get('start_date') != null)) 
      || 
      ($request->has('end_date') && ($request->get('end_date') != null))) {
      $searching = true;

      $model->where(function ($query) use ($request) {

        if($request->has('start_date') && $request->has('end_date')) {

          $query
          ->where([
            ['items.date_1','>=',$request->start_date],
            ['items.date_1','<=',$request->end_date]
          ])
          ->orWhere([
            ['items.date_2','>=',$request->start_date],
            ['items.date_2','<=',$request->end_date]
          ]);
        }elseif($request->has('start_date') && ($request->get('start_date') != null)) {
          $query
          ->where('items.date_1','>=',$request->start_date)
          ->orWhere('items.date_2','>=',$request->start_date);
        }elseif($request->has('end_date') && ($request->get('end_date') != null)) {
          $query
          ->where('items.date_1','<=',$request->end_date)
          ->orWhere('items.date_2','<=',$request->end_date);
        }

      });
    }else{

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

    $model->where(function($q) use ($now) {
      $q->where('cancel_option','=',0)
        ->where('deleted','=',0);
        // ->where('date_2','>=',$now);
        // ->where('expiration_date','>=',$now);
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

        case 'card_date':
          $model->orderBy('items.date_1','asc');
          break;

        default:
          $model->orderBy('items.active_date','desc');
          break;
      }

    }else {
      $model->orderBy('items.active_date','desc');
    }

    // $data = [];

    // if(!$searching || (!$request->has('q') || ($request->get('q') == null)) || !empty($keywords)) {
    //   $data = $model->paginate(48);
    // }
    $data = $model->paginate(24);  

    $next = true;
    if($request->page == $data->lastPage()) {
      $next = false;
    }

    $html = '';
    $hasData = true;

    if((count($data) > 0) && ($data->currentPage() <= $data->lastPage())) {
      $html = view('pages.item._item_list',array(
        'data' => $data
      ))->render();
    }elseif($searching) {
      // Display searching not found
      $hasData = false;
      $html = '<div class="tc mv6 ph3 pv5 white">
        <h3 class="dark-gray">ไม่พบรายการที่กำลังค้นหา</h3>
        <p class="dark-gray">โปรดลองค้นหาอีกครั้ง ด้วยคำที่แตกต่างหรือคำที่มีความหมายใกล้เคียง</p>
      </div>';
    }else {
      // Display ticket not found
      $hasData = false;
      $html = '<div class="tc mv6 ph3 pv5 white">
        <h3 class="dark-gray">ยังไม่มีรายการขาย</h3>
        <p class="dark-gray">บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่</p>
        <a href="/ticket/new" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">
          ขายบัตรของคุณ
        </a>
      </div>';
    }

    $result = array(
      'hasData' => $hasData,
      'html' => $html,
      'next' => $next
    );

    return response()->json($result);

  }

  // =================================================================

  // public function listView(Request $request, $category = null) {

  //   $model = Service::loadModel('Item')->query();

  //   $currentPage = 1;
  //   if($request->has('page')) {
  //     $currentPage = $request->page;
  //   }

  //   Paginator::currentPageResolver(function() use ($currentPage) {
  //     return $currentPage;
  //   });

  //   $searchData = array();
  //   $searching = false;

  //   if($request->has('q') && !empty($request->get('q'))) {
  //     $searching = true;

  //     $_q = trim(strip_tags($request->q));
  //     $_q = preg_replace('/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/', ' ', $_q);
  //     $_q = preg_replace('/\s{1,}/', ' ', $_q);

  //     $keywords = array();

  //     foreach (explode(' ', $_q) as $word) {

  //       $word = str_replace(array('\'','"'), '', $word);
  //       $word = str_replace('+', ' ', $word);

  //       $len = mb_strlen($word);

  //       if($len < 2) { // not search this word
  //         continue;
  //       }elseif(substr($_q, 0, 1) === '#') { // search by hashtag
  //         $keywords[] = array('items.description','like','%'.$word.'%');

  //         $searchData['q_description'][] = $word;
  //       }else { // default search
  //         $keywords[] = array('items.title','like','%'.$word.'%');
  //         // $keywords[] = array('description','like','%#'.$word.'%'); // search only hashtag

  //         $searchData['q_title'][] = $word;
  //       }
  //     }

  //     $model->where(function ($query) use ($keywords) {
  //       foreach ($keywords as $keyword) {
  //         $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
  //       }
  //     });

  //   }

  //   $locationSearchingData = null;
  //   if(!empty($request->get('location'))) {
  //     $searching = true;

  //     $locationPaths = Service::loadModel('LocationPath')
  //     ->select('location_id')
  //     ->where('path_id','=',$request->get('location'))
  //     ->get();

  //     $ids = array();
  //     foreach ($locationPaths as $locationPath) {
  //       $ids[] = $locationPath->location_id;
  //     }

  //     if(!empty($ids)) {
  //       $model->leftJoin('item_to_locations', 'item_to_locations.item_id', '=', 'items.id');
  //       $model->leftJoin('shops', 'shops.id', '=', 'items.shop_id');
  //       $model->leftJoin('shop_to_locations', 'shop_to_locations.shop_id', '=', 'shops.id');

  //       $model->where(function($q) use($ids) {
  //         // $q
  //         // ->where(function($_q) use($ids) {
  //         //   $_q->whereIn('item_to_locations.location_id',$ids);
  //         // })
  //         // ->orWhere(function($_q) use($ids) {
  //         //   $_q->where('items.use_shop_location','=',1)->whereIn('shop_to_locations.location_id',$ids);
  //         // });

  //         $q
  //         ->whereIn('item_to_locations.location_id',$ids)
  //         ->orWhere(function($_q) use($ids) {
  //           $_q->where('items.use_shop_location','=',1)->whereIn('shop_to_locations.location_id',$ids);
  //         });
  //       });

  //       // $model->whereIn('item_to_locations.location_id',$ids);
  //     }

  //     // old code
  //     // $model
  //     // ->join('item_to_locations', 'item_to_locations.item_id', '=', 'items.id')
  //     // ->where('item_to_locations.location_id','=',$request->get('location')); 

  //     $paths = Service::loadModel('Location')->getLocationPaths($request->get('location'));

  //     $locationSearchingData = array(
  //       'id' => $request->get('location'),
  //       'path' => json_encode($paths)
  //     );

  //     $searchData['location'] = $request->get('location');
  //   }

  //   if($request->has('price_start') || $request->has('price_end')) {
  //     $searching = true;

  //     $conditions = array();

  //     if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
  //       $conditions[] = array('items.price','>=',$request->price_start);
  //       $searchData['price_start'] = $request->get('price_start');
  //     }

  //     if($request->has('price_end') && Validation::isCurrency($request->price_end)) {
  //       $conditions[] = array('items.price','<=',$request->price_end);
  //       $searchData['price_end'] = $request->get('price_end');
  //     }

  //     if(!empty($conditions)) {
  //       $model->where(function ($query) use ($conditions) {
  //         $query->where($conditions);
  //       });
  //     }
  //   }

  //   if(($request->has('user') && ($request->get('user') == 1)) && ($request->has('shop') && ($request->get('shop') == 1))) {
  //     $searching = true;
  //   }elseif($request->has('user') && ($request->get('user') == 1)) {
  //     $searching = true;
  //     $model->where('items.shop_id','=',null);
  //     $searchData['from'] = 1;
  //   }elseif($request->has('shop') && ($request->get('shop') == 1)) {
  //     $searching = true;
  //     $model->where('items.shop_id','!=',null);
  //     $searchData['from'] = 2;
  //   }

  //   if(($request->has('sell') && ($request->get('sell') == 1)) && ($request->has('buy') && ($request->get('buy') == 1))) {
  //     $searching = true;
  //   }elseif(!empty($request->get('sell'))) {
  //     $searching = true;
  //     $model->where('items.publishing_type','=',1);
  //     $searchData['publishing'] = 1;
  //   }elseif(!empty($request->get('buy'))) {
  //     $searching = true;
  //     $model->where('items.publishing_type','=',2);
  //     $searchData['publishing'] = 2;
  //   }

  //   // if(
  //   //   ($request->has('new') && ($request->get('new') == 1)) && 
  //   //   ($request->has('old') && ($request->get('old') == 1)) &&
  //   //   ($request->has('homemade') && ($request->get('homemade') == 1))
  //   //   ) {
  //   //   $searching = true;
  //   // }else{

  //   //   if(!empty($request->get('new'))) {
  //   //     $searching = true;
  //   //     $model->where('items.grading','=',1);

  //   //     $searchData['item'][] = 1;
  //   //   }

  //   //   if(!empty($request->get('old'))) {
  //   //     $searching = true;
  //   //     $model->where('items.grading','=',2);

  //   //     $searchData['item'][] = 2;
  //   //   }
      
  //   //   if(!empty($request->get('homemade'))) {
  //   //     $searching = true;
  //   //     $model->where('items.grading','=',3);

  //   //     $searchData['item'][] = 3;
  //   //   }
  //   // }

  //   if($request->has('new') || $request->has('old') || $request->has('homemade')) {

  //     if(($request->get('new') == 1) && ($request->get('old') == 1) && ($request->get('homemade') == 1)) {
  //       $searching = true;
  //     }else {

  //       $searching = true;

  //       $_sqlArr = array();

  //       if(!empty($request->get('new'))) {
          
  //         // $model->where('items.grading','=',1);

  //         $_sqlArr[] = 1;
  //       }

  //       if(!empty($request->get('old'))) {

  //         // $model->where('items.grading','=',2);

  //         $_sqlArr[] = 2;
  //       }
        
  //       if(!empty($request->get('homemade'))) {

  //         // $model->where('items.grading','=',3);

  //         $_sqlArr[] = 3;
  //       }

  //       $model->where(function($q) use($_sqlArr) {
  //         foreach ($_sqlArr as $value) {
  //           $q->orWhere('items.grading','=',$value);
  //         }
  //       });

  //       $searchData['item'] = $_sqlArr;
  //     }
  //   }

  //   $blockedData = null;
  //   if(Auth::check()) {

  //     $blockedData = Service::loadModel('UserBlocking')->getBlockedData(Auth::user()->id);

  //     $model->where(function($q) use($blockedData) {

  //       // if(!empty($blockedData['user']) && !empty($blockedData['item'])) {
  //       //   $q
  //       //   ->whereNotIn('items.created_by',$blockedData['user'])
  //       //   ->orWhereNotIn('items.id',$blockedData['item']);
  //       // }elseif(!empty($blockedData['user'])) {
  //       //   $q->whereNotIn('items.created_by',$blockedData['user']);
  //       // }elseif(!empty($blockedData['item'])) {
  //       //   $q->whereNotIn('items.created_by',$blockedData['item']);
  //       // }



  //       // if(Auth::user()->upgraded && !empty($blockedData['shop'])) {
  //       //   $q->whereNotIn('items.shop_id',$blockedData['shop']);
  //       // }elseif(!empty($blockedData['user'])) {
  //       //   $q->whereNotIn('items.created_by',$blockedData['user']);
  //       // }


  //       if(!empty($blockedData['user'])) {
  //         $q->whereNotIn('items.created_by',$blockedData['user']);
  //       }

  //       if(!empty($blockedData['item'])) {
  //         $q->whereNotIn('items.id',$blockedData['item']);
  //       }
 
  //     });

  //   }

  //   // $now = date('Y-m-d H:i:s');
  //   // $searchData['date'] = $now;

  //   // $model->where(function($q) {
  //   //   $q->where([
  //   //     ['cancel_option','=',0],
  //   //     ['expiration_date','>',date('Y-m-d H:i:s')],
  //   //     ['approved','=',1],
  //   //     ['deleted','=',0]
  //   //   ]);
  //   // });

  //   $model
  //   ->where([
  //     ['items.cancel_option','=',0],
  //     ['items.expiration_date','>',date('Y-m-d H:i:s')],
  //     ['items.approved','=',1],
  //     ['items.deleted','=',0]
  //   ]);

  //   $queryString = '';
  //   if(!empty($request->getQueryString())) {
  //     $queryString = '?'.$request->getQueryString();
  //   }

  //   $categoryData = array();
  //   if(empty($category)) {

  //     $options = array(
  //       // 'blocking' => $blockedData,
  //       // 'cancel_option' => 0,
  //       // 'approved' => 1,
  //       // 'deleted' => 0,
  //       // 'expiration_date' => date('Y-m-d H:i:s'),
  //       'model' => $model,
  //       'queryString' => $queryString
  //     );

  //     $categories = Service::loadModel('Category')->select('id','name','slug','image')->where('parent_id','=',null)->get();

  //     foreach ($categories as $_category) {

  //       $categoryData[] = array(
  //         'name' => $_category->name,
  //         'url' => '/category/'.$_category->slug.$queryString,
  //         // 'total' => $_category->countItem($_category->id,$options),
  //         'subCategories' => Service::loadModel('Category')->getCategoriesWithSubCategories($_category->id,$options),
  //         'image' => $_category->getImagePath()
  //       );

  //     }

  //   }else {

  //     $_category = Service::loadModel('Category')->select('id','name','parent_id','image')->where('slug','=',$category)->first();

  //     if(empty($_category)) {
  //       return $this->error('ไม่พบประเภทสินค้า');
  //     }

  //     $back = '/';
  //     if(!empty($_category->parent_id)) {
  //       $back = '/category/'.Service::loadModel('Category')->select('slug')->where('id','=',$_category->parent_id)->first()->slug;
  //     }

  //     $options = array(
  //       'model' => $model,
  //       'queryString' => $queryString
  //     );

  //     $categoryData = array(
  //       'id' => $_category->id,
  //       'name' => $_category->name,
  //       'image' => $_category->getImagePath(),
  //       // 'breadcrumb' => Service::loadModel('Category')->breadcrumb($_category->parent_id),
  //       'path' => json_encode(Service::loadModel('Category')->getCategoryPaths($_category->id)),
  //       'recommended' => Service::loadModel('Category')->getRecommendedCategories($_category->id,$options),
  //       'back' => $back.$queryString
  //     );

  //     $categoryPaths = Service::loadModel('CategoryPath')->select('category_id')->where('path_id','=',$_category->id)->get();

  //     $ids = array();
  //     foreach ($categoryPaths as $categoryPath) {
  //       $ids[] = $categoryPath->category_id;
  //     }

  //     if(!empty($ids)) {
  //       $model->join('item_to_categories', 'item_to_categories.item_id', '=', 'items.id');
  //       $model->whereIn('item_to_categories.category_id',$ids);
  //     }
  //   }

  //   if($request->has('sort')) {

  //     switch ($request->sort) {
  //       case 'post_n':
  //         $model->orderBy('items.active_date','desc');
  //         break;
        
  //       case 'post_o':
  //         $model->orderBy('items.active_date','asc');
  //         break;

  //       case 'price_h':
  //         $model->orderBy('items.price','desc');
  //         break;

  //       case 'price_l':
  //         $model->orderBy('items.price','asc');
  //         break;

  //       default:
  //         $model->orderBy('items.active_date','desc');
  //         break;
  //     }

  //   }else {
  //     $model->orderBy('items.active_date','desc');
  //   }

  //   $this->setData('data',$model->select('items.*')->distinct('items.id')->paginate(48));
  //   $this->setData('categoryData',$categoryData);
  //   $this->setData('locationSearchingData',$locationSearchingData);
  //   $this->setData('searching',$searching);

  //   if(!empty($category)) {
  //     $this->setData('searchData',json_encode($searchData));
  //     $this->setData('queryString',$queryString);

  //     if($searching) {
  //       if($request->has('q')) {
  //         $this->setMeta('title',$request->get('q').' — ค้นหาสินค้า | '.$categoryData['name']);
  //       }else {
  //         $this->setMeta('title','ค้นหาสินค้า | '.$categoryData['name']);
  //       }
  //     }else {
  //       $this->setMeta('title',$categoryData['name']);
  //     }

  //     return $this->view('pages.item.list-w-cat');
  //   }

  //   $showBanner = false;
  //   if($searching) {
  //     if($request->has('q')) {
  //       $this->setMeta('title',$request->get('q').' — ค้นหาสินค้า');
  //     }else {
  //       $this->setMeta('title','ค้นหาสินค้า');
  //     }
  //   }elseif($currentPage == 1) {
  //     $showBanner = true;
  //   }

  //   // check show banner
  //   $this->setData('showBanner',$showBanner);

  //   return $this->view('pages.item.list');
  // }

  public function detail($itemId) {

    $model = Service::loadModel('Item')->where('id','=',$itemId)->first();

    return $this->_detail($model,$itemId);
  }

  public function v_detail($slug) {

    $model = Service::loadModel('Item')->where('slug','=',$slug)->first();
//dd($model);
    return $this->_detail($model,$model->id);
  }

  private function _detail($model,$itemId) {

    if(empty($model)) {
      return $this->error('ไม่พบรายการขาย');
    }

    if($model->deleted == 1) {
      return $this->error('รายการขาย "'.$model->title.'" ถูกลบแล้ว');
    }

    if($model->cancel_option > 0) {
      return $this->error('รายการขาย "'.$model->title.'" ถูกยกเลิกแล้ว');
    }

    // if((Auth::guest() || (Auth::check() && (Auth::user()->id != $model->created_by))) && ($model->expiration_date < date('Y-m-d H:i:s'))) {
    //   return $this->error('รายการขาย "'.$model->title.'" หมดอายุแล้ว');
    // }

    // if((Auth::guest() && !$model->approved) || (Auth::check() && (Auth::user()->id != $model->created_by) && !$model->approved)) {
    //   return $this->error('ขออภัย รายการขาย "'.$model->title.'" อยู่ระหว่างการตรวจสอบ');
    // }

    $blocked = false;
    if(Auth::check() && (Auth::user()->id != $model->created_by)) {
      $blocked = Service::loadModel('UserBlocking')
      ->where(function($q) use($model) {

        // if(empty($model->shop_id)) {
        //   $q
        //   ->where([
        //     ['model','=','User'],
        //     ['model_id','=',$model->created_by]
        //   ])
        //   ->orWhere([
        //     ['model','=','Item'],
        //     ['model_id','=',$model->id]
        //   ]);
        // }else {
        //   $q
        //   ->where([
        //     ['model','=','Shop'],
        //     ['model_id','=',$model->shop_id]
        //   ])
        //   ->orWhere([
        //     ['model','=','Item'],
        //     ['model_id','=',$model->id]
        //   ]); 
        // }

        $q
        ->where([
          ['model','=','User'],
          ['model_id','=',$model->created_by]
        ])
        ->orWhere([
          ['model','=','Item'],
          ['model_id','=',$model->id]
        ]);

      })
      ->where('user_id','=',Auth::user()->id)->exists();
    }

    $this->setData('data',$model->buildDataDetail());
    // $this->setData('owner',$model->getOwnerInfo());
    $this->setData('breadcrumb',$model->getCategoryBreadcrumb());
    $this->setData('locations',$model->getLocationBreadcrumb());
    // $this->setData('hasShop',$model->hasShop());
    $this->setData('blocked',$blocked);

    // not using
    // $this->setData('expired',$model->checkIsExpire());
    // $this->setData('extendExpire',$model->chechCanExtendExpire());
    // $this->setData('extendDays',$model->getExtendDays());
    // $this->setData('pullPost',$model->checkRePost());

    // Get Related With This Item
    $this->getRelatedItem($itemId);

    $this->randomBanner();

    $this->getSharingTitle($model->id,$model->getCategoryId(),$model->checkIsItemOwner());

    // SET META
    $this->setMeta('title',$model->title);
    $this->setMeta('description',$model->getShortDesc());
    $this->setMeta('image',$model->getMetaImage());

    return $this->view('pages.item.detail');

  }

  // public function _detail(Request $request) {

  //   if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
  //     return false;
  //   }

  //   if(empty($request->get('id'))) {
  //     return json_encode(array(
  //       'success' => false,
  //       'errorMessage' => 'ไม่พบรายการขาย'
  //     ));
  //   }

  //   $model = Service::loadModel('Item')->where('id','=',$request->get('id'))->first();

  //   if(empty($model)) {
  //     return json_encode(array(
  //       'success' => false,
  //       'errorMessage' => 'ไม่พบรายการขาย'
  //     ));
  //   }

  //   if($model->deleted == 1) {
  //     return json_encode(array(
  //       'success' => false,
  //       'errorMessage' => 'รายการขาย "'.$model->title.'" ถูกลบแล้ว'
  //     ));
  //   }

  //   if($model->cancel_option > 0) {
  //     return json_encode(array(
  //       'success' => false,
  //       'errorMessage' => 'รายการขาย "'.$model->title.'" ถูกยกเลิกแล้ว'
  //     ));
  //   }

  //   if((Auth::guest() || (Auth::check() && (Auth::user()->id != $model->created_by))) && ($model->expiration_date < date('Y-m-d H:i:s'))) {
  //     return json_encode(array(
  //       'success' => false,
  //       'errorMessage' => 'รายการขาย "'.$model->title.'" หมดอายุแล้ว'
  //     ));
  //   }

  //   $blocked = false;
  //   if(Auth::check() && (Auth::user()->id != $model->created_by)) {
  //     $blocked = Service::loadModel('UserBlocking')
  //     ->where(function($q) use($model) {
  //       $q
  //       ->where([
  //         ['model','=','User'],
  //         ['model_id','=',$model->created_by]
  //       ])
  //       ->orWhere([
  //         ['model','=','Item'],
  //         ['model_id','=',$model->id]
  //       ]);
  //     })
  //     ->where('user_id','=',Auth::user()->id)->exists();
  //   }

  //   $result = array(
  //     'success' => true,
  //     'html' => view('pages.item._detail',array(
  //       'data' => $model->buildDataDetail(),
  //       'contact' => $model->getContact(),
  //       'owner' => $model->getOwnerInfo(),
  //       'breadcrumb' => $model->getCategoryBreadcrumb(),
  //       'locations' => $model->getLocationBreadcrumb(),
  //       'hasShop' => $model->hasShop(),
  //       'blocked' => $blocked,
  //       'extendDays' => $model->getExtendDays(),
  //       'expired' => $model->checkIsExpire()
  //     ))->render()
  //   );

  //   return response()->json($result);
  // }
  
  public function add() {

    $this->setData('hasShop',Service::loadModel('Shop')->select('id')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ])->exists());

    $hour = [];
    for ($i=0; $i <= 23; $i++) { 
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

    // $this->setData('themes',ThemeColor::getThemes());
    $this->setData('categories',Service::loadModel('Category')->get());
    $this->setData('dateType',Service::loadModel('Item')->getDateType());

    $this->setData('hour',$hour);
    $this->setData('min',$min);

    $this->randomBanner();

    $this->botDisallowed();
    
    $this->setMeta('title','ขายบัตร ตั๋ว วอชเชอร์ และอื่นๆ — Ticket Easys');

    return $this->view('pages.item.form.add');
  }

  public function addingSubmit(ItemRequest $request) {

    // dd($request->all());

    // $validatedData = Request::validate([
    //   'contact' => 'required',
    //   'ItemToLocation.location_id' => 'required|numeric',
    // ]);

    // if(!$request->has('use_specific_contact')) {
    //   // Verify $request->get('contact')
    // return Redirect::back()->with('msg', 'The Message');
    // }

    // if(!$request->has('use_shop_location')) {
    //   // Verify $request->get('ItemToLocation')
    // }

    $shop = Service::loadModel('Shop')->select('id')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ]);

    $hasShop = $shop->exists();

    $model = Service::loadModel('Item');

    $model->title = preg_replace('/\s+/', ' ', strip_tags($request->get('title')));
    // $model->description = strip_tags($request->get('description'));
    $model->description = $request->get('description');
    $model->price = str_replace(',','',strip_tags($request->get('price')));
    $model->place_location = strip_tags($request->get('place_location'));
    // $model->theme_color_id = $request->get('theme_color_id');
    $model->quantity = 1;
    $model->theme_color_id = 1;

    if($request->has('original_price') && ($request->get('original_price') != null)) {
      $model->original_price = str_replace(',','',strip_tags($request->get('original_price')));
    }

    if($hasShop && $request->has('use_specific_contact')) {
      $model->use_specific_contact = 1;
    }else {
      // $model->contact = strip_tags($request->get('contact'));
      $model->contact = $request->get('contact');
    }

    if($hasShop && $request->has('use_shop_location')) {
      $model->use_shop_location = 1;
    }

    $now = time();
    $model->active_date = date('Y-m-d H:i:s',$now);
    // $model->expiration_date = date('Y-m-d H:i:s',$now+(86400*$model->getExtendDays(Auth::user()->id)));

    // $model->publishing_type = $request->get('publishing_type');
    // $model->grading = $request->get('grading');

    if($request->get('date_1') == 0) {
      $date1 = null;
    }else {
      $date1 = $request->get('date_1').' '.$request->get('start_time_hour').':'.$request->get('start_time_min').':00';
    }

    if($request->get('date_2') == 0) {
      $date2 = null;
    }else {
      $date2 = $request->get('date_2').' '.$request->get('end_time_hour').':'.$request->get('end_time_min').':00';
    }

    // using date
    $model->date_type = $request->get('date_type');
    $model->date_1 = $date1;
    $model->date_2 = $date2;

    $model->approved = 1;

    if($hasShop) {
      $model->shop_id = $shop->first()->id;
    }

    if(!$model->save()) {
      return Redirect::back();
    }

    // Add category to item
    if($request->has('ItemToCategory')) {
      Service::loadModel('ItemToCategory')->__saveRelatedData($model,$request->get('ItemToCategory'));
    }

    // Add location to item
    if($request->has('ItemToLocation') && !$model->use_shop_location) {
      Service::loadModel('ItemToLocation')->__saveRelatedData($model,$request->get('ItemToLocation'));
    }

    // Images
    if($request->has('Image')) {
      Service::loadModel('Image')->__saveRelatedData($model,$request->get('Image'));
    }

    // Banner
    // if($hasShop && $request->has('Banner')) {
    if($request->has('Banner')) {

      $banner = $request->get('Banner');

      if(!empty($banner['filename'])) {
        Service::loadModel('Image')->addImage($model,array('filename' => $banner['filename']),array(
          'token' => $banner['token'],
          'type' => 'cover',
        ));
      }
      
    }

    // Preview
    // if($request->has('Preview')) {

    //   $preview = $request->get('Preview');

    //   if(!empty($preview['filename'])) {
    //     Service::loadModel('Image')->addImage($model,array('filename' => $preview['filename']),array(
    //       'token' => $preview['token'],
    //       'type' => 'preview',
    //     ));
    //   }

    // }

    // re-scrap
    // Service::facebookReScrap('ticket/view/'.$model->id);

    // Hashtag Log
    Service::loadModel('HashtagList')->__saveRelatedData($model,$model->description);

    // Approve queue
    // Service::loadModel('ApproveQueue')->_push($model);

    // User log
    Service::addUserLog('Item',$model->id,'add');

    Snackbar::message('คุณได้เพิ่มสินค้าและรายการขายแล้ว');
    
    // session()->flash('item-adding-success',true);

    return Redirect::to('ticket/v/'.$model->slug);
  }

  public function edit($itemId) {

    $model = Service::loadModel('Item')->where([
      ['id','=',$itemId],
      ['cancel_option','=',0],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($model)) {
      return $this->error('ไม่สามารถแก้ไขรายการขายนี้ได้');
    }

    $_images = $model->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',1)
      ),
      'fields' => array('model','model_id','filename','image_type_id')
    ));

    $images = array();
    if(!empty($_images)) {
      foreach ($_images as $image) {
        $images[] = $image->buildFormData();
      }
    }

    //
    $_banner = $model->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',3)
      ),
      'fields' => array('filename'),
      'first' => true
    ));

    $banner = null;
    if(!empty($_banner)) {
      $banner = $_banner->filename;
    }

    $_preview = $model->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',4)
      ),
      'fields' => array('filename'),
      'first' => true
    ));

    $preview = null;
    if(!empty($_preview)) {
      $preview = $_preview->filename;
    }

    $hour = [];
    for ($i=0; $i <= 23; $i++) { 
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

    // Get Hour and Min

    if(!empty($model->date_1)) {
      $parts = explode(' ', $model->date_1);
      $time = explode(':', $parts[1]);

      $model->date_1 = $parts[0];
      $model->start_time_hour = $time[0];
      $model->start_time_min = $time[1];
    }

    if(!empty($model->date_2)) {
      $parts = explode(' ', $model->date_2);
      $time = explode(':', $parts[1]);

      $model->date_2 = $parts[0];
      $model->end_time_hour = $time[0];
      $model->end_time_min = $time[1];
    }

    $this->setData('data',$model);
    $this->setData('images',json_encode($images));
    $this->setData('banner',$banner);
    $this->setData('preview',$preview);

    $this->setData('categoryId',$model->getCategoryId());
    $this->setData('categoryPaths',json_encode($model->getCategoryPaths()));

    $this->setData('locationId',$model->getLocationId());
    $this->setData('locationPaths',json_encode($model->getLocationPaths()));

    $this->setData('hasShop',Service::loadModel('Shop')->select('id')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ])->exists());

    // $this->setData('themes',ThemeColor::getThemes());
    $this->setData('categories',Service::loadModel('Category')->get());
    $this->setData('dateType',Service::loadModel('Item')->getDateType());

    $this->setData('hour',$hour);
    $this->setData('min',$min);

    $this->setMeta('title','แก้ไขรายการขาย » '.$model->title.' — websiteName');

    $this->botDisallowed();

    return $this->view('pages.item.form.edit');
  }

  public function editingSubmit(ItemRequest $request, $itemId) {

    $model = Service::loadModel('Item')->where([
      ['id','=',$itemId],
      ['cancel_option','=',0],
      ['deleted','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($model) || ($model->created_by != Auth::user()->id)) {
      Snackbar::message('ไม่สามารถแก้ไขรายการขายนี้ได้');
      return Redirect::to('/');
    }

    $hasShop = Service::loadModel('Shop')->select('id')->where([
      ['created_by','=',Auth::user()->id],
      ['deleted','=',0]
    ])->exists();

    $dataChanged = false;

    $floatingData = ['price','original_price'];
    // $booleanData = ['publishing_type','grading'];

    if($model->title != $request->get('title')) {
      $model->title = preg_replace('/\s+/', ' ', strip_tags($request->get('title')));
      $dataChanged = true;
    }

    if($model->description != $request->get('description')) {
      // $model->description = strip_tags($request->get('description'));
      $model->description = $request->get('description');
      $dataChanged = true;
    }

    if($model->place_location != $request->get('place_location')) {
      $model->place_location = strip_tags($request->get('place_location'));
      $dataChanged = true;
    }

    foreach ($floatingData as $value) {
      if($model->{$value} != $request->get($value) && ($request->get($value) != null)) {
        $model->{$value} = str_replace(',','',strip_tags($request->get($value)));
        $dataChanged = true;
      }
    }

    // foreach ($booleanData as $value) {
    //   if($model->{$value} != $request->get($value)) {
    //     $model->{$value} = $request->get($value);
    //     $dataChanged = true;
    //   }
    // }

    // if($model->theme_color_id != $request->get('theme_color_id')) {
    //   $model->theme_color_id = $request->get('theme_color_id');
    //   $dataChanged = true;
    // }

    // $model->theme_color_id = $request->get('theme_color_id');

    if($hasShop && $request->has('use_specific_contact')) {

      if($model->use_specific_contact != 1) {
        $dataChanged = true;
        $model->use_specific_contact = 1;
      }

      $model->contact = '';
      
    }elseif($model->contact != $request->get('contact')) {
      // $model->contact = strip_tags($request->get('contact'));
      $model->contact = $request->get('contact');
      $dataChanged = true;
    }

    if($hasShop && $request->has('use_shop_location')) {

      if($model->use_shop_location != 1) {
        $dataChanged = true;
        $model->use_shop_location = 1;
      }

    }

    if($request->get('date_1') == 0) {
      $date1 = null;
    }else {
      $date1 = $request->get('date_1').' '.$request->get('start_time_hour').':'.$request->get('start_time_min').':00';
    }

    if($request->get('date_2') == 0) {
      $date2 = null;
    }else {
      $date2 = $request->get('date_2').' '.$request->get('end_time_hour').':'.$request->get('end_time_min').':00';
    }

    // using date
    $model->date_type = $request->get('date_type');
    $model->date_1 = $date1;
    $model->date_2 = $date2;

    // Update
    $model->save();

    if(!empty($request->get('ItemToCategory'))) {
      $_data = $request->get('ItemToCategory');

      if($_data['category_id'] != $model->getCategoryId()) {
        $dataChanged = Service::loadModel('ItemToCategory')->__saveRelatedData($model,$request->get('ItemToCategory'));
      }
    }

    if($model->use_shop_location) {
      Service::loadModel('ItemToLocation')->where('item_id','=',$model->id)->delete();
    }elseif(!empty($request->get('ItemToLocation'))) {
      $_data = $request->get('ItemToLocation');

      if($_data['location_id'] != $model->getLocationId()) {
        $dataChanged = Service::loadModel('ItemToLocation')->__saveRelatedData($model,$request->get('ItemToLocation'));
      }
    }

    if($request->has('Image')) {
      
      $changed = false;

      $images = $request->get('Image');

      if(!empty($images['photo']['delete'])) {
        $changed = true;
      }

      if(!empty($images['photo']['images'])) {
        foreach ($images['photo']['images'] as $value) {

          if($changed) {
            break;
          }

          if(!empty($value['filename'])) {
            $changed = true;
          }
        }
      }
      
      if($changed) {
        $dataChanged = Service::loadModel('Image')->__saveRelatedData($model,$request->get('Image'));
      }

    }

    // if($hasShop && $request->has('Banner')) {
    if($request->has('Banner')) {

      $banner = $request->get('Banner');

      if(!empty($banner['delete'])) {
        Service::loadModel('Image')->deleteAllImages($model,array(
          'type' => 'cover'
        ));
        $dataChanged = true;
      }

      if(!empty($banner['filename'])) {

        $_imageExist = Service::loadModel('Image')->where([
          ['filename','=',$banner['filename']],
          ['model','=','Item'],
          ['model_id','=',$model->id],
          ['image_type_id','=',3]
        ])->exists();

        if(!$_imageExist) {
          Service::loadModel('Image')->addImage($model,array('filename' => $banner['filename']),array(
            'token' => $banner['token'],
            'type' => 'cover',
          ));
          $dataChanged = true;
        }
        
      }
    
    }

    // if($request->has('Preview')) {

    //   $preview = $request->get('Preview');

    //   if(!empty($preview['delete'])) {
    //     Service::loadModel('Image')->deleteAllImages($model,array(
    //       'type' => 'preview'
    //     ));
    //     $dataChanged = true;
    //   }
   
    //   // check if exist
    //   if(!empty($preview['filename'])) {

    //     $_imageExist = Service::loadModel('Image')->where([
    //       ['filename','=',$preview['filename']],
    //       ['model','=','Item'],
    //       ['model_id','=',$model->id],
    //       ['image_type_id','=',4]
    //     ])->exists();

    //     if(!$_imageExist) {
    //       Service::loadModel('Image')->addImage($model,array('filename' => $preview['filename']),array(
    //         'token' => $preview['token'],
    //         'type' => 'preview',
    //       ));
    //       $dataChanged = true;
    //     }

    //   }
    
    // }

    if($dataChanged) {
      
      // $model->update(array(
      //   'approved' => 0
      // ));

      // re-scrap
      // Service::facebookReScrap('ticket/view/'.$model->id);

      // Hashtag Log
      Service::loadModel('HashtagList')->__saveRelatedData($model,$model->description);

      // Approve queue
      // Service::loadModel('ApproveQueue')->_push($model);

      // User log
      Service::addUserLog('Item',$model->id,'edit');

      Snackbar::message('รายการขายถูกแก้ไขแล้ว');
    }
    
    return Redirect::to('ticket/v/'.$model->slug);
  }

  public function cancel(Request $request) {

    if(empty($request->itemId)) {
      return Redirect::to('/');
    }

    $model = Service::loadModel('Item')->where([
      ['id','=',$request->itemId],
      ['created_by','=',Auth::user()->id],
      ['cancel_option','=',0],
      ['deleted','=',0],
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถยกเลิกรายการขายนี้ได้');
      return Redirect::to('/');
    }

    $model->update([
      'cancel_option' => $request->cancel_option,
      'closing_reason' => $request->closing_reason,
      'deleted' => 1,
    ]);

    // remove notification (item)
    Service::loadModel('Notification')->where([
      ['model','=','Item'],
      ['model_id','=',$request->itemId],
      // ['receiver_id','=',Auth::user()->id]
    ])->delete();

    // set chat room active to 0
    Service::loadModel('ChatRoom')->where([
      ['model','=','Item'],
      ['model_id','=',$request->itemId],
    ])->update(['active' => 0]);

    // remove blocked items
    Service::loadModel('UserBlocking')->where([
      ['model','=','Item'],
      ['model_id','=',$request->itemId],
    ])->delete();

    // User log
    Service::addUserLog('Item',$request->itemId,'close');

    Snackbar::message('รายการขายของคุณถูกปิดแล้ว');
    return Redirect::to('/account/sale');
  }

  public function pullPost($itemId) {

    if(empty($itemId)) {
      return Redirect::to('/');
    }

    $now = date('Y-m-d H:i:s');

    $model = Service::loadModel('Item')
    ->select('id','title','active_date','expiration_date','cancel_option','deleted')
    ->where([
      ['id','=',$itemId],
      ['created_by','=',Auth::user()->id],
    ])->first();

    if(empty($model)) {
      return $this->error('ไม่สามารถเลื่อนรายการขาย "'.$model->title.'" ได้');
    }

    if($model->deleted == 1) {
      return $this->error('รายการขาย "'.$model->title.'" ถูกลบแล้ว');
    }

    if($model->cancel_option > 0) {
      return $this->error('รายการขาย "'.$model->title.'" ถูกยกเลิกแล้ว');
    }

    // if($model->expiration_date < $now) {
    //   return $this->error('รายการขาย "'.$model->title.'" หมดอายุแล้ว');
    // }

    // check pulling post
    $timeDiff = time() - strtotime($model->active_date);

    if($timeDiff < $model->getRePostPeriodDays()) {
      Snackbar::message('ยังไม่สามารถเลื่อนรายการขายได้ในตอนนี้');
      return Redirect::to('/ticket/view/'.$itemId);
    }

    // Update Activated Date
    $model->active_date = $now;
    $model->save();

    Snackbar::message('รายการขาย "'.$model->getShortTitle().'" ได้เลื่อนขึ้นสู่ตำแหน่งบนแล้ว');
    return Redirect::to('/ticket/view/'.$itemId);

  }

  // public function extendExpire($itemId) {

  //   if(empty($itemId)) {
  //     return Redirect::to('/');
  //   }

  //   $model = Service::loadModel('Item')
  //   ->select('id','title','expiration_date','created_by')
  //   ->where([
  //     ['id','=',$itemId],
  //     ['created_by','=',Auth::user()->id],
  //     ['cancel_option','=',0],
  //     ['deleted','=',0],
  //   ])->first();

  //   if(empty($model)) {
  //     Snackbar::message('ไม่สามารถเลื่อนรายการขายนี้ได้');
  //     return Redirect::to('/');
  //   }

  //   $timeDiff = strtotime($model->expiration_date) - time();

  //   if($timeDiff >= $model->getExpirePeriodDays()) {
  //     Snackbar::message('ยังไม่สามารถต่ออายุรายการขายได้ในตอนนี้');
  //     return Redirect::to('/ticket/view/'.$itemId);
  //   }

  //   $model->update([
  //     'expiration_date' => date('Y-m-d H:i:s',strtotime($model->expiration_date) + (86400 * $model->getExtendDays()))
  //   ]);

  //   Snackbar::message('รายการขาย "'.$model->getShortTitle().'" ต่ออายุเรียบร้อยแล้ว');
  //   return Redirect::to('/ticket/view/'.$itemId);

  // }

  private function getRelatedItem($itemId) {

    $itemModel = Service::loadModel('Item');

    $model = $itemModel->where('id','=',$itemId)->first();

    $now = date('Y-m-d H:i:s');

    $take = 8;

    $relatedItem = $itemModel->where([
      ['id','!=',$itemId],
      ['cancel_option','=',0],
      ['deleted','=',0],
      ['date_2','>=',$now],
      ['created_by','=',$model->created_by],
    ]);

    if($relatedItem->count() > $take) {
      $relatedItem->take($take)->skip(rand(1,$relatedItem->count() - $take));
    }

    $relatedWithShop = [];
    foreach ($relatedItem->get() as $item) {
      $relatedWithShop[] = $item->buildDataList();
    }

    // ====================================================

    $category = Service::loadModel('ItemToCategory')
                ->select('category_id')
                ->where('item_id','=',$itemId)
                ->first();

    $relatedItem = $itemModel->select('items.*')
    ->join('item_to_categories', 'item_to_categories.item_id', '=', 'items.id')
    ->where([
      // ['items.id','!=',$itemId],
      ['items.cancel_option','=',0],
      ['items.deleted','=',0],
      ['items.date_2','>=',$now],
      ['item_to_categories.category_id','=',$category->category_id],
      ['created_by','!=',$model->created_by],
    ]);

    if($relatedItem->count() > $take) {
      $relatedItem->take($take)->skip(rand(1,$relatedItem->count() - $take));
    }

    $relatedWithCategory = [];
    foreach ($relatedItem->get() as $item) {
      $relatedWithCategory[] = $item->buildDataList();
    }

    $this->setData('relatedWithShop',$relatedWithShop);
    $this->setData('relatedWithCategory',$relatedWithCategory);
  }

  public function getItemContact($itemId) {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return response('', 500)->header('Content-Type', 'text/plain');
    }

    $itemModel = Service::loadModel('Item');

    $model = $itemModel->select('contact','shop_id','use_specific_contact')->where('id','=',$itemId)->first();

    if(empty($model)) {
      return  [
        'contact' => ''
      ];
    }

    $userOpenItemContact = Service::loadModel('UserOpenItemContact');
    $userOpenItemContact->item_id = $itemId;

    if(Auth::check()) {
      $userOpenItemContact->user_id = Auth::user()->id;
    }

    $userOpenItemContact->ip_address = Service::getIp();
    $userOpenItemContact->token = Token::generate(32);
    $userOpenItemContact->save();

    return [
      'contact' => $model->getContact()
    ];
  }

  private function getSharingTitle($itemId,$categoryId,$postOwner = false) {

    // if(Auth::check()) {
    //   $name = 'sharing_'.Auth::user()->id.'_'.$itemId;
    //   $minutes = 7200;
    // }else {
    //   $name = 'sharing_'.$itemId;
    //   $minutes = 7200;
    // }

    $name = md5('sharing_'.$itemId);
    // $minutes = 7200;

    $notify = true;

    if(Cookie::get($name)) {
      if(rand(1,100) > 2) {
        $notify = false;
      }
    }elseif(rand(1,100) > 10) {
      $notify = false;
      Cookie::queue($name, 1, 7200);
    }

    if(!$postOwner && $notify) {

      // $titles = [
      //   0 => 'แชร์ไปยังเพื่อนหรือครอบครัวของคุณตอนนี้',
      //   1 => 'เที่ยวไปยังสถานที่ต่างๆกับเพื่อนของคุณ อย่ารอช้าแชร์ไปยังเพื่อนๆของคุณเลย',
      //   2 => 'รออะไรอยู่ล่ะ แชร์ไปยังเพื่อนหรือครอบครัวของคุณเลย',
      //   3 => 'แชร์ไปยังคนที่คุณรู้จักดูสิ',
      //   4 => 'ที่พักนี้เหมาะที่จะไปกันหลายคนถึงสนุก อย่ารอช้าแชร์ไปยังเพื่อนๆของคุณเลย',
      //   5 => 'แชร์ไปยังเพื่อน ครอบครัวของคุณ หรือคนที่คุณรู้จักดฺสิ',
      //   6 => 'ส่งต่อกิจกรรมสนุกๆไปยังเพื่อนของคุณ แชร์เลย!!!',
      //   7 => 'อย่ารอช้าเพื่อนๆของคุณรอคุณอยู่ แชร์เลย!!!',
      //   // 8 => 'ไม่แน่เพื่อนของคุณกำลังคิดจะไปเที่ยวที่นี่อยู่ก็ได้ แชร์ไปยังเพื่อนๆของคุณดูสิ'
      // ];

      // // post owner title
      // // 'แชร์ไปยังเครือข่ายสังคมของคุณดูสิ เพื่อเพิ่มโอกาสการขายได้มากขึ้น'
      // // 'เพื่อเพิ่มโอกาสในการขายได้มากขึ้น แชร์ไปยังเครือข่ายสังคมของคุณดูสิ'

      // $categoryToTitle = [
      //   1 => [2,3,4],
      //   2 => [3,4,5],
      //   3 => [3,4,5],
      //   4 => [3,4,5],
      //   5 => [3,4,5],
      //   6 => [3,4,5],
      //   7 => [3,4,5],
      //   8 => [3,4,5],
      //   9 => [3,4,5],
      //   10 => [3,4,5],
      //   11 => [3,4,5],
      //   12 => [3,4,5],
      //   13 => [3,4,5],
      //   14 => [3,4,5],
      //   15 => [3,4,5]
      // ];

      // $_titles = $categoryToTitle[$categoryId];

      // $title = $titles[rand(0,count($_titles)-1)];

      $titles = [
        'แชร์ไปยังเพื่อนหรือครอบครัวของคุณตอนนี้',
        // 'เที่ยวไปยังสถานที่ต่างๆกับเพื่อนของคุณ อย่ารอช้าแชร์ไปยังเพื่อนๆของคุณเลย',
        'รออะไรอยู่ล่ะ แชร์ไปยังเพื่อนหรือครอบครัวของคุณเลย',
        'แชร์ไปยังคนที่คุณรู้จักดูสิ',
        // 'ที่พักนี้เหมาะที่จะไปกันหลายคนถึงสนุก อย่ารอช้าแชร์ไปยังเพื่อนๆของคุณเลย',
        'แชร์ไปยังเพื่อน ครอบครัวของคุณ หรือคนที่คุณรู้จักดูสิ',
        // 'ส่งต่อกิจกรรมสนุกๆไปยังเพื่อนของคุณ แชร์เลย!!!',
        // 'อย่ารอช้าเพื่อนๆของคุณรอคุณอยู่ แชร์เลย!!!',
        // 'ไม่แน่เพื่อนของคุณกำลังคิดจะไปเที่ยวที่นี่อยู่ก็ได้ แชร์ไปยังเพื่อนๆของคุณดูสิ'
      ];

      $this->setData('_sharingTitle',$titles[rand(0,count($titles)-1)]);
    }

  }

  private function showAricleNotification() {

    $matchingNumber = 50; // 1 - 100%

    $name = md5('article_notification');

    $notify = true;

    if(Cookie::get($name) && (rand(1,100) > $matchingNumber)) {
      $notify = false;
    }else {
      Cookie::queue($name, 1, 720);
    }

    if($notify) {

      // $siteUrl = config('app.blog_site_url');

      // $newsList = [
      //   [
      //     'title' => '69 Milk Bar',
      //     'content' => 'สายหวานต้องไม่พลาด คาเฟ่กึ่งร้านอาหาร ที่การออกแบบ รู้สึกสัมผัสได้ถึงความอบอุ่น มีให้เลือกนั่งชิลทั้ง indoor และ outdoor การตกแต่งที่โดดเด่นเพราะนำ...',
      //     'image' => 'https://ticketeasys.com/blog/wp-content/uploads/2019/01/IMG_0956.jpg',
      //     'link' => 'https://ticketeasys.com/blog/?p=103'
      //   ],
      //   [
      //     'title' => 'หนีหนาว(น้อย)ไปหนาวมาก! ขอเสนอ 5 กระท่อมและโรงแรมน้ำแข็ง ให้ไปพักผ่อนสัมผัสฤดูหนาวอย่างเต็มที่',
      //     'content' => 'ปีใหม่ก็แล้ว… สำหรับใครที่รอลมหนาว(น้อย)ในไทยไม่ไหว หรือยังสัมผัสความหนาวไม่สะใจ...',
      //     'image' => 'https://ticketeasys.com/blog/wp-content/uploads/2019/01/Sorrisniva-Igloo-Hotel-1-700x445.jpg',
      //     'link' => 'https://ticketeasys.com/blog/?p=163'
      //   ]
      // ];

      // $selected = $newsList[rand(0,count($newsList)-1)];

      // // $image = '/assets/images/logo/logo_tn.jpg'; // logo
      // $image = '/assets/images/banner/article.jpg'; // article

      // ToastNotification::show($selected['title'],$selected['content'],$selected['image'],[
      //   'label' => 'อ่าน',
      //   'url' => $selected['link'],
      //   'target' => '_blank'
      // ]);

      $posts = Service::loadModel('WPPost')
      ->select('ID','post_title','post_content','guid')
      ->where([
        ['post_type','=','post'],
        ['post_status','=','publish'],
        ['post_modified','<=',date('Y-m-d H:i:s',time()-(int)(432000 * (rand(0,10)/10)))],
        ['post_author','=',3]
      ])
      ->orderBy('post_modified','desc')
      ->take(3)
      ->get();

      $count = count($posts);

      if($count > 0) {

        $post = $posts[rand(0,$count-1)];

        $meta = Service::loadModel('WPPost')->select('wp_postmeta.meta_value')
        ->join('wp_postmeta', 'wp_postmeta.post_id', '=', 'wp_posts.ID')
        ->where([
          ['wp_posts.post_parent','=',$post->ID],
          ['wp_posts.post_type','=','attachment'],
          ['wp_postmeta.meta_key','=','_wp_attached_file']
        ])
        ->first();

        $image = '';
        if(!empty($meta) && Service::urlExists('https://ticketeasys.com/blog/wp-content/uploads/'.$meta->meta_value)) {
          $image = 'https://ticketeasys.com/blog/wp-content/uploads/'.$meta->meta_value;
        }

        ToastNotification::show(
          $post->post_title,
          stringHelper::truncString($post->post_content,70),
          $image,
          [
            'label' => 'อ่าน',
            'url' => $post->guid,
            'target' => '_blank'
          ]
        );

      }

    }

  }

}
