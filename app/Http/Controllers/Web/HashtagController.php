<?php

namespace App\Http\Controllers\Web;

use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use App\library\service;
use App\library\validation;
use App\library\snackbar;
use Redirect;
use Auth;

class HashtagController extends Controller
{
  public function index(Request $request, $hashTag) {

    $model = Service::loadModel('Item')->query();

    $currentPage = 1;
    if($request->has('page')) {
      $currentPage = $request->page;
    }
    //set page
    Paginator::currentPageResolver(function() use ($currentPage) {
        return $currentPage;
    });


    $hashtag = trim(strip_tags($hashTag));

    if(empty($hashtag)) {
      return $this->error('ไม่พบ Hashtag');
    }

    // if(substr($_q, 0, 1) !== '#') {$hashTag = '#'.trim($hashTag);}

    $hashTag = '#'.trim($hashTag);

    // $searchKeywords = array();
    $searching = false;

    $locationSearchingData = null;
    if(!empty($request->get('location'))) {
      $searching = true;

      $model
      ->join('item_to_locations', 'item_to_locations.item_id', '=', 'items.id')
      ->where('item_to_locations.location_id','=',$request->get('location')); 

      $paths = Service::loadModel('Location')->getLocationPaths($request->get('location'));

      $locationSearchingData = array(
        'id' => $request->get('location'),
        'path' => json_encode($paths)
      );

      $searchData['location'] = $request->get('location');
    }

    if(!empty($request->get('price_start')) || !empty($request->get('price_end'))) {
      $searching = true;

      $conditions = array();

      if($request->has('price_start') && Validation::isCurrency($request->price_start)) {
        $conditions[] = array('items.price','>=',$request->price_start);
        $searchData['price_start'] = $request->get('price_start');
      }

      if($request->has('price_end') && Validation::isCurrency($request->price_end)) {
        $conditions[] = array('items.price','<=',$request->price_end);
        $searchData['price_end'] = $request->get('price_end');
      }

      if(!empty($conditions)) {
        $model->where(function ($query) use ($conditions) {
          $query->where($conditions);
        });
      }
    }

    // if(($request->has('user') && ($request->get('user') == 1)) && ($request->has('shop') && ($request->get('shop') == 1))) {
    //   $searching = true;
    // }elseif($request->has('user') && ($request->get('user') == 1)) {
    //   $searching = true;
    //   $model->where('items.shop_id','=',null);
    //   $searchData['from'] = 1;
    // }elseif($request->has('shop') && ($request->get('shop') == 1)) {
    //   $searching = true;
    //   $model->where('items.shop_id','!=',null);
    //   $searchData['from'] = 2;
    // }

    // if(($request->has('sell') && ($request->get('sell') == 1)) && ($request->has('buy') && ($request->get('buy') == 1))) {
    //   $searching = true;
    // }elseif(!empty($request->get('sell'))) {
    //   $searching = true;
    //   $model->where('items.publishing_type','=',1);
    //   $searchData['publishing'] = 1;
    // }elseif(!empty($request->get('buy'))) {
    //   $searching = true;
    //   $model->where('items.publishing_type','=',2);
    //   $searchData['publishing'] = 2;
    // }

    // if($request->has('new') || $request->has('old') || $request->has('homemade')) {

    //   if(($request->get('new') == 1) && ($request->get('old') == 1) && ($request->get('homemade') == 1)) {
    //     $searching = true;
    //   }else {

    //     $searching = true;

    //     $_sqlArr = array();

    //     if(!empty($request->get('new'))) {
    //       $_sqlArr[] = 1;
    //     }

    //     if(!empty($request->get('old'))) {
    //       $_sqlArr[] = 2;
    //     }
        
    //     if(!empty($request->get('homemade'))) {
    //       $_sqlArr[] = 3;
    //     }

    //     $model->where(function($q) use($_sqlArr) {
    //       foreach ($_sqlArr as $value) {
    //         $q->orWhere('items.grading','=',$value);
    //       }
    //     });

    //     $searchData['item'] = $_sqlArr;
    //   }
    // }

    $blockedData = null;
    if(Auth::check()) {

      $blockedData = Service::loadModel('UserBlocking')->getBlockedData(Auth::user()->id);

      $model->where(function($q) use($blockedData) {

        // if(!empty($blockedData['user']) && !empty($blockedData['item'])) {
        //   $q
        //   ->whereNotIn('items.created_by',$blockedData['user'])
        //   ->orWhereNotIn('items.id',$blockedData['item']);
        // }elseif(!empty($blockedData['user'])) {
        //   $q->whereNotIn('items.created_by',$blockedData['user']);
        // }elseif(!empty($blockedData['item'])) {
        //   $q->whereNotIn('items.created_by',$blockedData['item']);
        // }



        // if(Auth::user()->upgraded && !empty($blockedData['shop'])) {
        //   $q->whereNotIn('items.shop_id',$blockedData['shop']);
        // }elseif(!empty($blockedData['user'])) {
        //   $q->whereNotIn('items.created_by',$blockedData['user']);
        // }


        if(!empty($blockedData['user'])) {
          $q->whereNotIn('items.created_by',$blockedData['user']);
        }

        if(!empty($blockedData['item'])) {
          $q->whereNotIn('items.id',$blockedData['item']);
        }
    
      });

    }

    // Get Hashtag By hashtag list
    // if not use this method, delele it
    $model
    ->join('hashtag_lists', 'hashtag_lists.model_id', '=', 'items.id')
    ->join('hashtags', 'hashtags.id', '=', 'hashtag_lists.hashtag_id')
    ->where([
      ['hashtags.hashtag','=',$hashTag],
      ['hashtag_lists.model','=','Item'],
      ['cancel_option','=',0],
      // ['expiration_date','>',date('Y-m-d H:i:s')],
      ['approved','=',1],
      ['deleted','=',0]
    ]);
    // end ===========================================

    // $model->where(function($q) use($hashTag) {
    //   $q->where([
    //     // ['description','like','%'.$hashTag.'%'], // search by desc
    //     ['cancel_option','=',0],
    //     ['expiration_date','>',date('Y-m-d H:i:s')],
    //     ['approved','=',1],
    //     ['deleted','=',0]
    //   ]);
    // });

    $model->orderBy('items.active_date','desc')->select('items.*')->distinct('items.id');

    $this->setPagination($model,48);
    $this->setFilter($model,$request->all());

    $this->setData('data',$model);
    // $this->setData('data',$model->select('items.*')->distinct('items.id')->paginate(48));
    $this->setData('hashtag',$hashTag);
    $this->setData('searching',$searching);
    // $this->setData('searchKeywords',$searchKeywords);

    $this->setData('locationSearchingData',$locationSearchingData);

    $this->setMeta('title',$hashTag.' | Ticket Easys');

    return $this->view('pages.hashtag.list');

  }
}
