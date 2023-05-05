<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use App\Http\Repositories\ServiceRepository;
use App\Http\Repositories\StringHelperRepository;
use App\Http\Repositories\TokenRepository;
use App\Http\Repositories\DateRepository;
use Session;
use Schema;
use Route;
use Auth;

class Model extends BaseModel
{
  public $modelName;
  public $modelAlias;
  protected $storagePath = 'app/public/';
  // protected $state = 'create';

  protected $serviceRepo;
  protected $stringHelperRepo;
  protected $tokenRepo;
  protected $dateRepo;
  
  public function __construct(array $attributes = []) {

    $this->serviceRepo = new ServiceRepository;
    $this->stringHelperRepo = new StringHelperRepository;
    $this->tokenRepo = new TokenRepository;
    $this->dateRepo = new DateRepository;

    $this->modelName = class_basename(get_class($this));
    $this->modelAlias = $this->stringHelperRepo->generateUnderscoreName($this->modelName);

    parent::__construct($attributes);
  }

  public static function boot() {

    parent::boot();

    // before saving
    parent::saving(function($model){
      
      if(!$model->exists){

        // $model->state = 'create';

        // if((Schema::hasColumn($model->getTable(), 'ip_address')) && (empty($model->ip_address))) {
        //   $model->ip_address = $this->serviceRepo->getIp();
        // }

        if(Schema::hasColumn($model->getTable(), 'slug') && empty($model->slug)) {

          $slug = ''; 
          do {        
            $slug = Token::generateUrlSlug();
          } while ($this->serviceRepo->loadModel($model->modelName)->where('slug','=',$slug)->exists());

          $model->slug = $slug;
        }

        if(Schema::hasColumn($model->getTable(), 'created_by') && empty($model->created_by)) {
          $model->created_by = Auth::user()->id;
        }

      }else{
        //$model->state = 'update';
      }

    });

  }

  public function checkExistByAlias($alias) {

    if(!Schema::hasColumn($this->getTable(), 'alias')){
      return false;
    }

    return $this->where('alias','like',$alias)->exists();
  }

  public function getIdByalias($alias) {

    if(!Schema::hasColumn($this->getTable(), 'alias')){
      return false;
    }

    $record = $this->getData(array(
      'conditions' => array(
        ['alias','like',$alias]
      ),
      'fields' => array('id'),
      'first' => true
    ));

    if(empty($record)) {
      return null;
    }

    return $record->id;
  }

  // public function getByAlias($alias) {

  //   if(!Schema::hasColumn($this->getTable(), 'alias')){
  //     return false;
  //   }

  //   return $this->getData(array(
  //     'conditions' => array(
  //       ['alias','like',$alias]
  //     ),
  //     'first' => true
  //   ));

  // }

  public function getBy($value,$field,$first = true) {

    if(empty($value)) {
      return null;
    }

    if(!Schema::hasColumn($this->getTable(), $field)){
      return false;
    }

    return $this->getData(array(
      'conditions' => array(
        [$field,'=',$value]
      ),
      'first' => $first
    ));

  }

  public function getOrNullById($id) {
    $data = $this->find($id);

    if(empty($data)) {
      return null;
    }

    return $data;
  }

  public function getData($options = array()) {

    $model = $this->newInstance();

    if(!empty($options['joins'])) {

      if(is_array(current($options['joins']))) {

        foreach ($options['joins'] as $value) {
          $model = $model->join($value[0], $value[1], $value[2], $value[3]);
        }

      }else{
        $model = $model->join(
          current($options['joins']), 
          next($options['joins']), 
          next($options['joins']), 
          next($options['joins'])
        );
      }

    }

    if(!empty($options['conditions']['in'])) {

      foreach ($options['conditions']['in'] as $condition) {
        $model = $model->whereIn(current($condition),next($condition));
      }

      unset($options['conditions']['in']);

    }

    if(!empty($options['conditions']['or'])) {

      $conditions = $criteria['conditions']['or'];

      $model = $model->where(function($query) use($conditions) {

        foreach ($conditions as $condition) {
          $query->orWhere(
            $condition[0],
            $condition[1],
            $condition[2]
          );
        }

      });

      unset($options['conditions']['or']);

    }

    if(!empty($options['conditions'])){
      $model = $model->where($options['conditions']);
    }

    if(!$model->exists()) {
      return array();
    }

    if(!empty($options['fields'])){
      $model = $model->select($options['fields']);
    }

    if(!empty($options['order'])){

      if(is_array(current($options['order']))) {

        foreach ($options['order'] as $value) {
          $model = $model->orderBy($value[0],$value[1]);
        }

      }else{
        $model = $model->orderBy(current($options['order']),next($options['order']));
      }
      
    }

    if(!empty($options['list'])) {
      return $this->serviceRepo->getList($model->get(),$options['list']);
    }
    
    if(empty($options['first'])) {
      return $model->get();
    }

    return $model->first();

  }

  public function getRelatedData($modelName,$options = array()) {

    $model = $this->serviceRepo->loadModel($modelName);
    $field = $this->modelAlias.'_id';

    if(Schema::hasColumn($model->getTable(), $field)) {
      $conditions = array(
        [$field,'=',$this->id],
      );
    }elseif($model->checkHasFieldModelAndModelId()) {
      $conditions = array(
        ['model','like',$this->modelName],
        ['model_id','=',$this->id],
      );
    }else{
      return false;
    }

    if(!empty($options['conditions'])){
      $options['conditions'] = array_merge($options['conditions'],$conditions);
    }else{
      $options['conditions'] = $conditions;
    }

    return $model->getData($options);

  }

  public function checkHasFieldModelAndModelId() {
    if(Schema::hasColumn($this->getTable(), 'model') && Schema::hasColumn($this->getTable(), 'model_id')) {
      return true;
    }
    return false;
  }

  // public function buildModelData() {
  //   return $this->getAttributes();
  // }

}
