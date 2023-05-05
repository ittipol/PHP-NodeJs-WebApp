<?php

namespace App\Models;

use App\library\stringHelper;

class OrderShippingConfirmation extends Model
{
	protected $table = 'order_shipping_confirmations';
	protected $fillable = ['order_id','detail','created_by'];

	public $imageTypeAllowed = array(
	  'photo' => array(
	    'limit' => 20
	  )
	);

	public function user() {
	  return $this->hasOne('App\Models\User','id','created_by');
	}

	public function buildDataDetail() {

		$imageTotal = Image::where([
		  'model' => $this->modelName,
		  'model_id' => $this->id,
		  'image_type_id' => 1
		])->count();

		$images = [];
		if($imageTotal > 0) {

		  $_images = $this->getRelatedData('Image',array(
		    'conditions' => array(
		      array('image_type_id','=',1)
		    ),
		    'fields' => array('model','model_id','filename','image_type_id')
		  ));

		  foreach ($_images as $image) {
		    $images[] = $image->buildSlide();
		  }

		}

		foreach (StringHelper::getUrlFromString($this->detail) as $value) {
		  $this->detail = str_replace($value, '<a href="'.$value.'">'.StringHelper::truncString($value,60,true,true).'</a>', $this->detail);
		}

		return [
			'id' => $this->id,
			'seller' => $this->user->getUserOrShopName(),
			'detail' => $this->detail,
			'images' => $images,
		];

	}

}
