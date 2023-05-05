<?php

namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Auth\User as Authenticatable;

use Illuminate\Support\Facades\Redis;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getProfile() {
      return array(
        'id' => $this->id,
        'name' => $this->name,
        'avatar' => $this->getProfileImage(),
        'online' => Redis::get('online-user:'.$this->id)
      );
    }

    public function getProfileImage() {

      $image = $this->getRelatedData('Image',array(
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));

     if(empty($image)) {
       return '/assets/images/common/avatar.png';
     }

     return $image->getImageUrl();
    }

    public function buildDataList() {
      return array(
        'id' => $this->id,
        'name' => $this->name,
        'profileImage' => $this->getProfileImage(),
      );
    }

    public function isOnline($id = null) {

      // if(empty($id) && !empty($this->id)) {
      //   $id = $this->id;
      // }

      if(empty($id)) {
        return false;
      }

      if(empty(Redis::get('online-user:'.$id))) {
        return false;
      }

      return true;
    }

    public static function buildProfile($id = null) {

      if(empty($id)) {
        return false;
      }

      $user = User::select('name')->find($id);

      if(empty($user)) {
        return null;
      }

      return array(
        'id' => $id,
        'name' => $user->name,
        'online' => Redis::get('online-user:'.$id)
      );
    }

    public function hasShop() {
      return Shop::select('id')->where([
        ['created_by','=',$this->id],
        ['deleted','=',0]
      ])->exists();
    }

    public function getUserOrShopName() {

      $shop = Shop::select('id','name','slug')->where([
        ['created_by','=',$this->id],
        ['deleted','=',0]
      ]);

      if($shop->exists()) {

        $shop = $shop->first();

        return $shop->name;
      }

      return $user->name;
    }

    public function getUserOnlineInfo() {

      // check has shop
      $shop = Shop::select('id','name','slug')->where([
        ['created_by','=',$this->id],
        ['deleted','=',0]
      ]);

      if($shop->exists()) {

        $shop = $shop->first();

        return array(
          'id' => $shop->id,
          'name' => $shop->name,
          'slug' => $shop->slug,
          // 'pageProfileImage' => $shop->getProfileImage(true),
          'last_active' => $this->dateRepo->calPassedDate($this->last_active),
          'online' => $this->isOnline($this->id)
        );
      }

      return array(
        'name' => $this->name,
        'last_active' => $this->dateRepo->calPassedDate($this->last_active),
        'online' => $this->isOnline($this->id)
      );

    }
}
