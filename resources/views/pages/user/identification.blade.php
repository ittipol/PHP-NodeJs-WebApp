@extends('shared.main-secondary')
@section('content')

<div class="container mv5 mv7-ns center w-100 w-50-ns">

  <h4>ลืมรหัสผ่าน</h4>
  <p>โปรดป้อนอีเมลของคุณเพื่อร้องขอการตั้งรหัสผ่านใหม่</p>

  {{Form::open(['id' => 'identify_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

  @include('shared.form_error')

    <div class="form-group">
      {{ Form::text('email', null, array(
        'id' => 'email',
        'class' => 'form-control',
        'autocomplete' => 'off',
        'placeholder' => 'อีเมล'
      )) }}
    </div>

    <div class="margin-top-30">
      <div class="w-100 w-70-ns center clearfix">
        {{Form::submit('ส่งคำร้องไปยังอีเมล', array('class' => 'btn btn-primary br0 fl w-50'))}}
        <a class="btn br0 fl w-50" href="{{URL::to('login')}}">ยกเลิก</a>
      </div>
    </div>

  {{Form::close()}}

</div>


<script type="text/javascript" src="/assets/js/form/validation/identify-validation.js"></script>

<script type="text/javascript">
  $(document).ready(function(){
    Validation.initValidation();
  });
</script>

@stop