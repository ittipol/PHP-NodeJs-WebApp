<?php

namespace App\Models;

use App\library\cache;
use App\library\url;
use App\library\date;
use App\library\currency;
use App\library\format;
use App\library\stringHelper;
use App\library\themeColor;
use Auth;

class Item extends Model
{
  protected $table = 'items';
  protected $fillable = ['slug','title','description','place_location','price','original_price','date_type','date_1','date_2','contact','use_specific_contact','quantity','publishing_type','grading','use_shop_location','cancel_option','cancel_reason','active_date','expiration_date','approved','pinned','theme_color_id','deleted','shop_id','created_by'];

  public $imageTypeAllowed = array(
    'photo' => array(
      'limit' => 10
    )
  );

  private $rePostPeriodDays = 259200; // 3 days // 86400
  // private $extendExpirePeriodDays = 864000; // 10 days // 86400

  private $dateType = array(
    1 => 'ช่วงวันและเวลาที่ใช้งานได้',
    2 => 'วันที่แสดง',
    3 => 'วันที่เดินทาง',
    0 => 'ไม่ระบุ',
  );

  // public function shop() {
  //   return $this->hasOne('App\Models\Shop','id','shop_id');
  // }

  public function getShortTitle() {
    if(empty($this->title)) {
      return null;
    }
    return StringHelper::truncString($this->title,80,true,true);
  }

  public function getShortDesc() {
    if(empty($this->description)) {
      return null;
    }
    return StringHelper::truncString($this->description,220,true,true);
  }

  public function getPrice($currencyFormat = true) {

    $originalPrice = null;
    $save = null;

    if(!empty($this->original_price)) {
      $originalPrice = $this->original_price;

      if($this->original_price > $this->price) {
        $save = Format::percent(100 - (($this->price * 100) / $this->original_price)) . '%';
      }
    }

    return [
      'price' => $currencyFormat ? Currency::format($this->price) : number_format($this->price, 0, '.', ''),
      'original_price' => $currencyFormat ? Currency::format($originalPrice) : number_format($originalPrice, 0, '.', ''),
      'save' => $save,
    ];
  }

  public function getDateType() {
    return $this->dateType;
  }

  public function getDateTypeById($id) {
    
    if(empty($this->dateType[$id])) {
      return null;
    }

    return $this->dateType[$id];
  }

  public function getExistingItem($id,$fields = '*') {
    return $this->select($fields)
      ->where([
        ['id','=',$id],
        ['deleted','=',0]
      ])
      ->first();
  }

  public function hasShop() {
    // return Shop::select('id')->where([
    //   ['created_by','=',$this->created_by],
    //   ['deleted','=',0]
    // ])->exists();
    return User::where('id','=',$this->created_by)->first()->hasShop();
  }

