<?php

namespace App\library;

use Route;
use Schema;

class NotificationHelper {

  private $model;

  // public function __construct($model = null) {
  //   $this->model = $model;
  // }

  public function setModel($model) {
    $this->model = $model;
  }

  public function create($event,$options = array()) {

    if(empty($this->model)) {
      return false;
    }

    $notificationModel = Service::loadModel('Notification');
    $notificationEventModel = Service::loadModel('NotificationEvent');

    $event = $notificationEventModel->where([
      ['model','like',$this->model->modelName],
      ['event','like',$event]
    ]);

    if(!$event->exists()) {
      return false;
    }

    $event = $event->first();

    $value = array(
      'model' => $this->model->modelName,
      'model_id' => $this->model->id,
      'notification_event_id' => $event->id,
      'unread' => 1,
      'notify' => 1
    );

    $parserOptions = array(
      'format' => array(
        'title' => $event->title_format,
      ),
      'data' => $this->model->getAttributes()
    );

    $result = $this->parser($this->model,$parserOptions);

    if(!empty($result)){
      foreach ($result as $key => $_value){
        $value[$key] = $_value;
      }
    }

    $value = array_merge($value,$this->getSender($options));

    $receivers = $this->getReceiver($event->receiver);

    foreach ($receivers as $receiver) {

      $_model = $notificationModel->newInstance();

      $_value = array_merge($value,array(
        'receiver' => 'Person',
        'receiver_id' => $receiver,
      ));

      $_model->fill($_value)->save();
    }

    return true;

  }

  private function parser($model,$options = array()) {

    if(empty($options['format']) || empty($options['data'])){
      return false;
    }

    $parseFormat = '/{{[\w\d|._,=>@$]+}}/';
    $parseValue = '/[\w\d|._,=>@$]+/';

    $result = array();
    foreach ($options['format'] as $key => $format){

      if(empty($format)) {
        $result[$key] = $format;
        continue;
      }

      preg_match_all($parseFormat, $format, $matches);

      if(empty($matches[0])){
        $result[$key] = $format;
        continue;
      }

      $result[$key] = $format;

      foreach ($matches[0] as $value) {

        preg_match($parseValue, $value, $_matches);

        if(empty($_matches[0])){
          break;
        }

        if(substr($_matches[0],0,2) == '$$'){

          list($var,$valueName) = explode('|', $_matches[0]);

          switch (substr($var,2)) {
            case 'Session':
              $_value = session()->get($valueName);
              break;
          }

        }elseif(substr($_matches[0],0,2) == '__'){

          if(strpos($_matches[0], '|')) {
            
            list($_model,$fx) = explode('|', $_matches[0]);

            $_value = Service::loadModel(substr($_model,2))->{$fx}($model);

          }else{
            $_value = $model->{substr($_matches[0],2)}();
          }

        }elseif(array_key_exists($_matches[0],$options['data'])) {
          $_value = $options['data'][$_matches[0]];
        }else{
          $parts = explode('|', $_matches[0]);

          if(!empty($parts[1])){

            $records = $this->_parser($parts[0],$model,array(
              'stringFormat' => $parts[1]
            ));

            list($class,$field) = explode('.', $parts[0]);

            $_value = array();

            if(!empty($records[$class])){
              foreach ($records[$class] as $record) {
                $_value[] = $record[$field];
              }
            }
       
            $_value = implode(' ', $_value);

          }
        }

        $result[$key] = $this->_replace($_value,$value,$result[$key]);

      }

      $result[$key] = trim($result[$key]);

    }

    return $result;

  }

  private function _parser($fields,$class,$options = array()) {

    $data = array();

    if(empty($class)){
      return false;
    }

    if(!empty($options['stringFormat'])) {

      $formats = explode(',', $options['stringFormat']);

      $records = array();
      foreach ($formats as $format) {
        list($key1,$key2) = explode('=>', $format);
        $records = $this->__formatParser($class,$key1,$key2,$records);
      }

      $fields = explode('.', $fields);

      $data = array();
      foreach ($records as $key => $record) {
        $data[$fields[0]][$key][$fields[1]] = $record[$fields[1]];
      }

    }

    return $data;

  }

  private function __formatParser($class,$key1,$key2,$records = array()) {

    $temp = array();

    if(substr($key1, 0, 1) == '@') {
      $func = substr($key1, 1);
      $records = $class->{$func}($key2);
      
      foreach ($records as $key => $_record) {
        $temp[] = $_record;
      }

      return $temp;
    }

    list($class1,$field1) = explode('.', $key1);
    list($class2,$field2) = explode('.', $key2);

    $class1 = Service::loadModel($class1);
    $class2 = Service::loadModel($class2);

    if(($class->modelName == $class1->modelName) && empty($records)){
      $records = $class->getAttributes();
    }

    if(array_key_exists($field1,$records)) {

      $_records = $class2->where($field2,'=',$records[$field1])->get();

      foreach ($_records as $key => $_record) {
        $temp[] = $_record;
      }

      $records = $temp;

    }else{

      foreach ($records as $key => $record) {

        if(empty($record[$field1])) {
          continue;
        }

        $_records = $class2->where($field2,'=',$record[$field1])->get();

        foreach ($_records as $key => $_record) {
          $temp[] = $_record->getAttributes();
        }
        
      }

      $records = $temp;

    }

    return $records;

  }

  private function _replace($value,$key1,$key2) {
    $value = $this->_clean($value);
    return str_replace($key1, $value, $key2);
  }

  private function _clean($value) {
    $value = strip_tags($value);
    $value = trim(preg_replace('/\s\s+/', ' ', $value));
    return $value;
  }

  public function setSender() {}

  public function setReceiver() {}

  public function getSender($options = array()) {

    if(!empty($options['sender'])) {
      $sender = $options['sender']['model'];
      $senderId = $options['sender']['id'];
    }else{
      $sender = 'Person';
      $senderId = session()->get('Person.id');
    }

    return array(
      'sender' => $sender,
      'sender_id' => $senderId,
    );

  }

  public function getReceiver($receiver) {

    $receiverGroup = json_decode($receiver,true);

    $receivers = array();    
    foreach ($receiverGroup as $groups) {
      
      foreach ($groups as $group => $value) {

        switch ($group) {
          case 'group':
              $people = $this->getReceiverByGroup($value);

              if(empty($people)) {
                break;
              }

              foreach ($people as $person) {
                $receivers[$person->created_by] = $person->created_by;
              }

            break;

          case 'person':
           
              if(Schema::hasColumn($this->model->getTable(), 'created_by')) {
                $receivers[$this->model->created_by] = $this->model->created_by;
              }

            break;

        }

      }


    }

    return $receivers;

  }

  private function getReceiverByGroup($group) {

    $people = array();
    switch ($group) {

      case 'all-person-in-shop':

        if(Schema::hasColumn($this->model->getTable(), 'shop_id')) {
          $people = Service::loadModel('PersonToShop')
          ->where('shop_id','=',$this->model->shop_id)
          ->select('created_by')
          ->get();
        }else{

          switch ($this->model->modelName) {
            case 'Message':

              if($this->model->receiver == 'Shop') {
                $people = Service::loadModel('PersonToShop')
                ->where('shop_id','=',$this->model->receiver_id)
                ->select('created_by')
                ->get();
              }

              break;
          }
          
        }

        break;

    }

    if(empty($people)) {
      return null;
    }

    return $people;

  }

}

?>