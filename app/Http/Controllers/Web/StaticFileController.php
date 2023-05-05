<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Models\Image;
use App\library\service;
use App\library\cache;
use Auth;
// use File;
use Response;

class StaticFileController extends Controller
{
  private $noImagePath = 'assets/images/common/no-img.svg';

  public function __construct() {
    $this->botDisallowed();
  }

  public function serveImages($filename){

    $cache = new Cache;
    $path = $cache->getCacheImagePath($filename);

    if(!empty($path)) {

      $headers = array(
        // 'Pragma' => 'no-cache',
        // 'Cache-Control' => 'no-cache, must-revalidate',
        // 'Cache-Control' => 'pre-check=0, post-check=0, max-age=0',
        'Cache-Control' => 'public, max-age=86400',
        'Content-Type' => mime_content_type($path),
        // 'Content-length' => filesize($path),
      );

      return Response::make(file_get_contents($path), 200, $headers);
    }

    $image = Image::where('filename','like',$filename)
    ->select(array('model','model_id','filename','image_type_id'))
    ->first();

    if(empty($image)) {
      // return Response::make(file_get_contents($this->noImagePath), 200, $headers);
      return response()->download($this->noImagePath, null, [], null);
    }

    $path = $image->getImagePath();

    if(file_exists($path)){

      $headers = array(
        // 'Pragma' => 'no-cache',
        // 'Cache-Control' => 'no-cache, must-revalidate',
        // 'Cache-Control' => 'pre-check=0, post-check=0, max-age=0',
        'Cache-Control' => 'public, max-age=604800',
        'Content-Type' => mime_content_type($path),
        // 'Content-length' => filesize($path),
      );

      return Response::make(file_get_contents($path), 200, $headers);

    }

    return response()->download($this->noImagePath, null, [], null);

  }

  public function userAvatar($userId = null,$filename = null){

    if($userId === 'f') {
      $image = Image::select('model','model_id','filename','image_type_id')->where([['model','=','User'],['filename','=',$filename]]);
    }elseif(is_numeric($userId)) {
      $user = User::select('id')->find($userId);

      if(empty($user)) {
        return null;
      }

      $image = $user->getRelatedData('Image',array(
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));

    }elseif(Auth::check()) {
      $user = User::select('id')->find(Auth::user()->id);

      if(empty($user)) {
        return null;
      }

      $image = $user->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',2)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));

    }else{
      return null;
    }

    $path = null;
    if(!empty($image)) {
      if(request()->has('o')) {
        $path = $image->getImagePath();
      }else {
        $cache = new Cache;
        $path = $cache->getCacheImageUrl($image,'avatar_sm',true);
      }
    }

    if(file_exists($path)){
      $headers = array(
        'Pragma-directive' => 'no-cache',
        'Cache-directive' => 'no-store',
        'Cache-control' => 'no-cache',
        'Pragma' => 'no-cache',
        'Expires' => '0',
        'Content-Type' => mime_content_type($path),
      );

      return Response::make(file_get_contents($path), 200, $headers);
    }

    if(request()->has('d')) {
      return response()->download('assets/images/common/avatar.png', null, [], null);
    }
    
    return null;
  }

  public function shopAvatar($slug){

    // Get Shop form slug
    $shop = Service::loadModel('Shop')->select('id')->where([
      ['slug','=',$slug],
      ['deleted','=',0]
    ])->first();

    if(empty($shop)) {
      return null;
    }

    $image = $shop->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',2)
      ),
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

    $path = null;
    if(!empty($image)) {
      if(request()->has('o')) {
        $path = $image->getImagePath();
      }else {
        $cache = new Cache;
        $path = $cache->getCacheImageUrl($image,'avatar_md',true);
      }
    }

    if(file_exists($path)){
      $headers = array(
        'Pragma-directive' => 'no-cache',
        'Cache-directive' => 'no-store',
        'Cache-control' => 'no-cache',
        'Pragma' => 'no-cache',
        'Expires' => '0',
        'Content-Type' => mime_content_type($path),
      );

      return Response::make(file_get_contents($path), 200, $headers);
    }

    if(request()->has('d')) {
      return response()->download('assets/images/common/shop.png', null, [], null);
    }

    return null;
  }

  public function temp($filename) {

    $image = Image::where('filename','like',$filename)
    ->select(array('model','token','image_type_id'))
    ->first();

    if(empty($image)) {
      return null;
    }

    $path = $image->getTemporyPath().$filename;

    if(file_exists($path)){

      $headers = array(
        'Pragma' => 'no-cache',
        'Cache-Control' => 'no-cache, must-revalidate',
        'Cache-Control' => 'pre-check=0, post-check=0, max-age=0',
        'Content-Type' => mime_content_type($path),
      );

      return Response::make(file_get_contents($path), 200, $headers);

    }

    return null;

  }

}
