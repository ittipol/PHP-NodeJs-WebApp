<?php

namespace App\Models;

class UserBlocking extends Model
{
  protected $table = 'user_blocking';
  protected $fillable = ['model','model_id','blocked_user_id'];
  public $timestamps  = false;

  public function getBlockedData($userId) {

    $blockedData = array();

    $_blockedData = $this->select('model','model_id')->where('user_id','=',$userId)->get();

    foreach ($_blockedData as $__blockedData) {

      switch ($__blockedData->model) {
        case 'Item':
          $blockedData['item'][] = $__blockedData->model_id;
          break;
        
        case 'User':
          $blockedData['user'][] = $__blockedData->model_id;
          break;

        case 'Shop':
          $blockedData['shop'][] = $__blockedData->model_id;
          break;
      }
    }

    return $blockedData;
  }

  public function buildDataList() {

    $data = array();

    switch ($this->model) {
      case 'User':
        $_user = User::select('name','upgraded')->find($this->model_id);
        
        $data[] = array(
          'label' => 'ผู้ใช้',
          'name' => $_user->name,
          'url' => '/profile/'.$this->model_id
        );

        if($_user->upgraded) {
          // Get Shop Info
          $shop = Shop::select('name','slug')->where([
            ['created_by','=',$this->model_id],
            ['deleted','=',0]
          ])->first();

          $data[] = array(
            'label' => 'ร้านขายสินค้า',
            'name' => $shop->name,
            'url' => '/shop/page/'.$shop->slug
          );
        }

        break;
      
      case 'Item':
        $data[] = array(
          'label' => 'รายการขาย',
          'name' => Item::select('title')->find($this->model_id)->title,
          'url' => '/ticket/view/'.$this->model_id
        );

        break;

      // case 'Shop':

      //   $shop = Shop::select('title','slug')->find($this->model_id);

      //   $data[] = array(
      //     'label' => 'รายการขาย',
      //     'name' => $shop->name,
      //     'url' => '/shop/page/'.$shop->slug
      //   );

      //   break;
    }

    return array(
      'model' => $this->model,
      'modelId' => $this->model_id,
      'data' => $data
    );

  }
}
