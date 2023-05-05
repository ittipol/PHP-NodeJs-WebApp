<?php

namespace App\Http\Controllers\Web;

use App\library\service;
use App\library\handleImageFile;
use App\library\imageTool;
use Illuminate\Http\Request;
use Input;
use Auth;

class ImageController extends Controller
{
  public function __construct() {
    $this->botDisallowed();
  }

  public function upload() {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(empty(Input::file('image')) || (Input::file('image')->getClientSize() == 0)) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $image = new HandleImageFile(Input::file('image'));

    if(!$image->checkFileType() || !$image->checkFileSize()) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    // $dimension = $image->generateImageSize(Input::get('imageType'));

    // if(empty($dimension)) {
    //   return response()->json(array(
    //     'uploaded' => false
    //   ));
    // }

    // save to image table
    $imageModel = Service::loadModel('Image');
    $imageModel->fill(array(
      'model' => Input::get('model'),
      'token' => Input::get('token'),
      'filename' => $image->getFileName(),
      'image_type_id' => $imageModel->getImageTypeAlias(Input::get('imageType'),'id')
    ))->save();

    // $width = $dimension[0];
    // $height = $dimension[1];

    // $temporaryPath = $imageModel->createTemporyFolder(Input::get('model').'_'.Input::get('token').'_'.Input::get('imageType'));

    $imageTool = new ImageTool($image->getRealPath());
    // $imageTool->png2jpg($width,$height);
    // $imageTool->resize($width,$height);
    $moved = $imageTool->save($imageModel->createTemporyFolder(Input::get('model').'_'.Input::get('token').'_'.Input::get('imageType')).$image->getFileName());

    if(!$moved) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    return response()->json(array(
      'uploaded' => true,
      'filename' => $image->getFileName()
    ));
  }

