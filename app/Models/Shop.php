<?php

namespace App\Models;

use App\library\stringHelper;
use Auth;

class Shop extends Model
{
  protected $table = 'shops';
  protected $fillable = ['slug','name','description','contact','deleted','created_by'];

  public function getShortDesc() {
    if(empty($this->description)) {
      return null;
    }
    return StringHelper::truncString($this->description,220,true,true);
  }

  public function getProfileImage($url = false) {
    
    $image = new Image;

    $avatar = $image
    ->select('filename')
    ->where([
      ['model','=','Shop'],
      ['model_id','=',$this->id],
      ['image_type_id','=',$image->getImageTypeAlias('avatar','id')],
      // ['created_by','=',Auth::user()->id]
    ]);

    if(!$avatar->exists()) {
      // return '/assets/images/common/shop.png';
      return null;
    }

    $avatar = $avatar->first();

    return ($url) ? '/get_image/'.$avatar->filename : $avatar->filename;
  }

  public function getCover($url = false) {

    $image = new Image;

    $cover = $image
    ->select('filename')
    ->where([
      ['model','=','Shop'],
      ['model_id','=',$this->id],
      ['image_type_id','=',$image->getImageTypeAlias('cover','id')],
      // ['created_by','=',$this->created_by]
    ]);

    if(!$cover->exists()) {
      return null;
    }

    $cover = $cover->first();

    return ($url) ? '/get_image/'.$cover->filename : $cover->filename;
  }

  public function getCategoryIds() {

    $shopToCategory = $this->getRelatedData('ShopToCategory',array(
      'fields' => array('category_id')
    ));

    if(empty($shopToCategory)) {
      return null;
    }

    $categoryId = array();
    foreach ($shopToCategory as $category) {
      $categoryId[] = $category->category_id;
    }

    return $categoryId;
  }

  public function getLocationId() {
    
    $shopToLocation = $this->getRelatedData('ShopToLocation',array(
      'fields' => array('location_id'),
      'first' => true
    ));

    if(empty($shopToLocation)) {
      return null;
    }

    return $shopToLocation->location_id;
  }

  public function getLocationPaths() {

    $shopToLocation = ShopToLocation::where('shop_id','=',$this->id)->select('location_id');

    if(!$shopToLocation->exists()) {
      return null;
    }

    $LocationModel = new Location;

    return $LocationModel->getLocationPaths($shopToLocation->first()->location_id);
  }

  public function getLocationBreadcrumb() {

    $shopToLocation = ShopToLocation::where('shop_id','=',$this->id)->select('location_id');

    if(!$shopToLocation->exists()) {
      return null;
    }

    $LocationModel = new Location;

    return $LocationModel->breadcrumb($shopToLocation->first()->location_id);
  }

  public function buildForChatRoom() {
    return array(
      'id' => $this->id,
      'title' => $this->name,
      'url' => '/shop/page/'.$this->slug
    );
  }

  public function buildDataDetail() {
    

    foreach (StringHelper::getHashtagFromString($this->description) as $value) {
      $this->description = str_replace($value, '<a href="/hashtag/'.substr($value, 1).'">'.$value.'</a>', $this->description);
    }

    foreach (StringHelper::getUrlFromString($this->description) as $value) {
      $this->description = str_replace($value, '<a href="'.$value.'">'.StringHelper::truncString($value,40,true,true).'</a>', $this->description);
    }

    foreach (StringHelper::getUrlFromString($this->contact) as $value) {
      $this->contact = str_replace($value, '<a href="'.$value.'">'.StringHelper::truncString($value,40,true,true).'</a>', $this->contact);
    }

    // category
    $categories = array();
    foreach ($this->getCategoryIds() as $categoryId) {

      $_category = Category::select('name','image')->find($categoryId);

      $categories[] = array(
        'name' => $_category->name,
        'image' => $_category->getImagePath(),
        'url' => null
      );
    }

    $user = User::select('id','name','last_active')->find($this->created_by);

    return array(
      'id' => $this->id,
      'slug' => $this->slug,
      'name' => $this->name,
      'description' => $this->description,
      'contact' => $this->contact,
      'created_by' => $this->created_by,
      'categories' => $categories,
      'locations' => $this->getLocationBreadcrumb(),
      'profileImage' => $this->getProfileImage(true),
      'cover' => $this->getCover(true),
      // 'online' => $this->online(),
      'owner' => $user->getUserOnlineInfo()
    );
  }

  public function buildDataList() {

    $blockedUser = false;
    if(Auth::check() && (Auth::user()->id != $this->created_by)) {
      $blockedUser = UserBlocking::where([
        ['model','=','User'],
        ['model_id','=',$this->created_by],
        ['user_id','=',Auth::user()->id],
      ])->exists();
    }

    return array(
      'id' => $this->id,
      'slug' => $this->slug,
      'name' => $this->name,
      'created_by' => $this->created_by,
      'locations' => $this->getLocationBreadcrumb(),
      'profileImage' => $this->getProfileImage(true),
      'cover' => $this->getCover(true),
      'blockedUser' => $blockedUser,
      'user' => User::buildProfile($this->created_by)
    );

  }

  // public function online() {
  //   return User::checkOnline($this->created_by);
  // }

}
