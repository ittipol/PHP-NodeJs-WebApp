<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Redis;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\EmailValidationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\library\service;
use App\library\token;
use App\library\snackbar;
use App\Mail\AccountVarify;
use App\Mail\AccountRecovery;
use Auth;
use Hash;
use Redirect;
use Mail;

class UserController extends Controller
{
  public function login() {
    $this->setMeta('title','ล็อคอินเข้าใช้งาน | Ticket Easys');
    return $this->view('pages.user.login');
  }

  public function authenticate() {

    if(Auth::attempt([
      'email' => request()->input('email'),
      'password' => request()->input('password'),
      'email_verified' => 1
    ],!empty(request()->input('remember')) ? true : false)){

      // $user = User::find(Auth::user()->id);
      // $user->user_key = Token::generate(64);
      // $user->save();

      // update item qty to cart
      $cartModel = Service::loadModel('Cart');
      $itemModel = Service::loadModel('Item');
      $items = session()->get('cart');
      session()->forget('cart');

      if(!empty($items)) {

        foreach ($items as $item) {

          $_item = $itemModel->select('id')->where([
            ['id','=',$item['itemId']],
            ['deleted','=',0]
          ])->exists();

          if(!$_item) {
            continue;
          }

          $cart = Service::loadModel('Cart')->where([
            ['item_id','=',$item['itemId']],
            ['user_id','=',Auth::user()->id],
          ])
          ->select('item_id');

          if($cart->exists()) {
            $cart->first()->increment('quantity', $item['quantity']);
          }else {
            $cartModel->addItem($item['itemId'],$item['quantity']);
          }

        }

      }

      // User log
      Service::addUserLog('User',Auth::user()->id,'login');

      Snackbar::message($this->welcomeMessage());
      return Redirect::intended('/');
    }

    $message = 'อีเมล หรือ รหัสผ่านไม่ถูก';

    if(empty(request()->input('email')) && empty(request()->input('password'))) {
      $message = 'กรุณาป้อนอีเมล และ รหัสผ่าน';
    }

    // Snackbar::message($message);

    return Redirect::to('/login')->withErrors([$message]);
  }

  public function register() {
    $this->setMeta('title','สมัครใช้งาน | Ticket Easys');
    return $this->view('pages.user.register');
  }

  public function registering(RegisterRequest $request) {

    // 2 pow rounds
    // $hashed = Hash::make($request->password, ['rounds' => 12]);
  
    $user = new User;
    $user->email = trim($request->email);
    $user->password = Hash::make($request->password, ['rounds' => 12]);
    $user->name = trim($request->name);
    $user->user_key = Token::generate(64);
    $user->email_verification_token = md5($request->email.Token::generateSecureKey(2)).Token::generateSecureKey(32);
    $user->email_verified = 1;

    if(!$user->save()) {
      $message = 'ไม่สามารถสร้างบัญชีได้';
      return Redirect::back()->withErrors([$message]);
    }

    // Send varify email
    // $template = new AccountVarify;
    // $template->key = $user->email_verification_token;
    // Mail::to($user->email)->send($template);

    // Snackbar::message('บัญชีของคุณถูกสร้างแล้ว คุณสามารถใช้บัญชีนี้เพื่อเข้าสู่ระบบ');
    
    // session()->flash('register-success',true);
    // return Redirect::to('subscribe/success');

    Snackbar::modal('การสมัครใช้งานเสร็จสิ้น','บัญชีของคุณพร้อมใช้งานแล้ว','popup-success');
    return Redirect::to('/login');    
  }

  public function socialCallback() {

    if(!request()->has('code')) {
      abort(405);
    }

    $response = Service::facebookGetUserProfile(request()->code);

    if($response === false) {
      abort(405);
    }

    $_user = $response->getGraphUser();

    $email = null;
    if(!empty($_user['email'])) {
      $email = trim($_user['email']);
    }

    $user = User::where([
      ['social_provider_id','=',1],
      ['social_user_id','=',$_user['id']]
    ])->first();

    if(empty($user)) {

      if(!empty($email)) {
        $user = User::where('email','=',$email)->first();
      }

      if(empty($user)) {
        // Create new user
        $user = new User;
        $user->social_provider_id = 1; // FB
        $user->social_user_id = $_user['id'];

        // if(!empty($_user['email'])) {
        //   $user->email = trim($_user['email']);
        // }

        $user->email = $email;
        $user->name = $_user['name'];
        $user->user_key = Token::generate(64);
        $user->save();
      }else {

        // Check if this email was create by social network already
        if(($user->social_provider_id != null) || ($user->social_user_id != null)) {
          Snackbar::message('ไม่สามารถเข้าสู่ระบบด้วย Social Network');
          return redirect('login');
        }

        $user->update(array(
          'social_provider_id' => 1,
          'social_user_id' => $_user['id']
        ));

      }

    }elseif(empty($user->email) && !empty($email)) {
      // update email
      // Check if email not exist
      // then update email

      $exist = User::select('id')->where('email','=',$email)->exists();

      if(!$exists) {
        $user->update(array(
          'email' => $email
        ));
      }

    }

    Auth::login($user,true);

    // User log
    Service::addUserLog('User',Auth::user()->id,'Login (Facebook)');

    Snackbar::message($this->welcomeMessage());

    return Redirect::intended('/');
  }

