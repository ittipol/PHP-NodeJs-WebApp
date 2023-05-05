<?php

namespace App\Http\Controllers\Web;

use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\library\service;
use App\library\snackbar;
use App\library\validation;
use App\library\stringHelper;
use Redirect;
use Auth;

class TicketController extends Controller
{
  public function listView(Request $request) {

    $model = Service::loadModel('Ticket')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }
    //set page
    Paginator::currentPageResolver(function() use ($currentPage) {
        return $currentPage;
    });

    // SELECT COUNT(`word_id`) AS total FROM `taggings` GROUP BY `word_id` HAVING total > 36

    $now = date('Y-m-d H:i:s');

    // $_taggings = Service::loadModel('Tagging')
    // ->join('tickets','tickets.id','=','taggings.model_id')
    // ->where([
    //   ['taggings.model','=','Ticket'],
    //   ['tickets.closing_option','=',0],
    // ])
    // ->where(function($query) use ($now) {
    //   $query
    //   ->where('tickets.date_1','=',null)
    //   ->orWhere('tickets.date_1','>=',$now);
    // })
    // ->selectRaw('word_id')
    // ->groupBy('taggings.word_id')
    // ->havingRaw('count(word_id) > 12')
    // ->take(10)
    // ->get();

    // $taggings = array();
    // foreach ($_taggings as $tagging) {
    //   $taggings[] = array(
    //     'word' => $tagging->word->word
    //   );
    // }

    $searching = false;

