<?php

namespace App\Http\Repositories;
// HandleImageFile
class ImageFileHelperRepository
{
  private $image;
  private $filename;
  private $width;
  private $height;
  private $maxFileSizes = 5242880;
  private $acceptedFileTypes = ['image/jpg','image/jpeg','image/png', 'image/pjpeg'];

  public function __construct($image = null) {

    if(!empty($image)) {

      $this->image = $image;
      $this->generateFileName();

      list($this->width,$this->height) = getimagesize($this->image->getRealPath());
    }

  }

  private function generateFileName() {
    $ext = $this->image->getClientOriginalExtension();

    if(empty($ext)) {
      $ext = 'jpg';
    }

    $this->filename = time().Token::generateNumber(15).$this->image->getSize().'.'.$ext;
  }

  public function getFileName() {
    return $this->filename;
  }

  public function getOriginalFileName() {
    return $this->image->getClientOriginalName();
  }

  public function getRealPath() {
    return $this->image->getRealPath();
  }

  public function checkFileType() {
    return in_array($this->image->getMimeType(), $this->acceptedFileTypes);
  }

  public function checkFileSize() {
    if($this->image->getSize() <= $this->maxFileSizes){
      return true;
    }
    return false;
  }

  public function generateImageSize($imageType,$width = null,$height = null){

    $accepteType = array('photo','avatar');

    if(empty($width)) {
      $width = $this->width; 
    }

    if(empty($height)) {
      $height = $this->height; 
    }

    if(!in_array($imageType, $accepteType)) {
      return array($width,$height);
    }

    switch ($imageType) {
      case 'photo':
        $maxSize = 960;
        break;

      case 'avatar':
        $maxSize = 300;
        break;

      case 'cover':
        $maxSize = 1000;
        break;

      default:
        return false;
        break;

    }

    if (($width > $height) && ($width > $maxSize)) {
      $height *= $maxSize / $width;
      $width = $maxSize;
    }else if(($height > $width) && ($height > $maxSize)) {
      $width *= $maxSize / $height;
      $height = $maxSize;
    }else if($width > $maxSize){
      $width = $maxSize;
      $height = $width;
    }

    return array($width,$height);

  }

}