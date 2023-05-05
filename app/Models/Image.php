<?php

namespace App\Models;

use App\library\service;
use App\library\stringHelper;
use App\library\handleImageFile;
use App\library\cache;
use App\library\url;
use File;
use Auth;

class Image extends Model
{
  protected $table = 'images';
  protected $fillable = ['model','model_id','token','filename','image_type_id','created_by'];

  private $imageTypes = array(
    'photo' => array(
      'id' => 1,
      'name' => 'photo',
      'path' => 'photo'
    ),
    'avatar' => array(
      'id' => 2,
      'name' => 'avatar',
      'path' => 'avatar'
    ),
    'cover' => array(
      'id' => 3,
      'name' => 'cover',
      'path' => 'cover'
    ),
    'preview' => array(
      'id' => 4,
      'name' => 'preview',
      'path' => 'preview'
    ),
  );

  protected $storagePath = 'app/public/images/';
  private $temporaryPath = 'temporary/';

  public static function boot() {

    parent::boot();

    Image::deleted(function($model) {
      // delete image file after image record is deleted
      $model->deleteImageFile();
    });

  }

  public function imageType() {
    return $this->hasOne('App\Models\ImageType','id','image_type_id');
  }

  public function __saveRelatedData($model,$value,$options = array()) {
    return $this->handleImages($model,$value,$options);
  }

  private function handleImages($model,$images,$options = array()) {

    // $imageType = new ImageType;

    foreach ($images as $type => $value) {

      // if(!$imageType->checkExistByAlias($type)) {
      //   continue;
      // }

      if(empty($this->getImageTypeAlias($type))) {
        continue;
      }

      if(!empty($value['delete'])) {
        $this->deleteImages($model,$value['delete']);
      }

      if(!empty($value['images'])) {
        $this->addImages($model,$value['images'],array(
          'type' => $type,
          'token' => $value['token']
        ));
      }

    }

    return true;
  }

  private function addImages($model,$images,$options = array()) {

    if(empty($model->imageTypeAllowed[$options['type']]) || ($model->imageTypeAllowed[$options['type']]['limit'] == 0)) {
      return false;
    }

    // $temporaryFile = new TemporaryFile;
    // $imageType = new ImageType;

    $count[$options['type']] = 0;

    // $imageType = $imageType->where('alias','like',$options['type'])->select('path')->first();

    foreach ($images as $image) {
      if($model->imageTypeAllowed[$options['type']]['limit'] < ++$count[$options['type']]) {
        break;
      }

      $this->handleImage($model,$image,$options);
    }

    // remove temp dir
    $this->deleteTemporaryDirectory($model->modelName.'_'.$options['token'].'_'.$options['type']);
    // remove temp file record
    // $temporaryFile->deleteTemporaryRecords($model->modelName,$options['token']);

  }

  public function deleteImageFile($path = null) {

    $path = $this->getImagePath();

    if(!file_exists($path)){
      return false;
    }

    if(File::Delete($path)) {
      $cache = new Cache;
      $cache->deleteCacheDirectory(pathinfo($this->filename, PATHINFO_FILENAME));
    }

    return true;

  }

