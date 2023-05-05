@extends('shared.main')
@section('content')

<div class="c-jumbotron tc" style="background-image:url('{{ $__banner__sm__ }}');">
  <div class="jumbotron jumbotron-fluid mb0">
    <div class="container">
      <h1>แพ็กเกจ ที่พัก ท่องเที่ยว จากธุรกิจของคุณ หรือ บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่</h1>
    </div>
  </div>
</div>

<div class="c-form-container mt4">
  <div class="mb4 mb5-ns">
    <h4>ลงขายหรือโฆษณาสินค้าของคุณตอนนี้</h4>
    <p>กรอกข้อมูลรายการขายของคุณให้ได้มากที่สุดเพื่อให้สินค้าของคุณมีรายละเอียดมากพอเพื่อให้ลูกค้าเข้าใจในสินค้าของคุณก่อนการซื้อสินค้าจากคุณ</p>
  </div>
</div>

{{Form::open(['id' => 'item_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

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
          <label class="form-control-label required">ประเภทบัตร</label>
          <div class="row">
            @foreach($categories as $key => $category)
            <?php $checked = false; if($key == 0) {$checked = true;} ?>
            <div class="col-6 col-md-4">
              <div class="c-input">
                {{Form::radio('ItemToCategory[category_id]', $category->id, $checked, array('id' => 'cat'.$key))}}
                <label for="cat{{$key}}">
                  {{$category->name}}
                </label>
              </div>
            </div>
            @endforeach
          </div>
        </div>

        <div class="form-group">
          <label class="form-control-label required">หัวข้อ / ชื่อสินค้า</label>
          {{ Form::text('title', null, array(
            'class' => 'form-control',
            'autocomplete' => 'off'
          )) }}
        </div>

        <div class="form-group">
          <label class="form-control-label required">รายละเอียด</label>
          <div class="alert alert-secondary" role="alert">
            คุณสามารถติด Hashtag ให้กับรายการขายของคุณ เพื่อจัดกลุ่มสินค้าของคุณ <a href="#" data-toggle="modal" data-c-modal-target="#model_tip_input_description">แสดงเคล็ดลับเพิ่มเติม</a>
          </div>
          {{Form::textarea('description', null, array('id' => 'description', 'class' => 'form-control'))}}
        </div>

        <div class="form-group">
          <label class="form-control-label required">ราคาขาย</label>
          <div class="input-group">
            {{ Form::text('price', null, array(
              'id' => 'price_input',
              'class' => 'form-control',
              'autocomplete' => 'off',
              'aria-describedby' => 'price-addon'
            )) }}
            <span class="input-group-addon" id="price-addon">บาท</span>
          </div>
        </div>

        <div class="row">

          <div class="col-12">
            <div class="alert alert-secondary" role="alert">
              <strong>ส่วนลดสินค้า</strong> หากสินค้านี้มีส่วนลดและต้องการแสดงส่วนลดของสินค้า ให้ป้อนราคาเดิมของสิค้าและระบบจะคำนวณแสดง % ส่วนลดให้กับสินค้า เพื่อใช้ในการแสดงส่วนลดของสินค้า
            </div>
          </div>

          <div class="form-group col-md-6">
            <label class="form-control-label">ราคาเดิม</label>
            <div class="input-group">
              {{ Form::text('original_price', null, array(
                'id' => 'original_price_input',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'aria-describedby' => 'full-price-addon'
              )) }}
              <span class="input-group-addon" id="full-price-addon">บาท</span>
            </div>
          </div>

          <div class="form-group col-md-6">
            <label class="form-control-label">ส่วนลด</label>
            <div class="input-group">
              {{ Form::text('percent', 0, array(
                'id' => 'percent_input',
                'class' => 'form-control',
                'autocomplete' => 'off',
                'aria-describedby' => 'percent-addon',
                'disabled' >= true
              )) }}
              <span class="input-group-addon" id="percent-addon">%</span>
            </div>
            <small>* จะคำนวณหลังจากกรอกราคาเดิมของบัตร</small>
          </div>
        </div>

        <div class="form-group">
          <label class="form-control-label required">ตำแหน่งสินค้า</label>

          @if($hasShop)
            <div class="mv3">
              <label class="control control--checkbox mb-2">
                ใช้ตำแหน่งของร้านขายสินค้า
                {{Form::checkbox('use_shop_location', 1)}}
                <div class="control__indicator"></div>
              </label>
            </div>
          @endif

          <div id="location_selecting_box" class="selecting-lable-box">
            <div id="location_label" class="selected-value" data-toggle="modal" data-c-modal-target="#selecting_location" data-selecting-empty-label="ระบุตำแหน่งสินค้า">
              ระบุตำแหน่งสินค้า
            </div>

            <a id="filter_location_clear" class="selected-value-delete">
              <span aria-hidden="true">&times;</span>
            </a>
          </div>

          <div id="selecting_location" class="c-modal">
            <a class="close"></a>
            <div class="c-modal-sidebar-inner">

              <a class="modal-close">
                <span aria-hidden="true">&times;</span>
              </a>

              <div class="list-item-panel selecting-list"></div>
              <div class="selecting-action">
                <div class="selecting-action-inner mv2">
                  <small class="mb2">เส้นทาง</small>
                  <h5 class="selecting-lable mb2">...</h5>
                </div>
              </div>
            </div>
            <input type="hidden" name="ItemToLocation[location_id]">
          </div>
        
        </div>

        <div class="form-group">
          <label class="form-control-label required">การติดต่อ</label>
          <div class="alert alert-secondary" role="alert">
            หมายเลขโทรศัพท์, Facebook, Line ID, แชท หรืออื่นๆ <a href="#" data-toggle="modal" data-c-modal-target="#model_tip_input_contact">แสดงเคล็ดลับเพิ่มเติม</a>
          </div>
          @if($hasShop)
            <div class="mv3">
              <label class="control control--checkbox mb-2">
                ใช้การติดต่อของร้านขายสินค้า
                {{Form::checkbox('use_specific_contact', 1)}}
                <div class="control__indicator"></div>
              </label>
            </div>
          @endif
          {{Form::textarea('contact', null, array('id' => 'contact', 'class' => 'form-control'))}}
        </div>

      </div>
    </div>
  </div>

  <!-- <div class="bb b--moon-gray mb4">
    <div class="c-form-container">
      <h5 class="mb3">จำนวนสินค้า</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="form-group">
      <label class="form-control-label required">จำนวนสินค้า</label>
      {{ Form::text('quantity', 1, array(
        'class' => 'form-control',
        'autocomplete' => 'off'
      )) }}
    </div>
  </div> -->

  <div class="bb b--moon-gray mb4">
    <div class="c-form-container">
      <h5 class="mb3">วันเวลาและสถานที่ในการใช้งาน</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="form-group">
      <label class="form-control-label">ประเภทวันและเวลาที่สามารถใช้งาน</label>
      {{ Form::select('date_type', $dateType, null, array('id' => 'date_type_select', 'class' => 'form-control')) }}
    </div>

    <div id="date_group_1" class="row">
      <div id="date_1" class="form-group col-md-6">
        <label class="form-control-label">วันที่เริ่มใช้</label>
        <div class="input-group">
          <span class="input-group-addon" id="location-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {{Form::text('date_1', null, array('id' => 'date_input_1', 'class' => 'form-control' ,'autocomplete' => 'off', 'readonly' => 'true'))}}
          <a class="date-clear" data-date-clear="#date_1"><span aria-hidden="true">×</span></a>
        </div>
      </div>

      <div class="form-group col-md-6">
        <label class="form-control-label">เวลา</label>
        <div class="input-group">
          {{ Form::select('start_time_hour', $hour, 0, array('class' => 'form-control')) }}
          {{ Form::select('start_time_min', $min, 0, array('class' => 'form-control')) }}
        </div>
      </div>
    </div>

    <!-- <hr class="date-separate"> -->

    <div id="date_group_2" class="row">
      <div id="date_2" class="form-group col-md-6">
        <label class="form-control-label required">ใช้ได้ถึง</label>
        <div class="input-group">
          <span class="input-group-addon" id="location-addon">
            <i class="fa fa-calendar"></i>
          </span>
          {{Form::text('date_2', null, array('id' => 'date_input_2', 'class' => 'form-control' ,'autocomplete' => 'off', 'readonly' => 'true'))}}
          <a class="date-clear" data-date-clear="#date_2"><span aria-hidden="true">×</span></a>
        </div>
      </div>

      <div class="form-group col-md-6">
        <label class="form-control-label">เวลา</label>
        <div class="input-group">
          {{ Form::select('end_time_hour', $hour, 23, array('class' => 'form-control')) }}
          {{ Form::select('end_time_min', $min, 59, array('class' => 'form-control')) }}
        </div>
      </div>
    </div>

    <hr>

    <div class="form-group">
      <label class="form-control-label">สถานที่หรือตำแหน่งที่สามารถนำไปใช้ได้</label>
      <div class="input-group">
        <span class="input-group-addon" id="location-addon">
          <i class="fa fa-map-marker"></i>
        </span> 
        {{ Form::text('place_location', null, array(
          'class' => 'form-control',
          'autocomplete' => 'off',
          'aria-describedby' => 'location-addon'
        )) }}
      </div>
    </div>
  </div>

  <div class="bb b--moon-gray mb4">
    <div class="c-form-container">
      <h5 class="mb3">รูปภาพ</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        
        @if(true)
        <div class="mb5">
          
          <div class="mb2">รูปภาพหน้าปกรายการขาย</div>

          <div class="alert alert-secondary" role="alert">
            เลือกรูปภาพที่ต้องการให้เป็นรูปภาพหน้าปก รูปภาพนี้จะแสดงอยู่บนสุดของรายการขาย <a href="#" data-toggle="modal" data-c-modal-target="#model_item_banner">สิ่งนี้คือ?</a>
          </div>

          <div id="banner" class="banner">

            <label class="data-banner-upload-btn pointer">
              <input type="file" class="dn">
              <div class="pv2 ph4">เลือกรูปภาพ</div>
            </label>

            <div class="data-banner-delete-btn pointer">
              <span aria-hidden="true">&times;</span>
            </div>

            <div class="data-banner-wrapper">
              <img class="r-data-banner data-banner">
              <img class="c-data-banner data-banner">
            </div>

            <div class="data-banner-upload-message f4">ลากเพื่อปรับตำแหน่ง</div>

            <div class="banner-managing-btn c-card__actions clearfix tc pa0">
              <a class="banner-save-btn c-btn c-btn-bg c-btn__secondary fl ma0 br0 db" href="javascript:void(0);">บันทึก</a>
              <a class="banner-cancel-btn c-btn c-btn-bg fl ma0 br0 db" href="javascript:void(0);">ยกเลิก</a>
            </div>

          </div>
          
        </div>
        @endif

        @if(false)
        <div class="form-group mb5">
          <label class="form-control-label">รูปภาพ Preview สินค้า</label>

          <div class="alert alert-secondary" role="alert">
            เลือกรูปภาพที่ต้องการให้แสดงเป็นรูป Preview <a href="#" data-toggle="modal" data-c-modal-target="#model_item_preview">สิ่งนี้คือ?</a>
          </div>

          <div class="c-card c-card--to-edge center">
            <div class="c-card--inner overflow-visible">

              <div id="preview" class="preview">
                <label class="data-preview-upload-btn pointer">
                  <input type="file" class="dn">
                  <div class="pv2 ph4">เลือกรูปภาพ</div>
                </label>

                <div class="data-preview-delete-btn pointer">
                  <span aria-hidden="true">&times;</span>
                </div>

                <div class="data-preview-wrapper">
                  <img class="r-data-preview data-preview">
                  <img class="c-data-preview data-preview">
                </div>

                <div class="data-preview-upload-message f4">ลากเพื่อปรับตำแหน่ง</div>

                <div class="preview-managing-btn c-card__actions clearfix tc pa0">
                  <a class="preview-save-btn c-btn c-btn-bg c-btn__secondary fl ma0 br0 db" href="javascript:void(0);">บันทึก</a>
                  <a class="preview-cancel-btn c-btn c-btn-bg fl ma0 br0 db" href="javascript:void(0);">ยกเลิก</a>
                </div>

                <input type="hidden" name="Preview[filename]" value="">
              </div>

              <div class="c-card__primary-title">
                <h2 class="title">
                  <a>หัวข้อ / ชื่อสินค้า</a>
                  &nbsp;<small>—&nbsp;&nbsp;รายละเอียด</small>
                </h2>
              </div>
            </div>
          </div>
        </div>
        @endif

        <div class="form-group">
          <label class="form-control-label">รูปภาพสินค้า</label>
          <div class="alert alert-secondary ma0 br0">
            <ul class="ma0">
              <li>ขนาดไฟล์สูงสุดไม่เกิน 5 MB</li> 
              <li>อัพโหลดไฟล์ได้ในรูปแบบ JPG หรือ PNG</li>
            </ul>
          </div>

          <div class="image-total pv4 ph2">
            <div id="image_item_list">

            </div>
            <div class="pt2 mt1">
              <h5 id="image_total" class="f5 tc mb-3">ไม่มีรูปภาพ</h5>
            </div>
          </div>

          <a class="btn btn-secondary btn-block br0" href="#" data-toggle="modal" data-c-modal-target="#model_upload_item_image"><i class="far fa-image"></i> เพิ่ม/แก้ไขรูปภาพ</a>
          
          <div id="model_upload_item_image" class="c-modal">
            <div class="c-modal-inner w-100 h-100">
              <a class="btn btn-secondary modal-close f5">
                <span aria-hidden="true">ตกลง</span>
              </a>
              <h4 class="item-title f4 f3-ns mb3 mb4-ns">รูปภาพสินค้า</h4>

              <div>
                <div id="_image_group" class="upload-image"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        <div class="mb3">
          การคลิก "ลงขายสินค้า" แสดงว่าคุณยินยอมตาม<a href="#" data-toggle="modal" data-c-modal-target="#modal_publishing_term_and_condition">ข้อกำหนดและเงื่อนไข</a>แล้ว
        </div>
      </div>
    </div>
  </div>

  {{Form::submit('ลงขายสินค้า', array('class' => 'c-floating-btn form-floating-btn btn-hide-bottom btn-hide-when-input white'))}}

{{Form::close()}}

<div class="clearfix margin-top-200"></div>

@include('shared.modal.tip-input-description')
@include('shared.modal.tip-input-contact')
@include('shared.modal.item-banner')
<!--@include('shared.modal.item-preview') -->
@include('shared.modal.publishing-term-and-condition')


<script type="text/javascript" src="/assets/lib/ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>

<script type="text/javascript" src="/assets/js/form/item-form.js"></script>
<script type="text/javascript" src="/assets/js/form/validation/item-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/selecting-list.js"></script>
<script type="text/javascript" src="/assets/js/form/upload_image.js"></script>
<script type="text/javascript" src="/assets/js/form/upload_banner.js"></script>
<!-- <script type="text/javascript" src="/assets/js/form/upload_preview.js"></script> -->
<script type="text/javascript" src="/assets/js/form/form-datepicker.js"></script>

<script type="text/javascript">

  $(document).ready(function(){

    $('.c-floating-btn.btn-hide-bottom').removeClass('btn-hide-bottom');

    const images = new UploadImage('#item_form','#_image_group','Item','photo',10);
    images.init();
    images.setImages();

    const banner = new UploadBanner('#banner','Item');
    banner.init();

    // const preview = new UploadPreview('#preview','Item');
    // preview.init();

    // const categoryList = new SelectingList('category','#selecting_category','#category_label');
    // categoryList.init();
    // categoryList.getData();

    const itemForm = new ItemForm();
    itemForm.init();

    const locationList = new SelectingList('location','#selecting_location','#location_label');
    locationList.init();
    locationList.getData();

    const date1 = new Datepicker('#date_input_1');
    date1.init();

    const date2 = new Datepicker('#date_input_2');
    date2.init();

    Validation.initValidation();

  });
</script>

@stop