  public function registeringSuccess() {
    if(!session()->has('register-success')) {
      return Redirect::to('login');
    }

    return $this->view('pages.user.registering-success');
  }

  public function verify() {

    if(!request()->has('key')) {
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
      return redirect('login');
    }

    $key = request()->key;

    $user = User::where('email_verification_token','like',$key);

    if(!$user->exists()) {
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
      return redirect('login');
    }

    $user = $user->select('id','email_verification_token','email_verified')->first();

    if($user->email_verified) {
      Snackbar::message('บัญชีของคุณถูกยืนยันแล้ว');
    }elseif($key === $user->email_verification_token) {
      $user->email_verification_token = null;
      $user->email_verified = 1;
      $user->save();

      // Snackbar::message('การยืนยันบัญชีของคุณเรียบร้อยแล้ว บัญชีของคุณสามารถใช้งานได้แล้ว');
      return $this->view('pages.user.verification-success');
    }else {
      Snackbar::message('ไม่สามารถยืนยันบัญชีของคุณได้');
    }

    return redirect('login');
  }

  public function forgottenPassword() {
    if(session()->has('identification-sent')) {
      return $this->view('pages.user.identification-sent');
    }

    return $this->view('pages.user.identification');
  }

  public function forgottenPasswordSubmit(EmailValidationRequest $request) {

    $userModel = new User;

    $email = request()->get('email');

    $user = $userModel->where('email','like',$email);

    if($user->exists()) {

      // check dup key
      // do {
      //   $key = Token::generateSecureKey(32);
      // } while ($userModel->where('identification_token','like',$key)->exists());

      $key = Token::generateSecureKey(64);

      // save token and expire
      $user = $user->select('id')->first();
      $user->identification_token = md5($email.Token::generateSecureKey(2)).$key;
      $user->identification_expire = date('Y-m-d H:i:s',time() + 2400);

      if($user->save()) {
        $template = new AccountRecovery;
        // $template->email = md5($email);
        $template->key = $user->identification_token;
        Mail::to($email)->send($template);
      }

    }

    session()->flash('identification-sent',true);
    return Redirect::to('account/identify');
  }

  public function recover() {

    if(!request()->has('key')) {
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
      return redirect('login');
    }

    $key = request()->key;

    $user = User::select('id')
    ->where([
      ['identification_token','like',$key],
      ['identification_expire','>',date('Y-m-d H:i:s')]
    ]);

    if(!$user->exists()) {
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
      return redirect('login');
    }

    $this->setData('key',$key);
    // $this->setData('hasPassword',!empty($user->first()->password));

    return $this->view('pages.user.recover');
  }

  public function recoverSubmit(ResetPasswordRequest $request) {

    if(!$request->has('key')) {
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
      return redirect('login');
    }

    $user = User::select('id')
    ->where([
      ['identification_token','like',request()->key],
      ['identification_expire','>',date('Y-m-d H:i:s')]
    ]);

    if($user->exists()) {

      $user = $user->select('id')->first();
      $user->password = Hash::make($request->password, ['rounds' => 12]);
      $user->identification_token = null;
      $user->identification_expire = null;
      $user->email_verified = 1;
      $user->save();

      Snackbar::message('รหัสผ่านใหม่ของคุณถูกบันทึกแล้ว คุณสามารถเข้าสู่ระบบด้วยรหัสผ่านใหม่ได้แล้ว');
    }else{
      Snackbar::message('ไม่พบคำขอหรือคำขออาจหมดอายุแล้ว');
    }
    
    return redirect('login');
  }

  private function welcomeMessage() {

    // if(!empty(Auth::user()->last_active)) {

    //   $time = strtotime(date('Y-m-d H:i:s')) - strtotime(Auth::user()->last_active);

    //   // if($time < (86400 * 5)) {
    //   //   return 'ยินดีต้อนรับ คุณได้เข้าสู่ระบบแล้ว';
    //   // }elseif($time < (86400 * 10)) {
    //   //   return 'ไม่ได้เจอนานเลย!!! ยินดีต้อนรับ';
    //   // }elseif($time < (86400 * 25)) {
    //   //   return 'นานมากแล้วที่คุณไม่ได้เข้ามาใช้งาน ยินดีต้อนรับ';
    //   // }else {
    //   //   return 'นึกว่าลืมกันซะแล้ว ยินดีต้อนรับ';
    //   // }

    //   if(!empty(Auth::user()->last_active)) {
    //     return 'ยินดีต้อนรับ คุณได้เข้าสู่ระบบแล้ว';
    //   }

    // }

    if(empty(Auth::user()->last_active)) {
      session()->flash('popup-feature',true);
    }

    return 'คุณได้เข้าสู่ระบบแล้ว';
  }

  public function logout() {

    if(Auth::check()) {
      $uid = Auth::user()->id;

      // Update last_active
      Auth::user()->last_active = date('Y-m-d H:i:s');
      Auth::user()->save();

      Auth::logout();
      session()->flush();

      Redis::del('online-user:'.$uid);

      Service::addUserLog('User',$uid,'logout',$uid);
    }

    return redirect('/');
  }

}