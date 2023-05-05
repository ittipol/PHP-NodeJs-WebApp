<?php

namespace App\Models;

class ItemToLocation extends Model
{
  protected $table = 'item_to_locations';
  protected $fillable = ['item_id','location_id'];
  public $timestamps  = false;

  public function location() {
    return $this->hasOne('App\Models\Location','id','location_id');
  }

  public function __saveRelatedData($model,$options = array()) {

    if(!empty($options['location_id'])) {

      // check existing
      if($this->where([
        ['item_id','=',$model->id],
        ['location_id','=',$options['location_id']]
      ])->exists()) {
        return false;
      }

      $this->where('item_id','=',$model->id)->delete();

      return $this->fill(array(
        'item_id' => $model->id,
        'location_id' => $options['location_id']
      ))->save();

    }
  }
}
