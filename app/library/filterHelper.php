<?php

namespace App\library;

use Schema;

class FilterHelper {

  private $searchQuery;
  private $filters;
  private $sort;
  private $criteria = array();
  private $model;
  private $filterOptions;
  private $sortingFields;
  private $defaultSorting = 'created_at:desc';

  private $specialFilterForModelAcceptedFields = array(
    'PersonExperience' => array(
      'filter' => array(
        'gender' => array(
          'key' => 'people_gender'
        ),
        'age' => array(
          'key' => 'people_age'
        )
      ),
      'sorting' => array(
        'name' => array(
          'table' => 'people'
        )
      ),
      'joins' => array(
        'person_experiences_join_people' => array('people', 'people.id', '=', 'person_experiences.created_by')
      )
    )
  );

  public function __construct($model = null) {
    
    if(!empty($model)) {
      $this->model = $model;
      $this->filterOptions = $model->getFilterOptions();
      $this->sortingFields = $model->getSortingFields();
    }

  }

  public function setSearchQuery($searchQuery) {
    $this->searchQuery = $searchQuery;
  }

  public function setFilters($filters) {

    if(empty($filters)) {
      $this->filters = null;
    }else{
      $this->filters = explode(',',$filters);
    }

  }

  public function setSorting($sort) {
    $this->sort = $sort;
  }

  public function setCriteria($criteria) {
    $this->criteria = $criteria;
  }

  public function buildSearchQuery($q = null) {

    if(empty($q) && !empty($this->searchQuery)) {
      $q = $this->searchQuery;
    }elseif(empty($q)) {
      return null;
    }

    $validation = new validation;

    $wordModel = Service::loadModel('Word');
    $province = Service::loadModel('Province');
    $district = Service::loadModel('District');
    $subDistrict = Service::loadModel('SubDistrict');

    $or = array();
    $and = array();

    $pattern = '/\s[+\'\'\\\\\/:;()*\-^&!<>\[\]\|]\s/';
    $_q = preg_replace($pattern, ' ', $q);

    // $wordGroupPattern = '/([\wก-๙]+|[(\"|\')]{1}[\wก-๙]+[(\+|\s)]{1}[\wก-๙]+[(\"|\')]{1})/';
    // $wordGroupPattern = '/([\w\เก-๙]+|[(\"|\')]{1}[\w\เก-๙]+[(\+|\s)]{1}[\w\เก-๙]+[(\"|\')]{1})/';
    // preg_match_all($wordGroupPattern, $_q, $words);

    $words[0] = explode(' ', $q);

    $isAddress = false;

    foreach ($words[0] as $word) {

      $word = str_replace(array('\'','"'), '', $word);
      $word = str_replace('+', ' ', $word);

      $len = mb_strlen($word);

      if($len < 2) {
        continue;
      }elseif($len < 3) {
        array_push($or,array('lookups.name','like','%'.$word.'%'));
        array_push($or,array('lookups.keyword_1','like','%'.$word.'%'));
        array_push($or,array('lookups.keyword_2','like','%'.$word.'%'));
        array_push($or,array('lookups.keyword_3','like','%'.$word.'%'));
        array_push($or,array('lookups.keyword_4','like','%'.$word.'%'));
        continue;
      }

      if($isAddress && $validation->isZipcode($word)) {
        $isAddress = false;
        array_push($or,array('lookups.address','like','%'.$word.'%'));
        continue;
      }

      if($province->where('name','like',$word)->exists() || $district->where('name','like',$word)->exists() || $subDistrict->where('name','like',$word)->exists()) {
        $isAddress = true;
        array_push($or,array('lookups.address','like','%'.$word.'%'));
        array_push($or,array('lookups.target_area','like','%'.$word.'%'));
        continue;
      }

      if($wordModel->where('word','like',$word)->exists()) {
        array_push($or,array('lookups.name','like','%'.$word.'%'));
        array_push($or,array('lookups.tags','like','%'.$word.'%'));
        continue;
      }else{
        array_push($or,array('lookups.name','like','%'.$word.'%'));
      }

      array_push($or,array('lookups.keyword_1','like','%'.$word.'%'));
      array_push($or,array('lookups.keyword_2','like','%'.$word.'%'));
      array_push($or,array('lookups.keyword_3','like','%'.$word.'%'));
      array_push($or,array('lookups.keyword_4','like','%'.$word.'%'));
      
    }

    // find Shop
    $shops = Service::loadModel('Shop')->select('id')->where('name','like','%'.$word.'%');

    if($shops->exists()) {

      foreach ($shops->get() as $shop) {
        array_push($or,array('lookups.shop_id','=',$shop->id));
      }
    }

    $query = array(); 

    if(!empty($and)) {
      $query = $and;
    }

    if(!empty($or)) {
      $query['or'] = $or;
    }

    return $query;

  }

