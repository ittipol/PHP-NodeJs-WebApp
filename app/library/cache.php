<?php

namespace App\library;

use File;

class Cache
{
  private $cachePath = 'cache/';
  private $imageCache = array(
    'avatar_sm' => array(
      'width' => 50,
      'height' => 50,
      'fx' => 'getImageSizeByRatio',
    ),
    'avatar_md' => array(
      'width' => 150,
      'height' => 150,
      'fx' => 'getImageSizeByRatio',
    ),
    // 'xs' => array(
    //   'width' => 50,
    //   'height' => 50,
    //   'fx' => '',
    // ),
    // 'sm' => array(
    //   'width' => 100,
    //   'height' => 100,
    //   'fx' => '',
    // ),
    // 'sm_list' => array(
    //   'width' => 100,
    //   'height' => 100,
    //   'fx' => 'getImageSizeByRatio',
    // ),
    'xsm_scale' => array(
      'width' => 100,
      'height' => 100,
      'fx' => 'getImageSizeByRatio',
    ),
    'sm_scale' => array(
      'width' => 200,
      'height' => 200,
      'fx' => 'getImageSizeByRatio',
    ),
    'card_scale' => array(
      'width' => 300,
      'height' => 300,
      'fx' => 'getImageSizeByRatio',
    ),
    'md_scale' => array(
      'width' => 400,
      'height' => 400,
      'fx' => 'getImageSizeByRatio',
    ),
    'list_pw_scale' => array(
      'width' => 600,
      'height' => 600,
      'fx' => 'getImageSizeByRatio',
    ),
    // 'list' => array(
    //   'width' => 380,
    //   'height' => 380,
    //   'fx' => 'getImageSizeByRatio',
    // ),
  );

  public function __construct($image = null) {
    $this->cachePath = Url::addSlash(storage_path($this->cachePath));
  }

  public function getCacheImagePath($filename) {

    $parts = explode('_', $filename);
    $cacheFile = Url::addSlash($this->cachePath.$parts[0]).$filename;

    if(!file_exists($cacheFile)) {
      return false;
    }

    return $cacheFile;

  }

  public function getCacheImageUrl($model,$alias,$getPath = false) {

    if(empty($model)) {
      return null;
    }

    $path = $model->getImagePath();

    if(!file_exists($path) || empty($this->imageCache[$alias])){
      return null;
    }

    $ext = pathinfo($model->filename, PATHINFO_EXTENSION);
    $filename = pathinfo($model->filename, PATHINFO_FILENAME);
    list($originalWidth,$originalHeight) = getimagesize($path);

    $value = $this->imageCache[$alias];

    $width = $value['width'];
    $height = $value['height'];

    if(!empty($value['fx'])) {
      list($width,$height) = $this->getImageSizeByRatio($originalWidth,$originalHeight,$width,$height);
    }

    $newFilename = $filename.'_'.$width.'x'.$height.'.'.$ext;
    $cachePath = Url::addSlash($this->cachePath.$filename);
    $cacheFile = $cachePath.$newFilename;

    if(!file_exists($cacheFile) && !$this->_cacheImage($path,$width,$height,$cachePath,$cacheFile)) {
      return false;
    }

    if($getPath) {
      return $this->cachePath.$filename.'/'.$newFilename;
    }

    return '/get_image/'.$newFilename;
  }

  private function _cacheImage($path,$width,$height,$cachePath,$cacheFile) {

    if(!is_dir($cachePath)){
      mkdir($cachePath,0777,true);
    }

    $imageLib = new ImageTool($path);
    $imageLib->resize($width,$height);
    return $imageLib->save($cacheFile);

  }

  public function cacheImage($model,$alias) {

    if(empty($model)) {
      return null;
    }

    $path = $model->getImagePath();

    if(!file_exists($path) || empty($this->imageCache[$alias])){
      return null;
    }

    $ext = pathinfo($model->filename, PATHINFO_EXTENSION);
    $filename = pathinfo($model->filename, PATHINFO_FILENAME);
    list($originalWidth,$originalHeight) = getimagesize($path);

    $value = $this->imageCache[$alias];

    $width = $value['width'];
    $height = $value['height'];

    if(!empty($value['fx']) && method_exists($this,$value['fx'])) {
      list($width,$height) = $this->getImageSizeByRatio($originalWidth,$originalHeight,$width,$height);
    }

    $cachePath = Url::addSlash($this->cachePath.$filename);
    if(!is_dir($cachePath)){
      mkdir($cachePath,0777,true);
    }

    $filename = $filename.'_'.$width.'x'.$height.'.'.$ext;

    $imageLib = new ImageTool($path);
    $imageLib->resize($width,$height);
    return $imageLib->save($cachePath.$filename);

  }

  public function getImageSizeByRatio($originalWidth,$originalHeight,$width,$height) {

    $ratio = abs($originalWidth/$originalHeight);

    if($ratio == 1) {
      return array($width,$height);
    }

    if($originalHeight > $originalWidth) {
      if($originalHeight > $height) {
        $width = $originalWidth * ($height / $originalHeight);
      }else{
        $height = $originalHeight * ($width / $originalWidth);
      }
    }elseif($originalWidth > $originalHeight) {
      if($originalWidth > $width) {
        $width = $originalWidth * ($height / $originalHeight);
      }else{
        $height = $originalHeight * ($width / $originalWidth);
      }
    }

    return array(
      $width,
      $height
    );
  }

  public function cacheDirectoryExist($directoryName) {
    return is_dir($this->cachePath.$directoryName);
  }

  public function deleteCacheDirectory($directoryName) {

    if(!$this->cacheDirectoryExist($directoryName)) {
      return false;
    }

    return File::deleteDirectory($this->cachePath.$directoryName);
  }

}