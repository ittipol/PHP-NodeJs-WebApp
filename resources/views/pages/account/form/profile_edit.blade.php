@extends('shared.account-with-menu')
@section('account-content')

<div class="c-form-container mt4">
  <h4 class="mb5">
    <a href="/me" class="c-btn c-btn mr3"><i class="fa fa-chevron-left" aria-hidden="true"></i></a> แก้ไขช้อมูลของฉัน
  </h4>
</div>

{{Form::model($data, ['id' => 'profile_edit_form', 'method' => 'PATCH', 'enctype' => 'multipart/form-data'])}}

  <div class="c-form-container">
    @include('shared.form_error')
  </div>

  <div class="bb b--moon-gray mb4">
    <div class="c-form-container">
      <h5 class="mb3">ข้อมูลทั่วไป</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">

        <div class="form-group">
          <label class="form-control-label">รูปภาพโปรไฟล์</label>

          <div id="avatar" class="avatar-upload center">

            @if(empty($profileImage))
            <div class="avatar-delete-btn pointer">
            @else
            <div class="avatar-delete-btn pointer db" data-filename="1">
            @endif
              <span aria-hidden="true">&times;</span>
            </div>

            <label class="upload-avatar-btn pointer">
              <input type="file" class="dn">
              <div class="pv2 ph4"><i class="far fa-image f4 f3-ns"></i></div>
            </label>
            
            <img @if(!empty($profileImage)) class="show" src="{{$profileImage}}" @endif>
          </div>
        </div>

        <div class="form-group">
          <label class="form-control-label required">ชื่อ นามสกุล</label>
          {{ Form::text('name', null, array(
            'class' => 'form-control',
            'autocomplete' => 'off'
          )) }}
        </div>

      </div>
    </div>
  </div>

  <div class="bb b--moon-gray mb3">
    <div class="c-form-container">
      <h5 class="mb3">ธีม</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12 clearfix">
        @foreach($themes as $key => $theme)
        <label class="theme-color {{$theme['name']}} fl">
          {{Form::radio('theme_color_id', $key)}}
          <div class="theme-color-tile"></div>
        </label>
        @endforeach
      </div>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        {{Form::submit('บันทึก', array('class' => 'btn btn-primary btn-block'))}}
      </div>
    </div>
  </div>

{{Form::close()}}

<div class="clearfix margin-top-200"></div>

<div id="model_upload_avatar" class="c-modal">
  <div class="c-modal-inner w-100 h-100">
    <h4 class="item-title f4 f3-ns mb3 mb4-ns">รูปภาพโปรไฟล์</h4>
    <div class="avatar-upload-panel">
      <div class="avatar-upload-view">
        <img class="avatar-upload-edit">
      </div>

      <img class="avatar-upload-edit o-30">

      <div class="f4 tc mt3">ลากเพื่อปรับตำแหน่ง</div>

      <div class="c-card__actions clearfix tc mt3 pa0">
        <a id="avatar_save_btn" class="c-btn c-btn c-btn__secondary fl w-50 ma0 br0 db" href="javascript:void(0);">บันทึก</a>
        <a id="avatar_cancel_btn" class="c-btn fl  w-50 ma0 br0 db" href="javascript:void(0);">ยกเลิก</a>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript" src="/assets/js/form/validation/profile-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/upload_avatar.js"></script>

<script type="text/javascript">
  
  $(document).ready(function(){

    const avatar = new UploadAvatar();
    avatar.init();

    Validation.initValidation();
  });

</script>

@stop