    if($request->has('q')) {
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

          // $_word = Service::loadModel('Word')->select('id')->where('word','like',$word);
          // if($_word->exists()) {
          //   $wordIds[] = $_word->first()->id;
          // }
        }
      }

      $model->where(function ($query) use ($keywords) {
        foreach ($keywords as $keyword) {
          $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
        }
  
        // if(!empty($wordIds)) {
        //   $query
        //   ->orWhere(function ($query) use ($wordIds) {
        //     $query
        //     ->where('taggings.model','=','Ticket')
        //     ->whereIn('taggings.word_id',$wordIds);
        //   });
        // }
      });

      // if(!empty($wordIds)) {
      //   $model->join('taggings', 'taggings.model_id', '=', 'tickets.id');
      // }
    }

    if($request->has('category')) {
      $searching = true;

      $model
      ->join('ticket_to_categories', 'ticket_to_categories.ticket_id', '=', 'tickets.id')
      ->whereIn('ticket_to_categories.ticket_category_id',$request->category);
    }

    if($request->has('price_start') || $request->has('price_end')) {
      $searching = true;

      $model->where(function ($query) use ($request) {
        if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
          $query->where('tickets.price','>=',$request->price_start);
        }

        if($request->has('price_end') && Validation::isCurrency($request->price_end)) {
          $query->where('tickets.price','<=',$request->price_end);
        }
      });

    }

    $locationSearchingData = null;
    if(!empty($request->get('location'))) {
      $searching = true;

      $model
      ->join('ticket_to_locations', 'ticket_to_locations.ticket_id', '=', 'tickets.id')
      ->where('ticket_to_locations.location_id','=',$request->get('location')); 

      $paths = Service::loadModel('Location')->getLocationPaths($request->get('location'));

      $locationSearchingData = array(
        'id' => $request->get('location'),
        'path' => json_encode($paths)
      );

      $searchData['location'] = $request->get('location');
    }

    if($request->has('start_date') || $request->has('end_date')) {
      $searching = true;

      $model->where(function ($query) use ($request) {

        if($request->has('start_date') && $request->has('end_date')) {
          $query
          ->where([
            ['tickets.date_1','>=',$request->start_date],
            ['tickets.date_1','<=',$request->end_date]
          ])
          ->orWhere([
            ['tickets.date_2','>=',$request->start_date],
            ['tickets.date_2','<=',$request->end_date]
          ]);
        }elseif($request->has('start_date')) {
          $query
          ->where('tickets.date_1','>=',$request->start_date)
          ->orWhere('tickets.date_2','>=',$request->start_date);
        }elseif($request->has('end_date')) {
          $query
          ->where('tickets.date_1','<=',$request->end_date)
          ->orWhere('tickets.date_2','<=',$request->end_date);
        }

      });
    }else{
      // $model->where(function($query) use ($now) {

      //   $query
      //   ->where(function($query) use ($now) {
      //     $query
      //     ->where('tickets.date_1','=',null)
      //     ->orWhere('tickets.date_1','>=',$now);
      //   })
      //   ->orWhere(function($query) use ($now) {
      //     $query
      //     ->where('tickets.date_2','!=',null)
      //     ->where('tickets.date_2','>=',$now);
      //   });

      // });

      $model->where(function($query) use ($now) {

        $query
        ->where(function($query) {

          $query
          ->where('date_type','=',0)
          ->where('tickets.date_1','=',null)
          ->where('tickets.date_2','=',null);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->where('date_type','=',1)
          ->where('tickets.date_2','>=',$now);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->whereIn('date_type', [2,3])
          ->where('tickets.date_1','>=',$now);

        }); 

      });

    }

    $model->where(function($q) {
      $q->where('closing_option','=',0);
    });

    // $model->where(function($query) use ($now) {

    //   $query
    //   ->where(function($query) {

    //     $query
    //     ->where('date_type','=',0)
    //     ->where('tickets.date_1','=',null)
    //     ->where('tickets.date_2','=',null);

    //   })
    //   ->orWhere(function($query) use ($now) {

    //     $query
    //     ->where('date_type','=',1)
    //     ->where('tickets.date_2','>=',$now);

    //   })
    //   ->orWhere(function($query) use ($now) {

    //     $query
    //     ->whereIn('date_type', [2,3])
    //     ->where('tickets.date_1','>=',$now);

    //   }); 
    //   // ->orWhere(function($query) use ($now) {

    //   //   $query
    //   //   ->where(function($query) use ($now) {
    //   //     $query->where('tickets.date_1','!=',null)->where('tickets.date_1','>=',$now);
    //   //   })
    //   //   ->orWhere(function($query) use ($now) {
    //   //     $query->where('tickets.date_2','!=',null)->where('tickets.date_2','>=',$now);
    //   //   });

    //   // });      

    // });

    if($request->has('sort')) {

      switch ($request->sort) {
        case 'post_n':
          $model->orderBy('tickets.activated_date','desc');
          break;
        
        case 'post_o':
          $model->orderBy('tickets.activated_date','asc');
          break;

        case 'price_h':
          $model->orderBy('tickets.price','desc');
          break;

        case 'price_l':
          $model->orderBy('tickets.price','asc');
          break;

        case 'card_date':
          $model->orderBy('tickets.date_1','asc');
          // $model->orderBy('tickets.date_2','asc');
          break;

        default:
          $model->orderBy('tickets.activated_date','desc');
          break;
      }

    }else {
      $model->orderBy('tickets.activated_date','desc');
    }

    if($searching && $request->has('q') && empty($keywords)) {
      $this->setData('data',array());
    }else{
      $this->setData('data',$model->paginate(48));
    }

    // $this->setData('taggings',$taggings);
    $this->setData('categories',Service::loadModel('TicketCategory')->get());
    $this->setData('locationSearchingData',$locationSearchingData);
    $this->setData('search',$searching);

    if($searching) {
      $this->setMeta('title','การค้นหาบน Ticket Easys');
    }

    return $this->view('pages.ticket.ajax_list');
  }

  public function detail($ticketId) {

    $now = strtotime(date('Y-m-d H:i:s'));

    $model = Service::loadModel('Ticket')->where([
      ['id','=',$ticketId]
      // ['closing_option','=',0]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่พบรายการนี้');
      return Redirect::to('/ticket');
    }

    $data = $model->buildDataDetail();

    $this->setData('data',$data);
    $this->setData('ticketId',$ticketId);
    $this->setData('seller',Service::loadModel('User')->buildProfileForTicketDetail($model->created_by));

    $this->setData('locations',$model->getLocationBreadcrumb());

    // Modal use for shared.ig with twitter title
    // $this->setData('_text',$data['title']);

    // SET META
    $this->setMeta('title',$model->title);
    $this->setMeta('description',$model->getShortDesc());

    if(!empty($data['images'])) {
      $this->setMeta('image',url('/').$data['images'][0]['_url']);
    }

    $keywords = array();
    if(!empty($data['tags'])) {
      foreach ($data['tags'] as $tag) {
        $keywords[] = $tag['word'];
      }
    }

    $this->setMeta('keywords',implode(',',$keywords));

    return $this->view('pages.ticket.detail');
  }

  public function add() {

    $model = Service::loadModel('Ticket');

    $this->setData('categories',Service::loadModel('TicketCategory')->get());
    $this->setData('dateType',$model->getDateType());

    $this->setMeta('title','ขายบัตร — Ticket Easys');

    return $this->view('pages.ticket.form.add');
  }

  public function addingSubmit(Request $request) {

    $model = Service::loadModel('Ticket');

    // create slug

    $model->title = strip_tags($request->get('title'));
    $model->description = strip_tags($request->get('description'));
    $model->place_location = strip_tags($request->get('place_location'));
    $model->price = str_replace(',','',strip_tags($request->get('price')));

    if($request->has('original_price')) {
      $model->original_price = str_replace(',','',strip_tags($request->get('original_price')));
    }
    
    $model->date_type = $request->get('date_type');

    if($request->get('date_type') > 0) {
      $model->date_1 = $request->get('date_1');
      $model->date_2 = $request->get('date_2');
    }

    $model->contact = strip_tags($request->get('contact'));
    $model->activated_date = date('Y-m-d H:i:s');
    
    $model->purpose = 's'; // sell
   
    if(!$model->save()) {
      return Redirect::back();
    }

    // Add category to ticket
    if(!empty($request->get('TicketToCategory'))) {
      Service::loadModel('TicketToCategory')->__saveRelatedData($model,$request->get('TicketToCategory'));
    }

    // Tagging
    // if(!empty($request->get('Tagging'))) {
    //   Service::loadModel('Tagging')->__saveRelatedData($model,$request->get('Tagging'));
    // }

    // images
    if(!empty($request->get('Image'))) {
      Service::loadModel('Image')->__saveRelatedData($model,$request->get('Image'));
    }

    // save Place location
    if(!empty($request->get('place_location'))) {
      Service::loadModel('PlaceLocation')->__saveRelatedData($model,$request->get('place_location'));
    }

    if($request->has('TicketToLocation')) {
      Service::loadModel('TicketToLocation')->__saveRelatedData($model,$request->get('TicketToLocation'));
    }
    
    // Lookup

    // re-scrap
    // Service::facebookReScrap('ticket/view/'.$model->id);

    // Hashtag Log
    Service::loadModel('HashtagList')->__saveRelatedData($model,$model->description);

    // User log
    Service::addUserLog('Ticket',$model->id,'add');

    Snackbar::message('รายการของคุณได้ถูกเพิ่มแล้ว');
    return Redirect::to('ticket/view/'.$model->id);
    
  }

  public function edit($ticketId) {

    $model = Service::loadModel('Ticket')->where([
      ['id','=',$ticketId],
      ['closing_option','=',0],
      ['created_by','=',Auth::user()->id]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถแก้ไขรายการนี้ได้');
      return Redirect::to('/ticket');
    }

    $ticketCategoryId = null;

    $_ticketCategoryId = $model->getRelatedData('TicketToCategory',array(
      'fields' => array('ticket_category_id'),
      'first' => true
    ));

    if(!empty($_ticketCategoryId)) {
      $ticketCategoryId = $_ticketCategoryId->ticket_category_id;
    }

    $taggings = $model->getRelatedData('Tagging', array(
      'fields' => array('word_id')
    ));

    $words = array();
    foreach ($taggings as $tagging) {
      $words[] = $tagging->buildFormData();
    }

    $_images = $model->getRelatedData('Image',array(
      'fields' => array('id','model','model_id','filename','description','image_type_id')
    ));

    $images = array();
    if(!empty($_images)) {
      foreach ($_images as $image) {
        $images[] = $image->buildFormData();
      }
    }

    $model['TicketToCategory'] = array(
      'ticket_category_id' => $ticketCategoryId
    );

    $this->setData('data',$model);
    $this->setData('images',json_encode($images));
    $this->setData('taggings',json_encode($words));
    $this->setData('categories',Service::loadModel('TicketCategory')->get());
    $this->setData('dateType',$model->getDateType());

    $this->setData('locationId',$model->getLocationId());
    $this->setData('locationPaths',json_encode($model->getLocationPaths()));

    $this->setMeta('title','แก้ไขรายการ » '.$model->title.' — Ticket Easys');

    $this->botDisallowed();

    return $this->view('pages.ticket.form.edit');
  }

  public function editingSubmit(Request $request, $ticketId) {

    $model = Service::loadModel('Ticket')->find($ticketId);

    if(empty($model) || ($model->created_by != Auth::user()->id)) {
      Snackbar::message('ไม่สามารถแก้ไขรายการนี้ได้');
      return Redirect::to('/ticket');
    }

    $model->title = strip_tags($request->get('title'));
    $model->description = strip_tags($request->get('description'));
    $model->place_location = strip_tags($request->get('place_location'));
    $model->price = str_replace(',','',strip_tags($request->get('price')));

    if($request->has('original_price')) {
      $model->original_price = str_replace(',','',strip_tags($request->get('original_price')));
    }

    $model->date_type = $request->get('date_type');

    if($request->get('date_type') == 0) {
      $model->date_1 = null;
      $model->date_2 = null;
    }else{
      $model->date_1 = $request->get('date_1');
      $model->date_2 = $request->get('date_2');
    }
    
    $model->contact = strip_tags($request->get('contact'));
    
    if(!$model->save()) {
      return Redirect::back();
    }

    // Add category to ticket
    if(!empty($request->get('TicketToCategory'))) {
      Service::loadModel('TicketToCategory')->__saveRelatedData($model,$request->get('TicketToCategory'));
    }

    // Tagging
    // if(!empty($request->get('Tagging'))) {
    //   $taggingModel = Service::loadModel('Tagging');
    //   $taggingModel->__saveRelatedData($model,$request->get('Tagging'));
    // }

    // images
    if(!empty($request->get('Image'))) {
      $imageModel = Service::loadModel('Image');
      $imageModel->__saveRelatedData($model,$request->get('Image'));
    }

    // save Place location
    if(!empty($request->get('place_location'))) {
      $placeLocationModel = Service::loadModel('PlaceLocation');
      $placeLocationModel->__saveRelatedData($model,$request->get('place_location'));
    }

    if(!empty($request->get('TicketToLocation'))) {
      $_data = $request->get('TicketToLocation');

      if($_data['location_id'] != $model->getLocationId()) {
        Service::loadModel('TicketToLocation')->__saveRelatedData($model,$request->get('TicketToLocation'));
      }
    }

    // Lookup

    // re-scrap
    // Service::facebookReScrap('ticket/view/'.$model->id);

    // Hashtag Log
    Service::loadModel('HashtagList')->__saveRelatedData($model,$model->description);

    // User log
    Service::addUserLog('Ticket',$model->id,'edit');

    Snackbar::message('รายการได้ถูกแก้ไขแล้ว');
    return Redirect::to('ticket/view/'.$model->id);
  }

  public function close(Request $request) {

    if(empty($request->ticketId)) {
      return Redirect::to('/');
    }

    $model = Service::loadModel('Ticket')->where([
      ['id','=',$request->ticketId],
      ['created_by','=',Auth::user()->id],
      ['closing_option','=',0]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถปิดรายการขายนี้ได้');
      return Redirect::to('/ticket');
    }

    $model->update([
      'closing_option' => $request->closing_option,
      'closing_reason' => $request->closing_reason
    ]);

    // User log
    Service::addUserLog('Ticket',$request->ticketId,'close');

    Snackbar::message('รายการขายของคุณถูกปิดแล้ว');
    return Redirect::to('/account/ticket');
    
  }

  public function pullPost($ticketId) {

    if(empty($ticketId)) {
      return Redirect::to('/');
    }

    $model = Service::loadModel('Ticket')->where([
      ['id','=',$ticketId],
      ['created_by','=',Auth::user()->id],
      ['closing_option','=',0]
    ])->first();

    if(empty($model)) {
      Snackbar::message('ไม่สามารถดึงรายการขายนี้ได้');
      return Redirect::to('/ticket');
    }

    $now = date('Y-m-d H:i:s');

    // check pulling post
    $timeDiff = strtotime($now) - strtotime($model->activated_date);

    if($timeDiff < $model->getRePostDays()) {
      Snackbar::message('ยังไม่สามารถดึงรายการขายได้ในตอนนี้');
      return Redirect::to('/ticket/view/'.$ticketId);
    }

    // Update Activated Date
    $model->activated_date = $now;
    $model->save();

    Snackbar::message('รายการขายของคุณถูกดึงไปยังหน้าแรกแล้ว');
    return Redirect::to('/ticket/view/'.$ticketId);

  }

  public function _list(Request $request) {

    // if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    //   return response('', 500)->header('Content-Type', 'text/plain');
    // }

    $model = Service::loadModel('Ticket')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }
    //set page
    Paginator::currentPageResolver(function() use ($currentPage) {
        return $currentPage;
    });

    $now = date('Y-m-d H:i:s');

    $searching = false;

    if($request->has('q')) {
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

          // $_word = Service::loadModel('Word')->select('id')->where('word','like',$word);
          // if($_word->exists()) {
          //   $wordIds[] = $_word->first()->id;
          // }
        }
      }

      $model->where(function ($query) use ($keywords) {
        foreach ($keywords as $keyword) {
          $query->orWhere($keyword[0], $keyword[1], $keyword[2]);
        }
    
        // if(!empty($wordIds)) {
        //   $query
        //   ->orWhere(function ($query) use ($wordIds) {
        //     $query
        //     ->where('taggings.model','=','Ticket')
        //     ->whereIn('taggings.word_id',$wordIds);
        //   });
        // }
      });

      // if(!empty($wordIds)) {
      //   $model->join('taggings', 'taggings.model_id', '=', 'tickets.id');
      // }
    }

    if($request->has('category')) {
      $searching = true;

      $model
      ->join('ticket_to_categories', 'ticket_to_categories.ticket_id', '=', 'tickets.id')
      ->whereIn('ticket_to_categories.ticket_category_id',$request->category);
    }

    if($request->has('price_start') || $request->has('price_end')) {
      $searching = true;

      $model->where(function ($query) use ($request) {
        if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
          $query->where('tickets.price','>=',$request->price_start);
        }

        if($request->has('price_end') && ($request->price_end > 0) && Validation::isCurrency($request->price_end)) {
          $query->where('tickets.price','<=',$request->price_end);
        }
      });

    }

    // $locationSearchingData = null;
    if(!empty($request->get('location'))) {
      $searching = true;

      $model
      ->join('ticket_to_locations', 'ticket_to_locations.ticket_id', '=', 'tickets.id')
      ->where('ticket_to_locations.location_id','=',$request->get('location')); 

    }

    if($request->has('start_date') || $request->has('end_date')) {
      $searching = true;

      $model->where(function ($query) use ($request) {

        if(($request->has('start_date') && ($request->get('start_date') != null)) 
          && 
          ($request->has('end_date') && ($request->get('end_date') != null))) {

          $query
          ->where([
            ['tickets.date_1','>=',$request->start_date],
            ['tickets.date_1','<=',$request->end_date]
          ])
          ->orWhere([
            ['tickets.date_2','>=',$request->start_date],
            ['tickets.date_2','<=',$request->end_date]
          ]);
        }elseif($request->has('start_date') && ($request->get('start_date') != null)) {
          $query
          ->where('tickets.date_1','>=',$request->start_date)
          ->orWhere('tickets.date_2','>=',$request->start_date);
        }elseif($request->has('end_date') && ($request->get('end_date') != null)) {
          $query
          ->where('tickets.date_1','<=',$request->end_date)
          ->orWhere('tickets.date_2','<=',$request->end_date);
        }

      });
    }else{

      $model->where(function($query) use ($now) {

        $query
        ->where(function($query) {

          $query
          ->where('date_type','=',0)
          ->where('tickets.date_1','=',null)
          ->where('tickets.date_2','=',null);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->where('date_type','=',1)
          ->where('tickets.date_2','>=',$now);

        })
        ->orWhere(function($query) use ($now) {

          $query
          ->whereIn('date_type', [2,3])
          ->where('tickets.date_1','>=',$now);

        }); 

      });

    }

    $model->where(function($q) {
      $q->where('closing_option','=',0);
    });

    if($request->has('sort')) {

      switch ($request->sort) {
        case 'post_n':
          $model->orderBy('tickets.activated_date','desc');
          break;
        
        case 'post_o':
          $model->orderBy('tickets.activated_date','asc');
          break;

        case 'price_h':
          $model->orderBy('tickets.price','desc');
          break;

        case 'price_l':
          $model->orderBy('tickets.price','asc');
          break;

        case 'card_date':
          $model->orderBy('tickets.date_1','asc');
          // $model->orderBy('tickets.date_2','asc');
          break;

        default:
          $model->orderBy('tickets.activated_date','desc');
          break;
      }

    }else {
      $model->orderBy('tickets.activated_date','desc');
    }

    $data = array();

    if(!$searching || (!$request->has('q') || ($request->get('q') == null)) || !empty($keywords)) {
      $data = $model->paginate(48);
    }
dd($data);
    $next = true;
    if($request->page == $data->lastPage()) {
      $next = false;
    }

    $html = '';
    $hasData = true;

    if(!empty($data) && ($data->currentPage() <= $data->lastPage())) {
      $html = view('pages.ticket._ticket_list',array(
        'data' => $data,
        'now' => time()
      ))->render();
    }elseif($searching) {
      // Display searching not found
      $hasData = false;
      $html = '<div class="tc mv5 pa3 pa0-ns white">
        <h3 class="dark-gray">ไม่พบรายการที่กำลังค้นหา</h3>
        <p class="dark-gray">โปรดลองค้นหาอีกครั้ง ด้วยคำที่แตกต่างหรือคำที่มีความหมายใกล้เคียง</p>
      </div>';
    }else {
      // Display ticket not found
      $hasData = false;
      $html = '<div class="tc mv5 pa3 pa0-ns white">
        <h3 class="dark-gray">ยังไม่มีรายการขายบัตร</h3>
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
}