  public function buildDataList() {

    $cache = new Cache;

    // GET Images
    $imageTotal = Image::where([
      'model' => $this->modelName,
      'model_id' => $this->id,
      'image_type_id' => 1
    ])->count();

    $image = null;
    if($imageTotal > 0) {

      $image = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',1)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));

      $image = array(
        '_preview_url' => $cache->getCacheImageUrl($image,'card_scale'),
        'formation' => $image->getFormation()
      );

    }

    if(empty($image)) {
      $_banner = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',3)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));
        
      if(!empty($_banner)) {
        $image = array(
          '_preview_url' => $cache->getCacheImageUrl($_banner,'card_scale'),
          'formation' => $_banner->getFormation()
        );
      }
    }

    $originalPrice = null;
    $save = null;
    if(!empty($this->original_price)) {
      $originalPrice = Currency::format($this->original_price);

      if($this->original_price > $this->price) {
        $save = Format::percent(100 - (($this->price * 100) / $this->original_price)) . '%';
      }
    }

    // $grading = null;
    // switch ($this->grading) {
    //   case 1:
    //     $grading = 'สินค้าใหม่';
    //     break;
      
    //   case 2:
    //     $grading = 'สินค้ามือสอง';
    //     break;

    //   case 3:
    //     $grading = 'ผลิตภัณฑ์จากธุรกิจครัวเรือน';
    //     break;
    // }

    // $publishingType = null;
    // switch ($this->publishing_type) {
    //   case 1:
    //     $publishingType = 'ขาย';
    //     break;
      
    //   case 2:
    //     $publishingType = 'ซื้อ';
    //     break;
    // }

    $description = null;
    $descLen = 120 - StringHelper::strLen($this->title);
    if($descLen > 10) {
      $description = StringHelper::truncString($this->description,$descLen,true,true);
    }

    $blockedUser = false;
    $blockedItem = false;
    if(Auth::check() && (Auth::user()->id != $this->created_by)) {

      // if(empty($this->shop_id)) {
      //   $blockedUser = UserBlocking::where([
      //     ['model','=','User'],
      //     ['model_id','=',$this->created_by],
      //     ['user_id','=',Auth::user()->id],
      //   ])->exists();
      // }else {
      //   $blockedUser = UserBlocking::where([
      //     ['model','=','Shop'],
      //     ['model_id','=',$this->shop_id],
      //     ['user_id','=',Auth::user()->id],
      //   ])->exists();
      // }

      $blockedUser = UserBlocking::where([
        ['model','=','User'],
        ['model_id','=',$this->created_by],
        ['user_id','=',Auth::user()->id],
      ])->exists();

      $blockedItem = UserBlocking::where([
        ['model','=','Item'],
        ['model_id','=',$this->id],
        ['user_id','=',Auth::user()->id],
      ])->exists();
    }

    // Get Expired date
    // switch ($this->date_type) {
    //   case 1:
    //       $expireDate = strtotime($this->date_2);
    //     break;

    //   case 2:
    //       $expireDate = strtotime($this->date_1);
    //     break;

    //   case 3:
    //       $expireDate = strtotime($this->date_1);
    //     break;
      
    //   default:
    //       $expireDate = 0;
    //     break;
    // }

    $expireDate = $this->getExpireDateTimeStamp();

    return array(
      'id' => $this->id,
      'slug' => $this->slug,
      'title' => $this->title,
      'description' => $description,
      'place_location' => $this->place_location,
      'price' => Currency::format($this->price),
      'original_price' => $originalPrice,
      'save' => $save,
      // 'grading' => $grading,
      // 'publishingType' => $publishingType,
      'date_type' => $this->date_type,
      'dateTypeLabel' => $this->getDateTypeById($this->date_type),
      'date_1' => $this->dateRepo->covertDateToSting($this->date_1),
      'date_2' => $this->dateRepo->covertDateToSting($this->date_2),
      'expireDate' => $expireDate,
      // 'shop_id' => $this->shop_id,
      'hasShop' => $this->hasShop(),
      'created_by' => $this->created_by,
      'created_at' => $this->dateRepo->calPassedDate($this->created_at->format('Y-m-d H:i:s')),
      'category' => $this->getCategoryDetail(),
      'image' => $image,
      'theme' => ThemeColor::getThemeById($this->theme_color_id),
      // 'imageTotal' => $imageTotal,
      // 'approved' => $this->approved,
      // 'pullPost' => $this->checkRePost(),
      // 'extendExpire' => $this->chechCanExtendExpire(),
      // 'extendDays' => $this->getExtendDays(),
      // 'expiredAt' => $this->dateRepo->covertDateTimeToSting($this->expiration_date),
      'locations' => $this->getLocationBreadcrumb(),
      'owner' => $this->getOwnerInfo(),
      'blockedUser' => $blockedUser,
      'blockedItem' => $blockedItem,
      'user' => User::buildProfile($this->created_by)
    );

  }

  public function getMetaImage() {

    $image = $this->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',1)
      ),
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

    if(empty($image)) {
      $image = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',3)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));
        
      if(empty($image)) {
        return null;
      }
    }

