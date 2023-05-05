<?php

namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $data = [];
  protected $pageName = null; 
  protected $meta = [
    'title' => 'Ticket Easys | บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่',
    // 'description' => 'เว็บไซต์ที่ให้คุณซื้อและขายบัตรงานแสดงต่างๆ ได้ด้วยตัวคุณเอง โดยคุณเป็นผู้ตั้งราคา',
    // 'description' => 'บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่',
    'description' => 'แพ็กเกจ ที่พัก ท่องเที่ยว จากธุรกิจของคุณ หรือ บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่',
    'image' => 'https://Ticket Easys.com/assets/images/logo/logo_tn_l.jpg',
    'keywords' => 'ซื้อ,ขาย,บัตรคอนเสิร์ต,ตั๋ว,วอชเชอร์,voucher,ticket,marketplace,community,แพ็กเกจ,package,tour.shop,ร้าน,สินค้า'
  ];

  protected $botDisallowed = false;

  protected function botDisallowed() {
    $this->botDisallowed = true;
  }

  protected function setMeta($type,$value = null) {
    
    if(empty($value)) {
      return false;
    }

    $this->meta[$type] = $value;

  }

  protected function setPagination(&$model,$perPage = null) {
    if(empty($perPage)) {
      $perPage = 24;
    }

    $model = $model->paginate($perPage);
  }

  protected function setFilter(&$model,$filter = []) {
    
    // Set query string to pagination
    foreach ($filter as $key => $value) {
      $model->appends([$key => $value]);
    }

    $this->setData('filter',$filter);
  }

  protected function setData($index,$value) {
    $this->data[$index] = $value;
  }

  protected function error($message) {

    $data = array(
      'message' => $message
    );

    return view('errors.error',$data);
  }

  protected function view($view = null) {

    // Request::fullUrl()
    // Request::url()
    // Request::ip()

    $this->data['_pageName'] = $this->pageName;
    $this->data['_meta'] = $this->meta;
    $this->data['_bot'] = $this->botDisallowed;

    return view($view,$this->data);
  }

  protected function randomBanner($size = 'sm') {

    $file = '';
    $name = '__banner__';

    switch ($size) {
      case 'sm':
        
        $name .= 'sm__'; 
        $file = '/assets/images/banner/sm/fbn'.rand(1,10).'.jpg';

        break;

      case 'xl':
        
        $name .= 'xl__';
        $file = '/assets/images/banner/xl/fbn'.rand(1,10).'.jpg';

        break;
      
    }

    $this->setData($name,$file);
  }
}
