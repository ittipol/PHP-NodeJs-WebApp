<?php

namespace App\library;

use App\library\cache;

class ModelData {

	private $model;
	private $data = array();

	public function __construct($model = null) {
	  $this->model = $model;
	}

	public function loadData($options = array()) {

    if(empty($this->model)) {
      return false;
    }

    if(empty($options['json'])) {
      $options['json'] = array();
    }

    if(empty($options['models'])) {
      $options['models'] = array();
    }

    $modeldNames = $this->model->getModelRelations();

    if(!empty($modeldNames)){

      foreach ($modeldNames as $key => $modelName) {

        if($this->model->modelName == $modelName) {
          continue;
        }

        if(!empty($options['models']) && !in_array($modelName, $options['models'])) {
          continue;
        }

        $json = in_array($modelName, $options['json']);

        $this->_getRelatedData($modelName,$json);

      }

    }

  }

  private function _getRelatedData($modelName,$json = false) {

    $data = array();
    switch ($modelName) {
      case 'Address':
        $data = $this->loadAddress();
        break;

      case 'Image':
        $data = $this->loadImage();
        break;

      case 'Tagging':
        $data = $this->loadTagging();
        break;

      case 'Contact':
        $data = $this->loadContact();
        break;

    }

    if($json) {
      $data = json_encode($data);
    }

    $this->data[$modelName] = $data; 

  }

  public function loadAddress() {

    $address = $this->model->getRelatedData('Address',
      array(
        'first' => true,
        'fields' => array('address','province_id','district_id','sub_district_id','description','latitude','longitude'),
        'order' => array('id','DESC')
      )
    );

    if(empty($address)){
      return array();
    }

    return $address->buildModelData();

  }

  public function loadImage() {

    $cache = new cache;

    $images = $this->model->getRelatedData('Image',array(
      'fields' => array('id','model','model_id','filename','description','image_type_id')
    ));

    if(empty($images)){
      return array();
    }

    $_images = array();
    foreach ($images as $image) {
      $_images[] = array_merge($image->buildModelData(),array(
        '_xs_url' => $cache->getCacheImageUrl($image,'xs')
      ));
    } 

    return $_images;

  }

  public function loadTagging() {
    $taggings = $this->model->getRelatedData('Tagging',
      array(
        'fields' => array('word_id')
      )
    );

    if(empty($taggings)) {
      return array();
    }

    $words = array();
    foreach ($taggings as $tagging) {
      $words[] = $tagging->buildModelData();
    }

    return $words;

  }

  public function loadContact() {
    $contact = $this->model->getRelatedData('Contact',array(
      'first' => true,
      'fields' => array('phone_number','fax','email','website','facebook','line')
    ));

    if(empty($contact)) {
      return array();
    }

    return $contact->buildModelData();

  }

  public function set($index,$value) {
    $this->data[$index] = $value;
  }

  public function getModelData() {
    return array_merge(
      $this->model->buildModelData(),
      $this->data
    );
  }

  public function build($onlyData = false) {
    
    if($onlyData) {
      return $this->getModelData();
    }

    return array(
      '_modelData' => $this->getModelData()
    );

  }

}