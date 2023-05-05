<?php

namespace App\Models;

class ItemToCategory extends Model
{
  protected $table = 'item_to_categories';
  protected $fillable = ['item_id','category_id'];
  public $timestamps  = false;

  public function category() {
    return $this->hasOne('App\Models\Category','id','category_id');
  }

  public function __saveRelatedData($model,$options = array()) {

    if(!empty($options['category_id'])) {

      // Check data existing
      if($this->where([
        ['item_id','=',$model->id],
        ['category_id','=',$options['category_id']]
      ])->exists()) {
        return false;
      }

      $this->where('item_id','=',$model->id)->delete();

      return $this->fill(array(
        'item_id' => $model->id,
        'category_id' => $options['category_id']
      ))->save();

    }
  }
}