  public function deleteImage($model,$filename) {

    $image = $this->newInstance()
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['model','=',$model->modelName],
      ['model_id','=',$model->id],
      ['created_by','=',Auth::user()->id],
      ['filename','=',$filename]
    ]);

    if(!$image->exists()) {
      return false;
    }

    $image->first()->deleteImageFile();

    return $image->delete();

  }

  public function deleteImages($model,$filenames) {

    $images = $this->newInstance()
    ->select('model','model_id','filename','image_type_id')
    ->whereIn('filename',$filenames)
    ->where([
      ['model','=',$model->modelName],
      ['model_id','=',$model->id],
      ['created_by','=',Auth::user()->id]
    ]);

    if(!$images->exists()) {
      return false;
    }

    foreach ($images->get() as $image) {
      $image->deleteImageFile();
    }

    return $images->delete();

  }

  public function deleteAllImages($model,$options = array()) {

    $conditions = array(
      ['model','=',$model->modelName],
      ['model_id','=',$model->id],
      ['created_by','=',Auth::user()->id]
    );

    if(!empty($options['type'])) {
      array_push($conditions, ['image_type_id','=',$this->getImageTypeAlias($options['type'],'id')]);
    }

    $images = $this->newInstance()
    ->select('model','model_id','filename','image_type_id')
    ->where($conditions);

    if(!$images->exists()) {
      return false;
    }

    foreach ($images->get() as $image) {
      $image->deleteImageFile();
    }

    return $images->delete();

  }

  public function deleteDirectory($model) {
    return File::deleteDirectory(storage_path($this->storagePath.$model->modelAlias).'/'.$model->id.'/');
  }

  public function addImage($model,$image,$options = array()) {

    $filename = $this->handleImage($model,$image,$options);

    $this->deleteTemporaryDirectory($model->modelName.'_'.$options['token'].'_'.$options['type']);

    return $filename;
  }

  public function handleImage($model,$image,$options = array()) {

    if(empty($image['filename'])) {
      return false;
    }

    $path = $this->getFilePath($image['filename'],array(
      'directoryName' => $model->modelName.'_'.$options['token'].'_'.$options['type']
    ));

    if(!file_exists($path)) {
      return false;
    }

    $imageInstance = $this->newInstance()
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['filename','=',$image['filename']],
      ['image_type_id','=',$this->getImageTypeAlias($options['type'],'id')],
      ['created_by','=',Auth::user()->id]
    ]);

    if(!$imageInstance->exists()) {
      return false;
    }

    if(!empty($image['_filename'])) {

      $_imageInstance = $this->newInstance()
      ->select('model','model_id','filename','image_type_id')
      ->where('filename','=',$image['_filename']);

      if($_imageInstance->exists()) {

        // delete record
        $imageInstance->delete();

        // delete image file & cache file
        $_imageInstance->first()->deleteImageFile();

        $_imageInstance->update([
          'filename' => $image['filename'],
        ]);

      }
    }else{
      $imageInstance->update([
        'model_id' => $model->id,
        'token' => null
      ]);
    }

    $imageInstance = $imageInstance->first();

    $toPath = $imageInstance->getFullDirPath();
    if(!is_dir($toPath)){
      mkdir($toPath,0777,true);
    }

    $imageInstance->moveImage($path,$imageInstance->getImagePath());

    return $imageInstance->filename;
  }

  public function moveImage($oldPath,$to) {

    if(empty($oldPath)) {
      return false;
    }

    return File::move($oldPath, $to);
  }

  public function getDirPath() {
    return storage_path($this->storagePath.StringHelper::generateUnderscoreName($this->model)).'/'.$this->model_id.'/';
  }

  public function getFullDirPath() {

    $path = $this->getDirPath();

    // if(!empty($this->imageType->path)) {
    //   $path .= $this->imageType->path.'/';
    // }

    $_path = $this->getImageTypeById($this->image_type_id,'path');

    if(!empty($_path)) {
      $path .= $_path.'/';
    }

    return $path;
  }

  public function getImagePath($filename = null) {

    if(empty($filename)) {
      $filename = $this->filename;
    }

    return $this->getFullDirPath().$filename;
  }

  public function getImageUrl($filename = '') {

    if(empty($filename)) {
      $filename = $this->filename;
    }

    $path = '';
    if(file_exists($this->getImagePath())){
      $path = '/get_image/'.$filename;
    }

    return $path;
  }

  public function getFirstImage($model,$style) {

    $imageStyle = new ImageStyle;

    $image = $model->getRelatedData('Image',array(
      'conditions' => array(
        array('image_style_id','=',$imageStyle->getIdByalias($style))
      ),
      'first' => true
    ));

    $_image = array();
    if(!empty($image)) {
      $_image = $image->buildModelData();
    }

    return $_image;

  }

  public function base64Encode() {

    $dirPath = 'image/'.strtolower($this->model).'/';

    $path = '';
    if(File::exists($this->getImagePath())){
      $path = '/get_image/'.$this->name;
    }

    return base64_encode(File::get($path));
  }

  // public function buildModelData() {
  //   return array(
  //     'filename' => $this->filename,
  //     '_url' => $this->getImageUrl()
  //   );
  // }

  public function build() {
    return array(
      'url' => $this->getImageUrl()
    );    
  }

  public function buildSlide() {

    // need
    // Size format {width}x{height}
    // original image url
    // preview image url

    $cache = new cache;

    if(!file_exists($this->getImagePath())){
      return array(
        'size' => '',
        '_url' => '',
        '_preview_url' => ''
      );
    }

    $info = getimagesize($this->getImagePath());

    return array(
      'size' => $info[0].'x'.$info[1],
      '_url' => $this->getImageUrl(),
      '_preview_url' => $cache->getCacheImageUrl($this,'md_scale')
    );
  }

  public function buildFormData() {

    $cache = new cache;
    
    return array(
      // 'id' => $this->id,
      'filename' => $this->filename,
      // '_url' => $this->getImageUrl(),
      '_url' => $cache->getCacheImageUrl($this,'sm_scale')
    );
  }

  // public function saveImage($model,$image,$options = array()) {

  //   $cache = new cache;
  //   // $imageType = new ImageType;
  //   $image = new HandleImageFile($image);

  //   if(!$this->exists) {
  //     $this->model = $model->modelName;
  //     $this->model_id = $model->id;
  //     // $this->image_type_id = $imageType->getIdByalias($options['type']);
  //     $this->image_type_id = $this->getImageTypeAlias($options['type'],'id');

  //     // if(!empty($options['position'])) {
  //     //   $this->position = $options['position'];
  //     // }

  //   }

  //   $this->filename = $image->getFileName();

  //   if(!$this->save()) {
  //     return false;
  //   }

  //   // $dimension = $image->generateImageSize($options['type']);

  //   // $width = $dimension[0];
  //   // $height = $dimension[1];

  //   $toPath = $this->getFullDirPath();
  //   if(!is_dir($toPath)){
  //     mkdir($toPath,0777,true);
  //   }

  //   $imageTool = new ImageTool($image->getRealPath());
  //   // $imageTool->png2jpg($width,$height);
  //   // $imageTool->resize($width,$height);
  //   $moved = $imageTool->save($this->getImagePath());

  //   if($moved) {
  //     $cache->deleteCacheDirectory(pathinfo($this->filename, PATHINFO_FILENAME));
  //   }

  //   return $this->filename;
  // }

  public function getFormation() {

    $path = $this->getImagePath();

    if(!file_exists($path)){
      return '';
    }

    $info = getimagesize($path);

    if($info[0] < ($info[1]-20)) {
      return 'portrait';
    }

    return 'landscape';
  }

  public function getImageTypeAlias($alias, $value = null) {

    if(empty($this->imageTypes[$alias])) {
      return null;
    }

    if(!empty($value)) {
      
      if(empty($this->imageTypes[$alias][$value])) {
        return null;
      }

      return $this->imageTypes[$alias][$value];
    }

    return $this->imageTypes[$alias];

  }

  public function getImageTypeById($id, $value = null) {

    switch ($id) {
      case 1:
        $alias = 'photo';
        break;

      case 2:
        $alias = 'avatar';
        break;

      case 3:
        $alias = 'cover';
        break;

      case 4:
        $alias = 'preview';
        break;
      
      default:
        return null;
        break;
    }

    return $this->getImageTypeAlias($alias,$value);
  }


  public function getFilePath($filename,$options = array()) {

    $temporaryPath = storage_path($this->temporaryPath);

    if(!empty($options['directoryName'])) {
      $temporaryPath .= $options['directoryName'].'/';
    }

    return $temporaryPath.$filename;
  }

  public function getTemporyFilePath() {
    return Url::addSlash(storage_path($this->temporaryPath).$this->model.'_'.$this->token.'_'.$this->getImageTypeById($this->image_type_id,'path')).$this->filename;
  }

  public function getTemporyPath() {
    return Url::addSlash(storage_path($this->temporaryPath).$this->model.'_'.$this->token.'_'.$this->getImageTypeById($this->image_type_id,'path'));
  }

  public function createTemporyFolder($directoryPath) {

    $temporaryPath = storage_path($this->temporaryPath);

    if(!is_dir($temporaryPath)){
      mkdir($temporaryPath,0777,true);
    }

    $temporaryPath .= $directoryPath;

    if(!is_dir($temporaryPath)){
      mkdir($temporaryPath,0777,true);
    }

    return Url::addSlash($temporaryPath);
  }

  public function moveTemporaryFile($oldPath,$filename,$options = array()) {

    $temporaryPath = $this->createTemporyFolder($options['directoryName']);

    return File::move($oldPath, $temporaryPath.$filename);
  }

  public function temporaryDirectoryExist($directoryPath) {
    return is_dir(storage_path($this->temporaryPath).$directoryPath);
  }

  public function deleteTemporaryDirectory($directoryPath) {
    if(!$this->temporaryDirectoryExist($directoryPath)) {
      return false;
    }

    return File::deleteDirectory(storage_path($this->temporaryPath).$directoryPath);
  }

}
