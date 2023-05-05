<?php

namespace App\Models;

use App\library\stringHelper;
use Auth;

class HashtagList extends Model
{
  protected $table = 'hashtag_lists';
  protected $fillable = ['model','model_id','hashtag_id','catagory_id','created_by'];
  public $timestamps  = false;

  public function __saveRelatedData($model,$value) {

    $now = date('Y-m-d H:i:s');

    // Get current hashtag log
    $_hashtagLogs = $this
    ->select('hashtag_id')
    ->where([
      ['model','=',$model->modelName],
      ['model_id','=',$model->id],
      ['created_by','=',Auth::user()->id]
    ])->get();

    $hashtagLogs = array();
    foreach ($_hashtagLogs as $hashtagLog) {
      $hashtagLogs[] = $hashtagLog->hashtag_id;
    }

    $hashtagSaved = array();

    foreach (StringHelper::getHashtagFromString($value) as $value) {

      $hashtag = Hashtag::where('hashtag','=',$value)->first();

      if(!empty($hashtag)) {

        $hashtagSaved[] = $hashtag->id;

        if(!in_array($hashtag->id, $hashtagLogs)) {
          // update last input
          $hashtag->update(
            ['last_input' => $now]
          );
          
          // Save new hashtag log
          $this->_save(array(
            'model' => $model->modelName,
            'modelId' => $model->id,
            'hashtagId' => $hashtag->id,
            'userId' => Auth::user()->id
          ));
        }

      }else {

        // save new hashtag
        $hashtag = new Hashtag;
        $hashtag->hashtag = $value;
        $hashtag->last_input = $now;
        $hashtag->save();

        $hashtagSaved[] = $hashtag->id;

        $this->_save(array(
          'model' => $model->modelName,
          'modelId' => $model->id,
          'hashtagId' => $hashtag->id,
          'userId' => Auth::user()->id
        ));
      }

    }

    $this
    ->select('hashtag_id')
    ->where([
      ['model','=',$model->modelName],
      ['model_id','=',$model->id],
      ['created_by','=',Auth::user()->id]
    ])
    ->whereNotIn('hashtag_id',$hashtagSaved)
    ->delete();

    return true;
  }

  public function _save($value) {
    $_hashtagLog = $this->newInstance();
    $_hashtagLog->model = $value['model'];
    $_hashtagLog->model_id = $value['modelId'];
    $_hashtagLog->hashtag_id = $value['hashtagId'];
    $_hashtagLog->created_by = $value['userId'];
    return $_hashtagLog->save();
  }

}
