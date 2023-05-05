@extends('shared.main-secondary')
@section('content')

<!-- <div class="bg-drop bg-drop-full bg-drop-light-gray dn db-ns"></div> -->

{{Form::open(['id' => 'login_form', 'class' => 'user-form mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

<div class="login-page mt3 mt5-ns">

  <a class="tc mb3 dn db-ns" href="/"><img class="logo" src="/assets/images/logo/logo_x.jpg"></a>

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
      <i class="fab fa-facebook-f"></i>&nbsp;เข้าสู่ระบบด้วย Facebook         
    </a>  
  </div>

  <div class="text-center margin-top-60">
    ยังไม่มีบัญชี? <a href="{{URL::to('subscribe')}}">สมัครใช้งาน</a>
  </div>

</div>

{{Form::close()}}


<script type="text/javascript" src="/assets/js/form/validation/login-validation.js"></script>

<script type="text/javascript">

  $(document).ready(function(){
    LoginValidation.initValidation();

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

@stop