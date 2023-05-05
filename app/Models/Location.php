<?php

namespace App\Models;

class Location extends Model
{
  protected $table = 'locations';
  protected $fillable = ['parent_id','code','name','region','type','zip_code','sort'];
  public $timestamps  = false;

  public function getLocationName($id) {
    $location = $this->select('name')->find($id);

    if(empty($location)) {
      return null;
    }

    return $location->name;
  }

  public function getLocationPaths($id) {

    $locationPaths = array();
    
    $paths = LocationPath::where('location_id','=',$id)->get();

    foreach ($paths as $path) {

      $hasChild = false;
      if($path->path->where('parent_id','=',$path->path->id)->exists()) {
        $hasChild = true;
      }

      $locationPaths[] = array(
        'id' => $path->path->id,
        'name' => $path->path->name,
        // 'url' => $url->setAndParseUrl('product/{location_id}',array('location_id'=>$path->path->id)),
        'hasChild' => $hasChild
      );
    }

    return $locationPaths;
  }

  public function breadcrumb($id) {

    $paths = LocationPath::where('location_id','=',$id)->get();

    foreach ($paths as $path) {

      $locationPaths[] = array(
        'id' => $path->path->id,
        'name' => $path->path->name,
        // 'url' => $url->setAndParseUrl('product/{location_id}',array('location_id'=>$path->path->id)),
        'url' => '/location/'.$path->path->id
      );
    }

    return $locationPaths;
  }
}
