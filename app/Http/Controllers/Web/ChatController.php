<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\library\service;
use App\library\token;
use App\library\snackbar;
use Auth;
use Redirect;

class ChatController extends Controller
{
  public function __construct() {
    $this->botDisallowed();
  }

  public function chatRoom($roomId) {

    $room = Service::loadModel('ChatRoom')->select('model','model_id')->find($roomId);

    if(empty($room)) {
      return $this->error('ไม่สามารถแชทได้');
    }

    switch ($room->model) {
      case 'Item':
        return $this->itemChat($room->model_id,$roomId);
        break;

      case 'Shop':
        return $this->shopChat(Service::loadModel('Shop')->select('id','slug','name','created_by')->find($room->model_id),$roomId);
        break;
    }

    return $this->error('ไม่สามารถแชทได้');
  }

  public function itemChat($itemId,$roomId = null) {

    $item = Service::loadModel('Item')->select('id','title','cancel_option','approved','deleted','expiration_date','created_by')->find($itemId);

    if(empty($item)) {
      return $this->error('ไม่พบรายการขาย');
    }

    if($item->deleted == 1) {
      return $this->error('รายการขาย "'.$item->title.'" ถูกลบแล้ว');
    }

    if($item->cancel_option > 0) {
      return $this->error('รายการขาย "'.$item->title.'" ถูกยกเลิกแล้ว');
    }

    // if($item->expiration_date < date('Y-m-d H:i:s')) {
    //   return $this->error('รายการขาย "'.$item->title.'" หมดอายุแล้ว');
    // }

    $room = Service::loadModel('ChatRoom')
    ->select('chat_rooms.id','chat_rooms.room_key')
    ->join('user_in_chat_room', 'user_in_chat_room.chat_room_id', '=', 'chat_rooms.id');

    if(!empty($roomId)) {

      $room = $room->where([
        ['chat_rooms.id','=',$roomId],
        ['user_in_chat_room.user_id','=',Auth::user()->id]
      ])
      ->first();

      if(empty($room)) {
        return $this->error('ไม่สามารถแชทได้');
      }

      $this->setReadMessage($room->id,Auth::user()->id);

    }else {

      if($item->created_by == Auth::user()->id) {
        return $this->error('ไม่สามารถแชทได้');
      }

      $room = $room->where([
        ['chat_rooms.model','=','Item'],
        ['chat_rooms.model_id','=',$itemId],
        ['user_in_chat_room.user_id','=',Auth::user()->id]
      ])
      ->first();

      if(empty($room)) {
         // create new one
        $room = $this->createRoom('Item',$itemId,array($item->created_by,Auth::user()->id));
      }else {
        // update read to last message
        $this->setReadMessage($room->id,Auth::user()->id);
      }

    }

    $userModel = Service::loadModel('User');

    // Get Other users in room
    $_users = Service::loadModel('UserInChatRoom')
    ->where([
      ['chat_room_id','=',$room->id],
      ['user_id','!=',Auth::user()->id]
    ])->get();

    $users = array();
    foreach ($_users as $user) {
      $users[] = $userModel->buildProfile($user->user_id);
    }

    $data = $item->buildForChatRoom();

    $this->setData('chat',json_encode(array(
      'user' => Auth::user()->id,
      'room' => $room->id,
      'key' => $room->room_key,
      'page' => 1,
      'time' => date('Y-m-d H:i:s')
    )));
    $this->setData('data',$data);
    $this->setData('users',$users);
    $this->setData('model','Item');
    $this->setData('label','รายการขาย');

    // SET META
    $this->setMeta('title','แชท » '.$data['title'].' — websiteName');

    return $this->view('pages.user.chat');
  }

  public function shopChatBySlug($slug) {
    return $this->shopChat(Service::loadModel('Shop')->select('id','slug','name','created_by')->where('slug','=',$slug)->first());
  }

  public function shopChatById($shopId) {
    return $this->shopChat(Service::loadModel('Shop')->select('id','slug','name','created_by')->find($shopId));
  }

