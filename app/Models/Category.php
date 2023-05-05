<?php

namespace App\Models;

use App\library\url;
use Auth;

class Category extends Model
{
  protected $table = 'categories';
  protected $fillable = ['parent_id','name','slug','image','active'];
  public $timestamps  = false;

  public function getCategoryName($id) {

    $category = $this->select('name')->find($id);

    if(empty($category)) {
      return null;
    }

    return $category->name;
  }

  public function getCategoryPaths($id) {

    $paths = CategoryPath::where('category_id','=',$id)->get();

    foreach ($paths as $path) {

      $hasChild = false;
      if($path->path->where('parent_id','=',$path->path->id)->exists()) {
        $hasChild = true;
      }

      $categoryPaths[] = array(
        'id' => $path->path->id,
        'name' => $path->path->name,
        'hasChild' => $hasChild
      );
    }

    return $categoryPaths;
  }

  public function breadcrumb($id) {

    if(empty($id)) {
      return null;
    }

    $paths = CategoryPath::where('category_id','=',$id)->get();

    foreach ($paths as $path) {

      $categoryPaths[] = array(
        'id' => $path->path->id,
        'name' => $path->path->name,
        // 'url' => Url::setAndParseUrl('ticket/{category_id}',array('category_id'=>$path->path->id)),
        'url' => '/category/'.$path->path->slug
      );
    }

    return $categoryPaths;
  }

  public function getSubCategories($parentId = null, $build = true, $options = null) {

    // $url = new Url;

    $categories = $this->select('id','name','slug')->where('parent_id','=',$parentId);

    if(!$categories->exists()) {
      return null;
    }

    if(!$build) {
      return $categories->get();
    }

    $_categories = array();
    foreach ($categories->get() as $category) {
      $_categories[] = array(
        'name' => $category->name,
        'url' => '/category/'.$category->slug,
        'total' => $this->countItem($_category->id,$options),
      );
    }

    return $_categories;

  }

  public function getCategoriesWithSubCategories($id = null, $options = array()) {

    $queryString = '';
    if(!empty($options['queryString'])) {
      $queryString = $options['queryString'];
    }

    if(empty($id)) {

      $categories = $this->getSubCategories($id,false);

      $_categories = array();
      foreach ($categories as $_category) {

        $_categories[] = array(
          'name' => $_category->name,
          'url' => '/category/'.$_category->slug.$queryString,
          // 'total' => $this->countItem($_category->id,$options),
          // 'subCategories' => array()
        );

      }

      return $_categories;
    }
    
    $category = $this->find($id);

    if(empty($category->parent_id)) {

      $categories = $this->getSubCategories($category->id,false);

      $_categories = array();
      foreach ($categories as $_category) {

        $_categories[] = array(
          'name' => $_category->name,
          'url' => '/category/'.$_category->slug,
          // 'total' => $this->countItem($_category->id,$options),
          // 'subCategories' => array()
        );

      }

    }else{
      $categories = $this->getSubCategories($category->parent_id,false);

      $_categories = array();
      foreach ($categories as $_category) {

        $__subCategories = array();
        if($id == $_category->id) {
      
          $subCategories = $this->getSubCategories($_category->id,false);

          if(!empty($subCategories)) {
            foreach ($subCategories as $_subCategories) {

              $__subCategories[] = array(
                'name' => $_subCategories->name,
                'url' => '/category/'.$_subCategories->slug,
                'total' => $this->countItem($_subCategories->idoptionsmodel)
              );
            }
          }
          
        }

        $_categories[] = array(
          'name' => $_category->name,
          'url' => '/category/'.$_category->slug,
          'total' => $this->countItem($_category->id,$options),
          'subCategories' => $__subCategories
        );

      }
    }

    return $_categories;
  }

  public function getRecommendedCategories($id = null, $options = array()) {

    // Order By counting (get 2 item)

    $_categories = array();

    $queryString = '';
    if(!empty($options['queryString'])) {
      $queryString = $options['queryString'];
    }

    if(!empty($id)) {

      $categories = $this->select('id','name','slug')->where('parent_id','=',$id)->get();

      foreach ($categories as $_category) {
        $_categories[] = array(
          'name' => $_category->name,
          'total' => $this->countItem($_category->id,$options),
          'url' => '/category/'.$_category->slug.$queryString
        );
      }
    }

    $itemCount = count($_categories);

    for($i = 0; $i < $itemCount; $i++){
      $val = $_categories[$i];
      $j = $i-1;
      while($j>=0 && $_categories[$j]['total'] < $val['total']){
        $_categories[$j+1] = $_categories[$j];
        $j--;
      }
      $_categories[$j+1] = $val;
    }

    for($i = $itemCount - 1; $i >= 0; $i--){
      if(($_categories[$i]['total'] == 0) || (count($_categories) > 3)){
        array_pop($_categories);
      }
    }

    return $_categories;
  }

  public function countItem($id, $options = array()) {

    $categoryPaths = CategoryPath::select('category_id')->where('path_id','=',$id)->get();

    $ids = array();
    foreach ($categoryPaths as $categoryPath) {
      $ids[] = $categoryPath->category_id;
    }

    $_query = Item::query();

    if(!empty($options['model'])) {
      $_query->getQuery()->joins = $options['model']->getQuery()->joins;
      $_query->getQuery()->wheres = $options['model']->getQuery()->wheres;
      $_query->getQuery()->bindings = $options['model']->getQuery()->bindings;
    }else {
      if(!empty($options['expiration_date'])) {
        $_query->where('items.expiration_date','>',$options['expiration_date']);
      }

      if(!empty($options['cancel_option'])) {
        $_query->where('items.cancel_option','=',$options['cancel_option']);
      }

      if(!empty($options['approved'])) {
        $_query->where('items.approved','=',$options['approved']);
      }

      if(!empty($options['deleted'])) {
        $_query->where('items.deleted','=',$options['deleted']);
      }
    }

    if(!empty($options['blocking'])) {

      $blocking = $options['blocking'];

      $_query->where(function($q) use($blocking) {

        if(!empty($blocking['user'])) {
          $q->whereNotIn('items.created_by',$blocking['user']);
        }

        if(!empty($blocking['item'])) {
          $q->whereNotIn('items.id',$blocking['item']);
        }
      
      });
    }

    return $_query
    ->join('item_to_categories', 'item_to_categories.item_id', '=', 'items.id')
    ->whereIn('item_to_categories.category_id',$ids)
    ->count();
  }

  public function getImagePath() {
    return '/assets/images/catagory/'.$this->image;
  }

}
