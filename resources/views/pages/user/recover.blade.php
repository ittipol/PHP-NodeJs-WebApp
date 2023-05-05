@extends('shared.main-secondary')
@section('content')

<div class="container mv5 mv7-ns center w-100 w-50-ns">

  <h4>รีเซ็ตรหัสผ่าน</h4>
  <p>ป้อนรหัสผ่านใหม่ของคุณ</p>

  {{Form::open(['id' => 'recovery_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

  @include('shared.form_error')

    <div class="form-group">
      <input type="password" name="password" id="password_field" class="form-control" placeholder="รหัสผ่านใหม่ (อย่างน้อย 4 อักขระ)" autocomplete="off">
    </div>

    <div class="form-group">
      <input type="password" name="password_confirmation" class="form-control" placeholder="ป้อนรหัสผ่านใหม่อีกครั้ง" autocomplete="off">
    </div>

    {{ Form::hidden('key', $key) }}

    <div class="margin-top-30">
      <div class="w-100 w-70-ns center">
        {{Form::submit('ตกลง', array('class' => 'btn btn-primary btn-block'))}}
      </div>
    </div>

  {{Form::close()}}

</div>


<script type="text/javascript" src="/assets/js/form/validation/recover-password-validation.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    Validation.initValidation();
  });
</script>

@stop