    return $image->getImageUrl();
  }

  // public function getPreview() {

  //   $_preview = $this->getRelatedData('Image',array(
  //     'conditions' => array(
  //       array('image_type_id','=',4)
  //     ),
  //     'fields' => array('model','model_id','filename','image_type_id'),
  //     'first' => true
  //   ));

  //   $preview = null;
  //   if(!empty($_preview)) {
  //     $preview = $_preview->getImageUrl();
  //   }

  //   return $preview;
  // }

  public function buildDataList2() {

    $cache = new Cache;

    // GET Images
    $imageTotal = Image::where([
      'model' => $this->modelName,
      'model_id' => $this->id,
      'image_type_id' => 1
    ])->count();

    $image = null;
    if($imageTotal > 0) {

      $image = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',1)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));

      $image = array(
        '_preview_url' => $cache->getCacheImageUrl($image,'card_scale'),
        'formation' => $image->getFormation()
      );

    }

    if(empty($image)) {
      $_banner = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',3)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));
        
      if(!empty($_banner)) {
        $image = array(
          '_preview_url' => $cache->getCacheImageUrl($_banner,'list_pw_scale'),
          'formation' => $_banner->getFormation()
        );
      }
    }

    $originalPrice = null;
    $save = null;
    if(!empty($this->original_price)) {
      $originalPrice = Currency::format($this->original_price);

      if($this->original_price > $this->price) {
        $save = Format::percent(100 - (($this->price * 100) / $this->original_price)) . '%';
      }
    }

    $grading = null;
    // switch ($this->grading) {
    //   case 1:
    //     $grading = 'สินค้าใหม่';
    //     break;
      
    //   case 2:
    //     $grading = 'สินค้ามือสอง';
    //     break;

    //   case 3:
    //     $grading = 'ผลิตภัณฑ์จากธุรกิจครัวเรือน';
    //     break;
    // }

    $publishingType = null;
    // switch ($this->publishing_type) {
    //   case 1:
    //     $publishingType = 'ขาย';
    //     break;
      
    //   case 2:
    //     $publishingType = 'ซื้อ';
    //     break;
    // }

    $description = null;
    $descLen = 120 - StringHelper::strLen($this->title);
    if($descLen > 10) {
      $description = StringHelper::truncString($this->description,$descLen,true,true);
    }

    $blockedItem = false;
    if(Auth::check() && (Auth::user()->id != $this->created_by)) {
      $blockedItem = UserBlocking::where([
        ['model','=','Item'],
        ['model_id','=',$this->id],
        ['user_id','=',Auth::user()->id],
      ])->exists();
    }

    // Get Expired date
    // switch ($this->date_type) {
    //   case 1:
    //       $expireDate = strtotime($this->date_2);
    //     break;

    //   case 2:
    //       $expireDate = strtotime($this->date_1);
    //     break;

    //   case 3:
    //       $expireDate = strtotime($this->date_1);
    //     break;
      
    //   default:
    //       $expireDate = 0;
    //     break;
    // }

    $expireDate = $this->getExpireDateTimeStamp();

    return array(
      'id' => $this->id,
      'slug' => $this->slug,
      'title' => $this->title,
      'description' => $description,
      'price' => Currency::format($this->price),
      'original_price' => $originalPrice,
      'save' => $save,
      'grading' => $grading,
      'publishingType' => $publishingType,
      // 'shop_id' => $this->shop_id,
      'hasShop' => $this->hasShop(),
      'created_by' => $this->created_by,
      'created_at' => $this->dateRepo->calPassedDate($this->created_at->format('Y-m-d H:i:s')),
      'category' => $this->getCategoryDetail(),
      'image' => $image,
      'theme' => ThemeColor::getThemeById($this->theme_color_id),
      'date_type' => $this->date_type,
      'dateTypeLabel' => $this->getDateTypeById($this->date_type),
      'date_1' => $this->dateRepo->covertDateToSting($this->date_1),
      'date_2' => $this->dateRepo->covertDateToSting($this->date_2),
      'expireDate' => $expireDate,
      // 'imageTotal' => $imageTotal,
      // 'approved' => $this->approved,
      // 'pullPost' => $this->checkRePost(),
      // 'extendExpire' => $this->chechCanExtendExpire(),
      // 'extendDays' => $this->getExtendDays(),
      // 'expiredAt' => $this->dateRepo->covertDateTimeToSting($this->expiration_date),
      // 'owner' => $this->getOwnerInfo(),
      'blockedItem' => $blockedItem,
      'user' => User::buildProfile($this->created_by)
    );
  }

  public function buildManagingDataList() {

    $cache = new Cache;

    // GET Images
    $imageTotal = Image::where([
      'model' => $this->modelName,
      'model_id' => $this->id,
      'image_type_id' => 1
    ])->count();

    $image = null;
    if($imageTotal > 0) {

      $image = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',1)
        ),
        'first' => true,
        'fields' => array('model','model_id','filename','image_type_id')
      ));

      $image = array(
        '_preview_url' => $cache->getCacheImageUrl($image,'card_scale'),
        'formation' => $image->getFormation()
      );

    }

    if(empty($image)) {
      $_banner = $this->getRelatedData('Image',array(
        'conditions' => array(
          array('image_type_id','=',3)
        ),
        'fields' => array('model','model_id','filename','image_type_id'),
        'first' => true
      ));
        
      if(!empty($_banner)) {
        $image = array(
          '_preview_url' => $cache->getCacheImageUrl($_banner,'list_pw_scale'),
          'formation' => $_banner->getFormation()
        );
      }
    }

    $originalPrice = null;
    $save = null;
    if(!empty($this->original_price)) {
      $originalPrice = Currency::format($this->original_price);

      if($this->original_price > $this->price) {
        $save = Format::percent(100 - (($this->price * 100) / $this->original_price)) . '%';
      }
    }

    // $grading = null;
    // switch ($this->grading) {
    //   case 1:
    //     $grading = 'สินค้าใหม่';
    //     break;
      
    //   case 2:
    //     $grading = 'สินค้ามือสอง';
    //     break;

    //   case 3:
    //     $grading = 'ผลิตภัณฑ์จากธุรกิจครัวเรือน';
    //     break;
    // }

    // $publishingType = null;
    // switch ($this->publishing_type) {
    //   case 1:
    //     $publishingType = 'ขาย';
    //     break;
      
    //   case 2:
    //     $publishingType = 'ซื้อ';
    //     break;
    // }

    $description = null;
    $descLen = 120 - StringHelper::strLen($this->title);
    if($descLen > 10) {
      $description = StringHelper::truncString($this->description,$descLen,true,true);
    }

    // $blockedUser = false;
    // $blockedItem = false;
    // if(Auth::check() && (Auth::user()->id != $this->created_by)) {

    //   // if(empty($this->shop_id)) {
    //   //   $blockedUser = UserBlocking::where([
    //   //     ['model','=','User'],
    //   //     ['model_id','=',$this->created_by],
    //   //     ['user_id','=',Auth::user()->id],
    //   //   ])->exists();
    //   // }else {
    //   //   $blockedUser = UserBlocking::where([
    //   //     ['model','=','Shop'],
    //   //     ['model_id','=',$this->shop_id],
    //   //     ['user_id','=',Auth::user()->id],
    //   //   ])->exists();
    //   // }

    //   $blockedUser = UserBlocking::where([
    //     ['model','=','User'],
    //     ['model_id','=',$this->created_by],
    //     ['user_id','=',Auth::user()->id],
    //   ])->exists();

    //   $blockedItem = UserBlocking::where([
    //     ['model','=','Item'],
    //     ['model_id','=',$this->id],
    //     ['user_id','=',Auth::user()->id],
    //   ])->exists();
    // }

    return array(
      'id' => $this->id,
      'title' => $this->title,
      'description' => $description,
      'price' => Currency::format($this->price),
      'original_price' => $originalPrice,
      'save' => $save,
      // 'grading' => $grading,
      // 'publishingType' => $publishingType,
      // 'shop_id' => $this->shop_id,
      // 'hasShop' => $this->hasShop(),
      'created_by' => $this->created_by,
      'created_at' => $this->dateRepo->calPassedDate($this->created_at->format('Y-m-d H:i:s')),
      'category' => $this->getCategoryDetail(),
      'image' => $image,
      // 'imageTotal' => $imageTotal,
      'theme' => ThemeColor::getThemeById($this->theme_color_id),
      'approved' => $this->approved,
      'pullPost' => $this->checkRePost(),
      // 'extendExpire' => $this->chechCanExtendExpire(),
      // 'extendDays' => $this->getExtendDays(),
      // 'expiredAt' => $this->dateRepo->covertDateTimeToSting($this->expiration_date),
      // 'owner' => $this->getOwnerInfo(),
      // 'blockedUser' => $blockedUser,
      // 'blockedItem' => $blockedItem,
      // 'user' => User::buildProfile($this->created_by)
    );

  }

  public function buildDataDetail() {

    // GET Images
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

    // Banner
    $_banner = $this->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',3)
      ),
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

    $banner = null;
    if(!empty($_banner)) {
      $banner = $_banner->getImageUrl();
    }

    $originalPrice = null;
    $save = null;
    if(!empty($this->original_price)) {
      $originalPrice = Currency::format($this->original_price);

      if($this->original_price > $this->price) {
        $save = Format::percent(100 - (($this->price * 100) / $this->original_price)) . '%';
      }
    }

    $_hashtags = array();
    foreach (StringHelper::getHashtagFromString(strip_tags($this->description)) as $key => $value) {
      // $value = strip_tags($value);

      $_hashtags[] = array(
        'pattern' => '#__'.$key.'__#',
        'replacement' => $value
      );

      $this->description = preg_replace('/'.$value.'/', '#__'.$key.'__#', $this->description, 1);

      // $this->description = str_replace($value, '<a href="/hashtag/'.substr($value, 1).'">'.$value.'</a>', $this->description);
      
      // search
      // $this->description = str_replace($value, '<a href="/?q=%23'.substr($value, 1).'">'.$value.'</a>', $this->description);
    }

    // Replacing hashtag
    foreach ($_hashtags as $hashtag) {
      $this->description = str_replace($hashtag['pattern'], '<a href="/hashtag/'.substr($hashtag['replacement'], 1).'">'.$hashtag['replacement'].'</a>', $this->description);
    }

    // foreach (StringHelper::getUrlFromString($this->description) as $value) {
    //   // if(strpos($value, ',')) {
    //   //   continue;
    //   // }

    //   $this->description = str_replace($value, '<a href="'.$value.'">'.StringHelper::truncString($value,60,true,true).'</a>', $this->description);
    // }

    // $blocked = false;
    // if(Auth::check() && (Auth::user()->id != $this->created_by)) {
    //   $blocked = UserBlocking::where(function($q) use($this) {
    //     $q
    //     ->where([
    //       ['model','=','User'],
    //       ['model_id','=',$this->created_by]
    //     ])
    //     ->orWhere([
    //       ['model','=','Item'],
    //       ['model_id','=',$this->id]
    //     ]);
    //   })
    //   ->where('user_id','=',Auth::user()->id)->exists();
    // }

    // switch ($this->date_type) {
    //   case 1:
    //       $expireDate = strtotime($this->date_2);
    //     break;

    //   case 2:
    //       $expireDate = strtotime($this->date_1);
    //     break;

    //   case 3:
    //       $expireDate = strtotime($this->date_1);
    //     break;
      
    //   default:
    //       $expireDate = 0;
    //     break;
    // }

    $expireDate = $this->getExpireDateTimeStamp();

    return array(
      'id' => $this->id,
      'slug' => $this->slug,
      'title' => $this->title,
      'description' => nl2br($this->description),
      'place_location' => $this->place_location,
      'price' => Currency::format($this->price),
      'original_price' => $originalPrice,
      'save' => $save,
      'cancel_option' => $this->cancel_option,
      'quantity' => $this->quantity,
      'date_type' => $this->date_type,
      'dateTypeLabel' => $this->getDateTypeById($this->date_type),
      'date_1' => $this->dateRepo->covertDateToSting($this->date_1),
      'date_2' => $this->dateRepo->covertDateToSting($this->date_2),
      'expireDate' => $expireDate,
      'expired' => $this->isExpired($expireDate),
      // 'contact' => nl2br($contact),
      // 'grading' => $grading,
      // 'publishingType' => $publishingType,
      'created_by' => $this->created_by,
      'created_at' => $this->dateRepo->calPassedDate($this->created_at->format('Y-m-d H:i:s')),
      'category' => $this->getCategoryName(),
      'images' => $images,
      'imageTotal' => $imageTotal,
      'banner' => $banner,
      'theme' => ThemeColor::getThemeById($this->theme_color_id),
      'approved' => $this->approved,
      'pullPost' => $this->checkRePost(),
      'owner' => $this->getOwnerInfo(),
      'hasShop' => $this->hasShop()
    );
  }

  public function getContact() {

    if($this->use_specific_contact) {
      // check account upgraded

      // if(User::find($this->created_by)->upgraded;) {
      //   $contact = Shop::select('contact')->where([
      //     ['id','=',$this->shop_id],
      //     ['deleted','=',0]
      //   ])->first()->contact; // shop

      // }else{
      //   $contact = Auth::user()->contact; // user account
      // }

      $contact = Shop::select('contact')->where([
        ['id','=',$this->shop_id],
        ['deleted','=',0]
      ])
      ->orderBy('id','desc')->first()->contact; // shop

    }else {
      $contact = $this->contact; // item contact
    }

    // foreach (StringHelper::getUrlFromString($contact) as $value) {
    //   $contact = str_replace($value, '<a href="'.$value.'">'.StringHelper::truncString($value,60,true,true).'</a>', $contact);
    // }

    return $contact;
  }

  public function getRePostPeriodDays() {
    return $this->rePostPeriodDays;
  }

  public function checkRePost() {
    $allowed = false;
    $daysLeft = '';

    $timeDiff = time() - strtotime($this->active_date);

    if($timeDiff >= $this->rePostPeriodDays) {
      $allowed = true;
    }else {
      $daysLeft = $this->dateRepo->findRemainingDays($this->rePostPeriodDays - $timeDiff);
    }

    return array(
      'allowed' => $allowed,
      'daysLeft' => $daysLeft
    );
  }

  public function getExtendDays($userId = null) {

    if(empty($userId)) {
      $userId = $this->created_by;
    }

    if(Shop::select('id')->where('created_by','=',$userId)->exists()) {
      return 50;
    }

    return 30;
  }


  // public function getExpirePeriodDays() {
  //   return $this->extendExpirePeriodDays;
  // }

  // public function chechCanExtendExpire() {

  //   $allowed = false;
  //   $daysLeft = null;

  //   $timeDiff = strtotime($this->expiration_date) - time();

  //   if($timeDiff < $this->extendExpirePeriodDays) {
  //     $allowed = true;
  //   }else {
  //     $daysLeft = $this->dateRepo->findRemainingDays($timeDiff - $this->extendExpirePeriodDays);
  //   }

  //   return array(
  //     'allowed' => $allowed,
  //     'daysLeft' => $daysLeft
  //   );

  // }

  // public function checkIsExpire() {

  //   if($this->expiration_date < date('Y-m-d H:i:s')) {
  //     return true;
  //   }

  //   return false;
  // }

  public function getCategoryDetail() {

    $itemToCategory = $this->getRelatedData('ItemToCategory',array(
      'fields' => array('category_id'),
      'first' => true
    ));

    if(empty($itemToCategory)) {
      return null;
    }

    $category = $itemToCategory->category;

    return array(
      'name' => $category->name,
      'image' => $category->getImagePath(),
      'url' => '/category/'.$category->slug
    );
  }

  public function getCategoryId() {

    $itemToCategory = $this->getRelatedData('ItemToCategory',array(
      'fields' => array('category_id'),
      'first' => true
    ));

    if(empty($itemToCategory)) {
      return null;
    }

    return $itemToCategory->category_id;
  }

  public function getCategoryName() {

    $itemToCategory = $this->getRelatedData('ItemToCategory',array(
      'fields' => array('category_id'),
      'first' => true
    ));

    if(empty($itemToCategory)) {
      return null;
    }

    return $itemToCategory->category->name;
  }

  public function getCategoryPaths() {

    $itemToCategory = ItemToCategory::where('item_id','=',$this->id)->select('category_id');

    if(!$itemToCategory->exists()) {
      return null;
    }

    $categoryModel = new Category;

    return $categoryModel->getCategoryPaths($itemToCategory->first()->category_id);
  }

  public function getCategoryBreadcrumb() {

    $itemToCategory = ItemToCategory::where('item_id','=',$this->id)->select('category_id');

    if(!$itemToCategory->exists()) {
      return null;
    }

    $categoryModel = new Category;

    return $categoryModel->breadcrumb($itemToCategory->first()->category_id);
  }

  public function getCategoryPathName($separate = ' / ') {

    $paths = $this->getCategoryPaths();

    $pathName = '';
    if(!empty($paths)) {

      $_path = array();
      foreach ($paths as $path) {
        $_path[] = $path['name'];
      }

      $pathName = implode($separate, $_path);

    }

    return $pathName;
  }

  public function getLocationId() {
    
    $itemToLocation = $this->getRelatedData('ItemToLocation',array(
      'fields' => array('location_id'),
      'first' => true
    ));

    if(empty($itemToLocation)) {
      return null;
    }

    return $itemToLocation->location_id;
  }

  public function getLocationName() {

    $itemToLocation = $this->getRelatedData('ItemToLocation',array(
      'fields' => array('location_id'),
      'first' => true
    ));

    if(empty($itemToLocation)) {
      return null;
    }

    return $itemToLocation->location->name;
  }

  public function getLocationPaths() {

    $itemToLocation = ItemToLocation::where('item_id','=',$this->id)->select('location_id');

    if(!$itemToLocation->exists()) {
      return null;
    }

    $LocationModel = new Location;

    return $LocationModel->getLocationPaths($itemToLocation->first()->location_id);
  }

  public function getLocationBreadcrumb() {

    if($this->use_shop_location) {
      $location = ShopToLocation::select('location_id')->where('shop_id','=',$this->shop_id);
    }else{
      $location = ItemToLocation::select('location_id')->where('item_id','=',$this->id);
    }

    if(!$location->exists()) {
      return null;
    }

    $LocationModel = new Location;

    return $LocationModel->breadcrumb($location->first()->location_id);
  }

  public function getOwnerInfo() {

    $user = User::select('id','name','last_active')->find($this->created_by);

    if(empty($user)) {
      return [];
    }

    return $user->getUserOnlineInfo();
  }

  public function buildForChatRoom() {
    return array(
      'id' => $this->id,
      'title' => $this->title,
      'url' => '/item/view/'.$this->id
    );
  }

  public function buildForCart() {

    $cache = new Cache;

    $image = $this->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',1)
      ),
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

    $_image = '';
    if(!empty($image)) {
      $_image = array(
        '_preview_url' => $cache->getCacheImageUrl($image,'sm_scale'),
        'formation' => $image->getFormation()
      );
    }

    return [
      'itemId' => $this->id,
      'name' => $this->title,
      'price' => $this->getPrice(),
      'image' => $_image
    ];

  }

  public function getItemImage($cacheAlias,$imageTypeId = 1) {

    $cache = new Cache;

    $image = $this->getRelatedData('Image',array(
      'conditions' => array(
        array('image_type_id','=',$imageTypeId)
      ),
      'fields' => array('model','model_id','filename','image_type_id'),
      'first' => true
    ));

    return [
      '_preview_url' => $cache->getCacheImageUrl($image,$cacheAlias),
      'formation' => $image->getFormation()
    ];

  }

  public function getExpireDateTimeStamp() {

    switch ($this->date_type) {
      case 1:
          $expireDate = strtotime($this->date_2);
        break;

      case 2:
          $expireDate = strtotime($this->date_1);
        break;

      case 3:
          $expireDate = strtotime($this->date_1);
        break;
      
      default:
          $expireDate = 0;
        break;
    }

    return $expireDate;
  }

  public function isExpired($expireDate = null) {
    if(!empty($expireDate)) {
      return (($expireDate - time()) < 0);
    }

    return (($this->getExpireDateTimeStamp() - time()) < 0);
  }

  public function checkIsItemOwner() {

    if(Auth::guest()) {
      return false;
    }

    if(Auth::user()->id != $this->created_by) {
      return false;
    }

    return true;
  }

}