  private function shopChat($shop,$roomId = null) {

    if(empty($shop)) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    if($shop->deleted == 1) {
      return $this->error('ไม่พบร้านขายสินค้า');
    }

    // if(empty($roomId) && ($shop->created_by == Auth::user()->id)) {
    //   return $this->error('ไม่สามารถแชทได้');
    // }

    $room = Service::loadModel('ChatRoom')
    ->select('chat_rooms.id','chat_rooms.room_key')
    ->join('user_in_chat_room', 'user_in_chat_room.chat_room_id', '=', 'chat_rooms.id');

    if(!empty($roomId)) {

      $room = $room->where([
        ['chat_rooms.id','=',$roomId],
        ['user_in_chat_room.user_id','=',Auth::user()->id]
      ])
      ->first();

      if(empty($room)) {
        return $this->error('ไม่สามารถแชทได้');
      }

      $this->setReadMessage($room->id,Auth::user()->id);

    }else {

      if($shop->created_by == Auth::user()->id) {
        return $this->error('ไม่สามารถแชทได้');
      }

      $room = $room->where([
        ['chat_rooms.model','=','Shop'],
        ['chat_rooms.model_id','=',$shop->id],
        ['user_in_chat_room.user_id','=',Auth::user()->id]
      ])
      ->first();

      if(empty($room)) {
         // create new one
        $room = $this->createRoom('Shop',$shop->id,array($shop->created_by,Auth::user()->id));
      }else {
        // update read to last message
        $this->setReadMessage($room->id,Auth::user()->id);
      }

    }

    $userModel = Service::loadModel('User');

    // Get Other users in room
    $_users = Service::loadModel('UserInChatRoom')
    ->where([
      ['chat_room_id','=',$room->id],
      ['user_id','!=',Auth::user()->id]
    ])->get();

    $users = array();
    foreach ($_users as $user) {
      $users[] = $userModel->buildProfile($user->user_id);
    }

    $data = $shop->buildForChatRoom();

    $this->setData('chat',json_encode(array(
      'user' => Auth::user()->id,
      'room' => $room->id,
      'key' => $room->room_key,
      'page' => 1,
      'time' => date('Y-m-d H:i:s')
    )));
    $this->setData('data',$data);
    $this->setData('users',$users);
    $this->setData('model','Shop');
    $this->setData('label','ร้านขายสินค้า');

    // SET META
    $this->setMeta('title','แชท » '.$data['title'].' — websiteName');

    return $this->view('pages.user.chat');
  }

  private function createRoom($model,$modelId,$users) {
    
    // create new room
    $room = Service::loadModel('ChatRoom');
    $room->model = $model;
    $room->model_id = $modelId;
    $room->room_key = md5($model.$modelId).Token::generate(128);
    $room->save();

    $now = date('Y-m-d H:i:s');

    foreach ($users as $user) {
      Service::loadModel('UserInChatRoom')
      ->fill([
        'chat_room_id' => $room->id,
        'user_id' => $user,
        'message_read_date' => $now
      ])->save();
    }

    return $room;
  }

  private function setReadMessage($roomId,$userId) {
    
    $message = Service::loadModel('ChatMessage')
    ->select('created_at')
    ->where('chat_room_id','=',$roomId)
    ->orderBy('created_at','desc')
    ->take(1)
    ->first();

    if(empty($message)) {
      return false;
    }

    $user = Service::loadModel('UserInChatRoom')
    ->where([
      ['chat_room_id','=',$roomId],
      ['user_id','=',$userId],
    ])
    ->update([
      'message_read_date' => $message->created_at->format('Y-m-d H:i:s'),
      'notify' => 0
    ]);

  }

  private function getLabel($modelName) {

    switch ($modelName) {
      case 'Item':
        return 'รายการขาย';
        break;
      
      case 'Shop':
        return 'ร้านขายสินค้า';
        break;

    }

    return '';
  }

  // ============================ API

  public function _chatRoom(Request $request) {

    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
      return false;
    }

    if(Auth::guest()) {
      return json_encode(array(
        'success' => false,
        'errorMessage' => 'คุณยังไม่ได้เข้าสู่ระบบ'
      ));
    }

    $result = [];

    $parts = explode('|', $request->get('data'));

    switch ($parts[0]) {
      case 'm': // model
        $result = $this->_chatRoomByModel($parts[1],$parts[2]);
        break;

      case 'r': // room
        $result = $this->_chatRoomById($parts[1]);
        break;
      
      default:
        $result = array(
          'success' => false,
          'errorMessage' => 'ไม่สามารถแชทได้' // General error
        );
        break;
    }

