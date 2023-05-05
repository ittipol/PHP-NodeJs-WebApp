@extends('shared.main-secondary')
@section('content')

<!-- <div class="bg-drop bg-drop-full bg-drop-light-gray dn db-ns"></div> -->

<div class="container mt3 mt5-ns mb3 mb5-ns">
  <h5 class="text-center">สมัครใช้งานฟรีเพื่อใช้ Ticket Easys อย่างเต็มรูปแบบ!</h5>
  <p class="text-center">ด้วยการสมัครใช้งานคุณสามารถเพื่มรายการขายขายและแชร์รายการที่คุณสนใจกับเพื่อนหรือครอบครัวของคุณได้ทันที</p> 
</div>

{{Form::open(['id' => 'register_form', 'class' => 'user-form mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

<div class="register-page">

  <a class="tc mb3 dn db-ns" href="/"><img class="logo" src="/assets/images/logo/logo_x.jpg"></a>

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
      <i class="fab fa-facebook-f"></i>&nbsp;เข้าสู่ระบบด้วย Facebook    
    </a>  
  </div>

  <div class="text-center margin-top-60">
    มีบัญชีอยู่แล้ว? <a href="{{URL::to('login')}}">เข้าสู่ระบบ</a>
  </div>

</div>

{{Form::close()}}


<script type="text/javascript" src="/assets/js/form/validation/register-validation.js"></script>

<script type="text/javascript">

  $(document).ready(function(){
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

@stop