  public function buildFilters() {

    if(!empty($this->filters)) {
      $filters = $this->filters;
    }elseif(!empty($this->filterOptions)) {

      $filters = array();
      foreach ($this->filterOptions as $filter) {

        if(empty($filter['default'])) {
          continue;
        }

        $filters[] = $filter['default'];
      }
      
    }else {
      return null;
    }

    if(empty($filters)) {
      return null;
    }

    $_filters = array();
    foreach ($filters as $filter) {

      if(!$this->filterValueValidation($filter)) {
        continue;
      }

      list($field,$value) = explode(':', $filter);

      if($field == 'f') {
        $specialFilter = $this->getSpecialFilter($value);

        if(!empty($specialFilter)) {

          if(!empty($specialFilter['operator'])) {
            $_filters[$specialFilter['key']]['operator'] = $specialFilter['operator'];
          }
          
          foreach ($specialFilter['value'] as $value) {
            $_filters[$specialFilter['key']]['value'][] = $value;
          }

        }

      }elseif($this->hasSpecialFilterForModel($field)) {

        $key = $this->getKeyForModel($field);

        $specialFilter = $this->getSpecialFilterForModel($field,$value);

        if(!empty($specialFilter)) {

          if(!empty($specialFilter['operator'])) {
            $_filters[$key]['operator'] = $specialFilter['operator'];
          }

          if(is_array(current($specialFilter['value']))) {

            $_value = array();
            foreach ($specialFilter['value'] as $value) {
              $_value[] = $value;
            }

             $_filters[$key]['value'][] = $_value;

          }else{
            $_filters[$key]['value'][] = $specialFilter['value'];
          }

        }

      }elseif(Schema::hasColumn($this->model->getTable(), $field)) {
        $_filters[$this->model->getTable().'_'.$field]['value'][] = array($this->model->getTable().'.'.$field,'=',$value);
      }

    }

    $filters = array();
    foreach ($_filters as $filter) {

      $operator = 'or';
      if(!empty($filter['operator'])) {
        $operator = $filter['operator'];
      }

      $value = array();
      if(count($filter['value']) == 1) {

        $value = current($filter['value']);

      }else{

        foreach ($filter['value'] as $_value) {
          $value[] = $_value;
        }

        if($operator == 'or') {
          $temp['or'] = $value;
          $value = $temp;
        }

      }

      $filters[] = $value;

    }

    return $filters;

  }

  public function getSpecialFilter($alias) {

    $filter = null;
    switch ($alias) {
      case 'new-arrival':
          
        $date = new Date;
        $date = date('Y-m-d H:i:s',$date->now(true,true) - 604800);
        $filter = array(
          'key' => $this->model->getTable().'_created_at',
          'operator' => 'and',
          'value' => array(
            array($this->model->getTable().'.created_at','>',$date)
          )
        );

        break;
    
    }

    return $filter;

  }

  public function getSpecialFilterForModel($field,$value) {

    $filters = null;
    switch ($this->model->modelName) {
      case 'PersonExperience':

          switch ($field) {
            case 'gender':
              
              $filters = array(
                'operator' => 'or',
                'value' => array('people.gender','=',$value)
              );

              break;

            case 'age':
              
              $range = Service::loadModel('Person')->getAgeRange($value);

              if(empty($range)) {
                break;
              }

              $filters = array(
                'operator' => 'or',
                'value' => array(
                  array('people.birth_date','>=',$range['start']),
                  array('people.birth_date','<=',$range['end'])
                )
              );

              break;
            
          }

        break;

    }

    return $filters;

  }

  public function hasSpecialFilterForModel($field,$type = 'filter') {

    if(empty($this->specialFilterForModelAcceptedFields[$this->model->modelName][$type])) {
      return false;
    }

    return array_key_exists($field, $this->specialFilterForModelAcceptedFields[$this->model->modelName][$type]);
  }

  public function getKeyForModel($field) {

    if(empty($this->specialFilterForModelAcceptedFields[$this->model->modelName]['filter'][$field]['key'])) {
      return null;
    }

    return $this->specialFilterForModelAcceptedFields[$this->model->modelName]['filter'][$field]['key'];

  }

  public function getSortingFieldForModel($field) {

    if(empty($this->specialFilterForModelAcceptedFields[$this->model->modelName]['sorting'][$field]['table'])) {
      return null;
    }

    return $this->specialFilterForModelAcceptedFields[$this->model->modelName]['sorting'][$field]['table'];

  }