    return response()->json($result);
  }

  private function _chatRoomById($roomId) {

    $room = Service::loadModel('ChatRoom')->select('model','model_id','active')->find($roomId);

    if(empty($room)) {
      return array(
        'success' => false,
        'errorMessage' => 'ไม่สามารถแชทได้' // General error
      );
    }

    if(!$room->active) {
      return array(
        'success' => false,
        'errorMessage' => 'ไม่สามารถแชทได้' // General error
      );
    }

    $modelData = $this->_getChatRoomModelData($room->model,$room->model_id);

    $error = $this->_chatRoomModelChecking($modelData,false);

    if(!empty($error)) {
      return $error;
    }

    $room = Service::loadModel('ChatRoom')
    ->select('chat_rooms.room_key','chat_rooms.active')
    ->join('user_in_chat_room', 'user_in_chat_room.chat_room_id', '=', 'chat_rooms.id')
    ->where([
      ['chat_rooms.id','=',$roomId],
      ['user_in_chat_room.user_id','=',Auth::user()->id]
    ])
    ->first();

    if(!empty($room) && $room->active) {
      // update read to last message
      $this->setReadMessage($roomId,Auth::user()->id);
    }else {
      return array(
        'success' => false,
        'errorMessage' => 'ไม่สามารถแชทได้' // General error
      );
    }

    return $this->buildData($roomId,$room->room_key,Auth::user()->id,$this->_getChatRoomTitle($modelData));

    // return $this->_chatRoomByModel($room->model,$room->model_id,false);
  }

  private function _chatRoomByModel($model,$modelId,$checkOwner = true) {

    $modelData = $this->_getChatRoomModelData($model,$modelId);

    $error = $this->_chatRoomModelChecking($modelData,$checkOwner);

    if(!empty($error)) {
      return $error;
    }

    $room = Service::loadModel('ChatRoom')
    ->select('chat_rooms.id','chat_rooms.room_key','chat_rooms.active')
    ->join('user_in_chat_room', 'user_in_chat_room.chat_room_id', '=', 'chat_rooms.id')
    ->where([
      ['chat_rooms.model','=',$model],
      ['chat_rooms.model_id','=',$modelId],
      ['user_in_chat_room.user_id','=',Auth::user()->id]
    ])
    ->first();

    if(empty($room)) {
       // create new one
      $room = $this->createRoom($model,$modelId,array($modelData->created_by,Auth::user()->id));
    }elseif(!$room->active) {
      return array(
        'success' => false,
        'errorMessage' => 'ไม่สามารถแชทได้' // General error
      );
    }else {
      // update read to last message
      $this->setReadMessage($room->id,Auth::user()->id);
    }

    // // Set to Redis
    // Redis::set('chat-room:'.$room->id.':'.$room->room_key.':'.Auth::user()->id,1);

    // return array(
    //   'success' => true,
    //   'room' => $room->id,
    //   'key' => $room->room_key,
    //   // 'page' => 1,
    //   'time' => date('Y-m-d H:i:s'),
    //   'user' => Auth::user()->id,
    //   'title' => $this->_getChatRoomTitle($modelData)
    // );

    return $this->buildData($room->id,$room->room_key,Auth::user()->id,$this->_getChatRoomTitle($modelData));
  }

  private function buildData($id,$key,$userId,$title) {
    // Set to Redis
    Redis::set('chat-room:'.$id.':'.$key.':'.$userId,1);
    Redis::expire('chat-room:'.$id.':'.$key.':'.$userId, 2400); // 40 mins

    return array(
      'success' => true,
      'room' => $id,
      'key' => $key,
      // 'page' => 1,
      'time' => date('Y-m-d H:i:s'),
      'user' => $userId,
      'title' => $title
    );
  }

  private function _getChatRoomModelData($model,$modelId) {

    switch ($model) {
      case 'Item':
        return Service::loadModel('Item')->select('id','title','cancel_option','approved','deleted','expiration_date','created_by')->find($modelId);
        break;
      
      case 'Shop':
        return Service::loadModel('Shop')->select('id','slug','name','created_by')->find($modelId);
        break;
    }

    return null;
  }

  private function _getChatRoomTitle($modelData) {

    switch ($modelData->modelName) {
      case 'Item':
        return $modelData->title;
        break;
      
      case 'Shop':
        return $modelData->name;
        break;

    }

    return '';
  }

  private function _chatRoomModelChecking($modelData,$checkOwner = true) {

    if(empty($modelData)) {
      return array(
        'success' => false,
        'errorMessage' => 'ไม่สามารถแชทได้' // General error
      );
    }

    switch ($modelData->modelName) {
      case 'Item':

        // $data = Service::loadModel('Item')->select('id','title','cancel_option','approved','deleted','expiration_date','created_by')->find($modelId);

        if($checkOwner && ($modelData->created_by == Auth::user()->id)) {
          return array(
            'success' => false,
            'errorMessage' => 'คุณคือผู้ขายสินค้านี้ ไม่สามารถแชทได้' // General error
          );
        }

        if($modelData->deleted == 1) {
          return array(
            'success' => false,
            'errorMessage' => 'ไม่สามารถแชทได้ รายการขายถูกลบแล้ว' // General error
          );
        }

        if($modelData->cancel_option > 0) {
          return array(
            'success' => false,
            'errorMessage' => 'ไม่สามารถแชทได้ รายการขายถูกยกเลิกแล้ว' // General error
          );
        }

        // if($modelData->expiration_date < date('Y-m-d H:i:s')) {
        //   return array(
        //     'success' => false,
        //     'errorMessage' => 'ไม่สามารถแชทได้' // General error
        //   );
        // }

        if($modelData->approved == 0) {
          return array(
            'success' => false,
            'errorMessage' => 'ไม่สามารถแชทได้ ประการศอยู่ระหว่างการตรวจสอบ' // General error
          );
        }

        break;
      
      case 'Shop':
        
        // $data = Service::loadModel('Shop')->select('id','slug','name','created_by')->find($modelId);

        if($checkOwner && ($modelData->created_by == Auth::user()->id)) {
          return array(
            'success' => false,
            'errorMessage' => 'คุณเป็นเจ้าของร้านนี้ ไม่สามารถแชทได้' // General error
          );
        }

        if($modelData->deleted == 1) {
          return array(
            'success' => false,
            'errorMessage' => 'ไม่สามารถแชทได้ ร้านขายสินค้าถูกลบแล้ว' // General error
          );
        }

        break;

      default:
        return array(
          'success' => false,
          'errorMessage' => 'ไม่สามารถแชทได้' // General error
        );
        break;
    }

    return null;
  }
}