  public function avatarUpload() {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(empty(Input::file('image')) || (Input::file('image')->getClientSize() == 0)) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $image = new HandleImageFile(Input::file('image'));

    if(!$image->checkFileType() || !$image->checkFileSize()) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $imageTypeId = 2; // avatar

    // check prev upload
    $banner = Service::loadModel('Image')
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['model','=','User'],
      ['token','=',Input::get('token')],
      ['image_type_id','=',$imageTypeId],
      ['created_by','=',Auth::user()->id]
    ]);

    if($banner->exists()) {
      $banner->update(['filename' => $image->getFileName()]);
      $banner = $banner->first();
    }else {
      $banner = Service::loadModel('Image');
      $banner->fill(array(
        'model' => 'User',
        'token' => Input::get('token'),
        'filename' => $image->getFileName(),
        'image_type_id' => $imageTypeId
      ))->save();
    }

    $imageTool = new ImageTool($image->getRealPath());
    $imageTool->crop(abs(Input::get('x')),abs(Input::get('y')),abs(Input::get('x')) + 320,abs(Input::get('y')) + 320);
    $moved = $imageTool->save($banner->createTemporyFolder('User_'.Input::get('token').'_'.Input::get('type')).$image->getFileName());

    if(!$moved) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    return response()->json(array(
      'uploaded' => true,
      'filename' => $image->getFileName()
    ));
  }

  public function bannerUpload() {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(empty(Input::file('image')) || (Input::file('image')->getClientSize() == 0)) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $image = new HandleImageFile(Input::file('image'));

    if(!$image->checkFileType() || !$image->checkFileSize()) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $imageTypeId = 3; // banner

    // check prev upload
    $banner = Service::loadModel('Image')
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['model','=',Input::get('model')],
      ['token','=',Input::get('token')],
      ['image_type_id','=',$imageTypeId],
      ['created_by','=',Auth::user()->id]
    ]);

    if($banner->exists()) {
      $banner->update(['filename' => $image->getFileName()]);
      $banner = $banner->first();
    }else {
      $banner = Service::loadModel('Image');
      $banner->fill(array(
        'model' => Input::get('model'),
        'token' => Input::get('token'),
        'filename' => $image->getFileName(),
        'image_type_id' => $imageTypeId
      ))->save();
    }

    $imageTool = new ImageTool($image->getRealPath());
    $imageTool->crop(abs(Input::get('x')),abs(Input::get('y')),abs(Input::get('x')) + 1000,abs(Input::get('y')) + 370);
    $moved = $imageTool->save($banner->createTemporyFolder(Input::get('model').'_'.Input::get('token').'_'.Input::get('type')).$image->getFileName());

    if(!$moved) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    return response()->json(array(
      'uploaded' => true,
      'filename' => $image->getFileName()
    ));

  }

  public function previewUpload() {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(empty(Input::file('image')) || (Input::file('image')->getClientSize() == 0)) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $image = new HandleImageFile(Input::file('image'));

    if(!$image->checkFileType() || !$image->checkFileSize()) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $imageTypeId = 4; // preview

    // check prev upload
    $preview = Service::loadModel('Image')
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['model','=',Input::get('model')],
      ['token','=',Input::get('token')],
      ['image_type_id','=',$imageTypeId],
      ['created_by','=',Auth::user()->id]
    ]);

    if($preview->exists()) {
      $preview->update(['filename' => $image->getFileName()]);
      $preview = $preview->first();
    }else {
      $preview = Service::loadModel('Image');
      $preview->fill(array(
        'model' => Input::get('model'),
        'token' => Input::get('token'),
        'filename' => $image->getFileName(),
        'image_type_id' => $imageTypeId
      ))->save();
    }

    $imageTool = new ImageTool($image->getRealPath());
    $imageTool->crop(abs(Input::get('x')),abs(Input::get('y')),abs(Input::get('x')) + 560,abs(Input::get('y')) + 315);
    $moved = $imageTool->save($preview->createTemporyFolder(Input::get('model').'_'.Input::get('token').'_'.Input::get('type')).$image->getFileName());

    if(!$moved) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    return response()->json(array(
      'uploaded' => true,
      'filename' => $image->getFileName()
    ));

  }

  public function uploadPageImage() {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(empty(Input::file('image')) || (Input::file('image')->getClientSize() == 0)) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $image = new HandleImageFile(Input::file('image'));

    if(!$image->checkFileType() || !$image->checkFileSize()) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    // check permission
    $shop = Service::loadModel('Shop')->select('created_by')->find(Input::get('id'));

    if(empty($shop) || $shop->created_by != Auth::user()->id) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    $imageTypeId = Service::loadModel('Image')->getImageTypeAlias(Input::get('type'),'id');

    $cover = Service::loadModel('Image')
    ->select('model','model_id','filename','image_type_id')
    ->where([
      ['model','=','Shop'],
      ['model_id','=',Input::get('id')],
      ['image_type_id','=',$imageTypeId],
      ['created_by','=',Auth::user()->id]
    ]);

    if($cover->exists()) { // remove current
      $cover->first()->deleteImageFile();
      $cover->delete();
    }

    // Save
    $imageModel = Service::loadModel('Image');
    $imageModel->model = 'Shop';
    $imageModel->model_id = Input::get('id');
    $imageModel->filename = $image->getFileName();
    $imageModel->image_type_id = $imageTypeId;
    // $imageModel->position = json_encode(array(
    //   'x' => Input::get('x'),
    //   'y' => Input::get('y')
    // ));
    $imageModel->save();

    // $dimension = $image->generateImageSize('cover');

    $toPath = $imageModel->getFullDirPath();
    if(!is_dir($toPath)){
      mkdir($toPath,0777,true);
    }

    $imageTool = new ImageTool($image->getRealPath());
    // $imageTool->png2jpg($width,$height);
    // $imageTool->resize($width,$height);
    if($imageTypeId == 2) {
      $imageTool->crop(abs(Input::get('x')),abs(Input::get('y')),abs(Input::get('x')) + 320,abs(Input::get('y')) + 320);
    }else {
      $imageTool->crop(abs(Input::get('x')),abs(Input::get('y')),abs(Input::get('x')) + 1200,abs(Input::get('y')) + 444);
    }
    $moved = $imageTool->save($imageModel->getImagePath());

    if(!$moved) {
      return response()->json(array(
        'uploaded' => false
      ));
    }

    return response()->json(array(
      'uploaded' => true,
      'filename' => $image->getFileName()
    ));

  }
}