  public function buildSorting($q = null) {

    if(!empty($this->sort)) {
      $sort = $this->sort;
    }elseif(!empty($this->sortingFields['default'])) {
      $sort = $this->sortingFields['default'];
    }else {
      $sort = $this->defaultSorting;
    }

    if(!$this->filterValueValidation($sort)) {
      return null;
    }

    list($sortingField,$order) = explode(':', $sort);

    if(empty($sortingField) || empty($order)) {
      return null;
    }

    $table = null;
    if($this->hasSpecialFilterForModel($sortingField,'sorting')) {
      $table = $this->getSortingFieldForModel($sortingField);
    }elseif(Schema::hasColumn($this->model->getTable(), $sortingField)) {
      $table = $this->model->getTable();
    }

    if(empty($table)) {
      return null;
    }

    if(empty($q) && !empty($this->searchQuery)) {
      $q = $this->searchQuery;
    }

    $hasData = false;
    if(!empty($q) && Schema::hasColumn($this->model->getTable(), 'name')) {
      $data = $this->model->select('id')->where('name','like',$q);
      $hasData = $data->exists();
    }

    if($hasData) {
      $ids = array();
      foreach ($data->get() as $value) {
        $ids[] = $value->id;
      }

      $orderBy['orderByRaw'] = 'FIELD('.$this->model->getTable().'.id, '.implode(' ,', $ids).') desc, '.$table.'.'.$sortingField.' '.strtolower($order);
    }else{
      $orderBy['order'] = array($table.'.'.$sortingField,strtolower($order));
    }

    return $orderBy;

  }

  public function buildCriteria() {

    return array(
      'query' => $this->buildSearchQuery(),
      'filter' => $this->buildFilters(),
      'order' => $this->buildSorting()
    );

  }

  public function filterValueValidation($value) {
    if(preg_match('/^\w+:[\w\-]+$/', $value)) {
      return true;
    }
    return false;
  }

  public function conditions($model = null) {

    if(empty($model) || empty($this->criteria)) {
      return null;
    }

    foreach ($this->criteria as $key => $value) {
      
      // switch ($key) {
      //   case 'query':
      //       $model = $this->setCondition($model,$value);
      //     break;
        
      //   case 'filter':
      //       $model = $this->setCondition($model,$value);
      //     break;

      //   case 'order':
      //       $model = $this->setCondition($model,$value);
      //     break;
      // }

      if($key === 'order') {
        continue;
      }

      $model = $this->setCondition($model,$value);

    }

    return $model;

  }

  public function setCondition($model,$conditions) {

    if(count($conditions) == 1) {
      $model = $this->_setCondition($model,$conditions);
    }else{
      $model = $model->where(function ($_query) use($conditions) {
        $_query = $this->_setCondition($_query,$conditions);
      });
    }

    return $model;

  }

  private function _setCondition($model,$conditions,$operator = 'and') {

    if(empty($conditions)) {
      return $model;
    }

    $query = $model;

    foreach ($conditions as $_operator => $value) {

      if($_operator === 'in') {

        if(is_array(current($value))) {
          
          foreach ($value as $_value) {

            if($operator === 'and') {
              $query = $query->whereIn(current($_value),next($_value));
            }else{
              $query = $query->orWhere(function ($_query) use($_value) {
                $_query->whereIn(current($_value),next($_value));
              });
            }

          }

        }else{

          if($operator === 'and') {
            $query = $query->whereIn(current($value),next($value));
          }else{
            $query = $query->orWhere(function ($_query) use($value) {
              $_query->whereIn(current($value),next($value));
            });
          }

        }

      }elseif(!empty(next($value) && !is_array(current($value)))) {

        if($operator === 'and') {
          $query = $query->where(prev($value),next($value),next($value));
        }elseif($operator === 'or') {
          $query = $query->orWhere(prev($value),next($value),next($value));
        }

      }else{

        if(($_operator !== 'and') && ($_operator !== 'or')) {
          $_operator = 'and';
        }

        if($operator === 'and') {
          $query = $query->where(function ($_query) use($value,$_operator) {
            $_query = $this->_setCondition($_query,$value,$_operator);
          });
        }elseif($operator === 'or') {
          $query = $query->orWhere(function ($_query) use($value,$_operator) {
            $_query = $this->_setCondition($_query,$value,$_operator);
          });
        }

      }

    }

    return $query;

  }

  // private function _setCondition($model,$conditions) {

  //   if(empty($conditions)) {
  //     return $model;
  //   }

  //   $query = $model;

  //   foreach ($conditions as $operator => $value) {

  //     if($operator === 'or') {

  //       if(!empty(next($value) && !is_array(current($value)))) {
  //         $query->orWhere(
  //           prev($value),
  //           next($value),
  //           next($value)
  //         );
  //       }else{
  //         $query->orWhere(function ($_query) use($value) {
  //           $_query = $this->_setCondition($_query,$value);
  //         });
  //       }

  //     }elseif($operator === 'in') {

  //       if(is_array(current($value))) {
          
  //         foreach ($value as $_value) {
  //           $model = $model->whereIn(current($_value),next($_value));
  //         }

