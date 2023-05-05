@if(Auth::check())

<div id="modal_user_menu" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner c-modal-dark-theme">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="row">
      <div class="col-md-6">
        <div class="avatar avatar-md tc mb3">
          <a href="/account/edit"><img src="/avatar?o=1&d=1"></a>
        </div>
        <div class="tc mt4">
          <h5 class="text-overflow-ellipsis white">{{Auth::user()->name}} <a href="/account/edit"><i class="fas fa-pen-square"></i></a></h5>
        </div>
        <hr>
        @if(config('app.module_enabled.shop'))
        <div class="tc">
          @if($_has_shop)
          <a href="/shop/page/{{$_shop['slug']}}" class="btn btn-primary">
            <i class="fas fa-hotel"></i> ร้านขายสินค้าของฉัน
          </a>
          @else
          <a href="/shop/create" class="btn btn-primary">
            <i class="fas fa-hotel"></i> สร้างร้านขายสินค้า
          </a>
          @endif
        </div>
        <hr>
        @endif
      </div>
      <div class="col-md-6">
        <ul class="list-group addition-menu">
          <li class="list-group-item">
            <a href="/ticket/new"><i class="fas fa-plus"></i> ขายตั๋ว วอชเชอร์ หรืออื่นๆ</a>
          </li>
          <li class="list-group-item">
            <a href="/account/sale"><i class="fas fa-tags"></i> รายการขายของฉัน</a>
          </li>
          <!-- <li class="list-group-item">
            <a href="/account/coin"><i class="fas fa-coins"></i> เหรียญ</a>
          </li> -->
          <li class="list-group-item">
            <a href="/logout"><i class="fas fa-sign-out-alt"></i> ออกจากระบบ</a>
          </li>
        </ul>
      </div>
    </div>

  </div>
</div>

@else

<div id="modal_user_menu" class="c-modal modal-login-form">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    {{Form::open(['action' => 'Web\UserController@authenticate', 'id' => 'login_form', 'class' => 'user-form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

    <div class="login-page">

      <a class="db tc mb3" href="/"><img class="logo" src="/assets/images/logo/logo_x.jpg"></a>

      @include('shared.form_error')
      
      <h5 class="text-center">ล็อคอินเข้าใช้งาน Ticket Easys</h5>   

      <div class="margin-top-30">
        <div class="form-group">
          <input type="text" name="email" class="form-control" placeholder="อีเมล">
        </div>

        <div class="form-group">
          <input type="password" name="password" class="form-control" placeholder="รหัสผ่าน">
        </div>

        <label class="margin-bottom-5">
          <div>
            <label class="control control--checkbox mb-2">
              จดจำการเข้าสู่ระบบ
              <input type="checkbox" name="remember_me" >
              <div class="control__indicator"></div>
            </label>
          </div>
        </label>

        {{Form::submit('เข้าสู่ระบบ', array('class' => 'btn c-btn c-btn-primary btn-block'))}}

        <!-- <div class="text-center margin-top-40">
          ลืมรหัสผ่าน? <a href="{{URL::to('account/identify')}}">ส่งคำขอรหัสผ่านใหม่</a>
        </div> -->

      </div>

      <hr class="margin-top-40 margin-bottom-30">

      <h5 class="text-center">ล็อคอินด้วย Social Network</h5>     

      <div class="social-login margin-top-20">     
        <a href="javascript:void(0);" id="fb_login_btn" class="btn rounded btn-block btn-facebook margin-bottom-10">           
          <i class="fab fa-facebook-f"></i> เข้าสู่ระบบด้วย Facebook         
        </a>  
      </div>

      <div class="text-center margin-top-60">
        ยังไม่มีบัญชี? <a href="#" data-toggle="modal" data-c-modal-target="#model_register_form">สมัครใช้งาน</a>
      </div>

    </div>

    {{Form::close()}}

  </div>
</div>

<div id="model_register_form" class="c-modal modal-register-form">
  <a class="close"></a>
  <div class="c-modal-inner fullscreen">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="container mt5 mb3 mb5-ns">
      <h5 class="text-center">สมัครใช้งานฟรีเพื่อใช้ Ticket Easys อย่างเต็มรูปแบบ!</h5>
      <p class="text-center">ด้วยการสมัครใช้งานคุณสามารถเพื่มรายการขายขายและแชร์รายการที่คุณสนใจกับเพื่อนหรือครอบครัวของคุณได้ทันที</p> 
    </div>

    {{Form::open(['action' => 'Web\UserController@registering', 'id' => 'register_form', 'class' => 'user-form mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

    <div class="register-page">

      <a class="db tc mb3" href="/"><img class="logo" src="/assets/images/logo/logo_x.jpg"></a>

      @include('shared.form_error')

      <h5 class="text-center">สมัครใช้งานกับ Ticket Easys</h5>

      <div class="margin-top-30">

        <div class="form-group">
          {{ Form::text('name', null, array(
            'class' => 'form-control rounded-right',
            'placeholder' => 'ชื่อ นามสกุล',
            'autocomplete' => 'off'
          )) }}
        </div>

        <div class="form-group">
          <input type="text" name="email" class="form-control rounded-right" placeholder="อีเมล" autocomplete="off">
        </div>

        <div class="form-group">
          <input type="password" name="password" id="password_field" class="form-control rounded-right" placeholder="รหัสผ่าน (อย่างน้อย 4 อักขระ)">
        </div>

        <div class="form-group">      
          <input type="password" name="password_confirmation" class="form-control rounded-right" placeholder="ป้อนรหัสผ่านอีกครั้ง">
        </div>

        {{Form::submit('สร้างบัญชี', array('class' => 'btn c-btn c-btn-primary btn-block'))}}

      </div>

      <hr class="margin-top-40 margin-bottom-30">

      <h5 class="text-center">ล็อคอินด้วย Social Network</h5>     

      <div class="social-login margin-top-20">     
        <a href="javascript:void(0);" id="fb_login_btn" class="btn rounded btn-block btn-facebook margin-bottom-10">           
          <i class="fab fa-facebook-f"></i> เข้าสู่ระบบด้วย Facebook    
        </a>  
      </div>

      <div class="text-center margin-top-60">
        มีบัญชีอยู่แล้ว? <a href="javascript:void(0);" data-close="modal">เข้าสู่ระบบ</a>
      </div>

      <div class="clearfix mb3"></div>

    </div>

    {{Form::close()}}

  </div>
</div>


<script type="text/javascript" src="/assets/js/form/validation/login-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/validation/register-validation.js"></script>

<script type="text/javascript">

  $(document).ready(function(){
    LoginValidation.initValidation();
    RegisterValidation.initValidation();
    
    $('#fb_login_btn').on('click',function(e){
      FB.login(function(response) {
        if (response.authResponse) {
          Loading.show();
          window.location.href = "/facebook/login?code="+response.authResponse.accessToken;
        }
      }, {scope: 'email,public_profile'});
    });

  });
</script>

@endif