<?php

namespace App\Models;

class ShopToCategory extends Model
{
  protected $table = 'shop_to_categories';
  protected $fillable = ['shop_id','category_id'];
  public $timestamps  = false;

  public function category() {
    return $this->hasOne('App\Models\Category','id','category_id');
  }

  public function __saveRelatedData($model,$options = array()) {

    if(!empty($options['category_id'])) {

      $this->where('shop_id','=',$model->id)->delete();

      foreach ($options['category_id'] as $categoryId) {
        $this->newInstance()
        ->fill(array(
          'shop_id' => $model->id,
          'category_id' => $categoryId
        ))->save();
      }

      return true;
    }
  }
}