  //       }else{
  //         $query->whereIn(current($value),next($value));
  //       }

  //     }else { // AND

  //       if(!empty(next($value) && !is_array(current($value)))) {
  //         $query->where(
  //           prev($value),
  //           next($value),
  //           next($value)
  //         );
  //       }else{
  //         $query->where(function ($_query) use($value) {
  //           $_query = $this->_setCondition($_query,$value);
  //         });
  //       }
  //     }

  //   }

  //   return $query;

  // }

  public function order($model) {

    if(empty($model) || empty($this->criteria['order'])) {
      return null;
    }

    $key = key($this->criteria['order']);
    $value = $this->criteria['order'][$key];

    if($key === 'order') {

      if(is_array(current($value))) {

        foreach ($value as $value) {
          $model = $model->orderBy(current($value),next($value));
        }

      }else{
        $model = $model->orderBy(current($value),next($value));
      }

    }elseif($key === 'orderByRaw') {

      $model = $model->orderByRaw($value);

    }

    return $model;  

  }

  public function getFilterOptions() {

    if(!empty($this->filters)) {
      $selectedfilters = $this->filters;
    }elseif(!empty($this->filterOptions)) {

      $selectedfilters = array();
      foreach ($this->filterOptions as $filter) {

        if(empty($filter['default'])) {
          continue;
        }

        $selectedfilters[] = $filter['default'];
      }
      
    }else {
      $selectedfilters = null;
    }

    $_filterOptions = array();
    foreach ($this->filterOptions as $key => $filter) {

      $_filterOptions[$key]['title'] = $filter['title'];
      $_filterOptions[$key]['input'] = $filter['input'];

      foreach ($filter['options'] as $option) {

        $select = false;
        if(in_array($option['value'], $selectedfilters)) {
          $select = true;
        }

        $_filterOptions[$key]['options'][] = array(
          'name' => $option['name'],
          'value' => $option['value'],
          'select' => $select
        ); 
      }

    }

    return $_filterOptions;

  }

  public function getSortingOptions() {

    if(!empty($this->sort)) {
      $selectedsort = $this->sort;
    }elseif(!empty($this->sortingFields['default'])) {
      $selectedsort = $this->sortingFields['default'];
    }else {
      $selectedsort = $sort = $this->defaultSorting;
    }

    if(empty($this->sortingFields)) {
      return null;
    }

    $sortingOptions['title'] = $this->sortingFields['title'];

    foreach ($this->sortingFields['options'] as $option) {

      $select = false;
      if(!empty($selectedsort) && ($selectedsort == $option['value'])) {
        $select = true;
      }

      $sortingOptions['options'][] = array(
        'name' => $option['name'],
        'value' => $option['value'],
        'select' => $select
      ); 
    }    

    return $sortingOptions;

  }

  public function getDisplayingFilterOptions() {

    if(!empty($this->filters)) {
      $selectedfilters = $this->filters;
    }elseif(!empty($this->filterOptions)) {

      $selectedfilters = array();
      foreach ($this->filterOptions as $filter) {

        if(empty($filter['default'])) {
          // $selectedfilters = $this->getDefaultFilter($filter['options']);
          continue;
        }

        $selectedfilters[] = $filter['default'];
      }
      
    }else {
      $selectedfilters = null;
    }

    $_displayingfilterOptions = array();
    foreach ($this->filterOptions as $key => $filter) {

      $_displayingfilterOptions[$key]['title'] = $filter['title'];

      foreach ($filter['options'] as $option) {

        $select = false;
        if(in_array($option['value'], $selectedfilters) || (!empty($filter['default']) && in_array($option['value'], $filter['default']))) {
          $select = true;
        }elseif(empty($selectedfilters) && empty($filter['default'])) {
          $select = true;
        }

        if($select) {
          $_displayingfilterOptions[$key]['display'][] = $option['name'];
        }

      }

    }

    return $_displayingfilterOptions;

  }

  public function getDisplayingSorting() {

    if(!empty($this->sort)) {
      $selectedsort = $this->sort;
    }elseif(!empty($this->sortingFields['default'])) {
      $selectedsort = $this->sortingFields['default'];
    }else {
      $selectedsort = $this->defaultSorting;
    }

    if(empty($this->sortingFields)) {
      return null;
    }

    $displayingSortingOptions['title'] = $this->sortingFields['title'];

    foreach ($this->sortingFields['options'] as $option) {

      if(!empty($selectedsort) && ($selectedsort == $option['value'])) {
        $displayingSortingOptions['display'][] = $option['name'];
      }
    }  

    return $displayingSortingOptions;

  }

  public function getDefaultFilter($options) {

    $filters = array();
    foreach ($options as $option) {
      $filters[] = $option['value'];
    }

    return $filters;

  }

}

?>