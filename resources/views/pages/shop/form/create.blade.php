@extends('shared.main')
@section('content')

<div class="c-jumbotron tc" style="background-image:url('{{ $__banner__sm__ }}');">
  <div class="jumbotron jumbotron-fluid mb0">
    <div class="container">
      <h1>ทำให้ผู้เห็นและคนเข้าถึงได้อย่างง่าย ด้วยการเปิดร้านออนไลน์บนเว็บไซต์พร้อมการสนับสนุนจากเรา</h1>
    </div>
  </div>
</div>

<div class="c-form-container mt4 mb5">
  <h4 class="mb2">ยังไม่มีร้านขายสินค้า</h4>
  <p>สร้างร้านขายสินค้าของคุณและขายสินค้า แพ็กเกจต่างๆ หรือสนับสนุนแบรน์ของคุณได้ฟรี</p>
  <div>
    <a href="/support/shop-creating">เรียนรู้เพิ่มเติม</a>
  </div>
</div>

{{Form::open(['id' => 'shop_create_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

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
      <div class="col-md-12">

        <div class="form-group">
          <label class="form-control-label required">เลือกประเภทสินค้าที่ขายในร้านขายสินค้า</label>
          <div>
            <a href="#" data-toggle="modal" data-c-modal-target="#modal_select_catagory" class="btn btn-secondary"><i class="fas fa-tasks"></i> เลือกประเภทสินค้า</a>
          </div>
          <div id="cat_error_message" class="c-error"></div>

          <div id="selected_category_box" class="mv3"></div>
          
          <div id="modal_select_catagory" class="c-modal">
            <a class="close"></a>
            <div class="c-modal-sidebar-inner">

              <a class="btn btn-secondary modal-close f5">
                <span aria-hidden="true">ตกลง</span>
              </a>

              <h4 class="item-title f4 f3-ns mb-3 mb4-ns">เลือกประเภทสินค้า</h4>

              <div class="row">
                @foreach($categories as $key => $category)
                <div class="col-12 col-md-6">
                  <div class="c-input">
                    {{Form::checkbox('ShopToCategory[category_id][]', $category->id, null, array('id' => 'cat'.$key, 'data-name' => $category->name, 'data-c-error' => '#cat_error_message'))}}
                    <label for="cat{{$key}}">
                      {{$category->name}}
                    </label>
                  </div>
                </div>
                @endforeach
              </div>

            </div>
          </div>

        </div>

        <div class="form-group">
          <label class="form-control-label required">ชื่อร้านขายสินค้า แบรนด์ หรือธุรกิจ</label>
          {{ Form::text('name', null, array(
            'class' => 'form-control',
            'autocomplete' => 'off'
          )) }}
        </div>

        <div class="form-group">
          <label class="form-control-label">คำอธิบาย เพื่ออธิบายถึงร้านขายสินค้า แบรนด์ หรือธุรกิจของคุณ</label>
          <div class="alert alert-secondary" role="alert">
            <strong>URL</strong> คุณสามารถป้อนเว็บที่เกี่ยวข้องได้ และ URL ที่ป้อนนั้นจะสามารถคลิกได้เมื่ออยู่ในหน้าแสดงข้อมูล
          </div>
          {{Form::textarea('description', null, array('id' => 'description', 'class' => 'form-control'))}}
        </div>

        <div class="form-group">
          <label class="form-control-label required">การติดต่อร้านขายสินค้า</label>
          <div class="alert alert-secondary" role="alert">
            หมายเลขโทรศัพท์, Facebook, Line ID หรืออื่นๆ <a href="#" data-toggle="modal" data-c-modal-target="#model_tip_input_shop_contact">แสดงเคล็ดลับเพิ่มเติม</a>
          </div>
          {{Form::textarea('contact', null, array('id' => 'contact', 'class' => 'form-control'))}}
        </div>

        <div class="form-group">
          <label class="form-control-label">ตำแหน่งที่ตั้ง</label>
          
          <div class="selecting-lable-box">
            <div id="location_label" class="selected-value" data-toggle="modal" data-c-modal-target="#selecting_location" data-selecting-empty-label="ระบุตำแหน่งที่ตั้ง">
              ระบุตำแหน่งที่ตั้ง
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
            <input type="hidden" name="ShopToLocation[location_id]">
          </div>

        </div>

      </div>
    </div>
  </div>

  <div class="bb b--moon-gray mb4">
    <div class="c-form-container">
      <h5 class="mb3">การรวมรายการขายไปยังร้านขายสินค้า</h5>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <div class="alert alert-secondary" role="alert">
            หากมีรายการขายที่ได้รายการขายไว้แล้ว รายการขายทั้งหมดจะถูกรวมไปยังร้านขายสินค้าและจะแสดงในนามร้านขายสินค้า <a href="#" data-toggle="modal" data-c-modal-target="#model_cancel_import_item">อ่านเพื่มเติม</a>
            หากไม่ต้องการให้ทำการรวมรายการขายไปยังร้านขายสินค้า ให้เลือกตัวเลือกด้านล่างนี้
          </div>
          <label class="control control--checkbox mb-2">
            ยกเลิกและลบรายการขายที่ได้รายการขายไว้แล้ว
            <input type="checkbox" name="cancel" value="1">
            <div class="control__indicator"></div>
          </label>
        </div>
      </div>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        <div class="mb3">
          การคลิก "เริ่มต้นใช้งาน" จะเป็นการแสดงว่าคุณยินยอมตาม<a href="#" data-toggle="modal" data-c-modal-target="#modal_shop_creating_term_and_condition">ข้อกำหนดและเงื่อนไข</a>แล้ว
        </div>

        {{Form::submit('เริ่มต้นใช้งาน', array('class' => 'btn btn-primary btn-block'))}}
      </div>
    </div>
  </div>

{{Form::close()}}

<div class="clearfix margin-top-200"></div>

<!-- Modal -->
@include('shared.modal.tip-input-shop-contact')
@include('shared.modal.cancel-import-item')
@include('shared.modal.shop-creating-term-and-condition')


<script type="text/javascript" src="/assets/lib/ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="/assets/js/form/validation/shop-create-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/selecting-list.js"></script>

<script type="text/javascript">

  class ShopForm {

    constructor() {}

    init() {
      this.bind();
    }

    bind() {

      let _this = this;

      $('#modal_select_catagory').on('click','input[type="checkbox"]',function(){
        if($(this).is(':checked')) {
          _this.createCatTag($(this).prop('id'),$(this).data('name'));
        }else {
          $('[data-element-id="'+$(this).prop('id')+'"]').remove();
        }
      });

      $('#selected_category_box').on('click','.remove-cat-btn',function(){

        let elem = document.getElementById($(this).data('cat-id'));

        if($(elem).is(':checked')) {
          $(elem).prop('checked', false);
        }

        $('[data-element-id="'+$(this).data('cat-id')+'"]').remove();
      });

    }

    checkCatSelected() {

      let _this = this;

      $('#modal_select_catagory').find('input[type="checkbox"]').each( function( index, element ){
        if($(this).is(':checked')) {
          _this.createCatTag($(this).prop('id'),$(this).data('name'));
        }
      });
    }

    createCatTag(id,name) {
      let removeBtn = document.createElement('span');
      removeBtn.setAttribute('class','remove-cat-btn pointer ml3 red hover-dark-red f4 b');
      removeBtn.setAttribute('data-cat-id',id);
      removeBtn.innerHTML = '×';

      let label = document.createElement('span');
      label.innerHTML = name;

      let tag = document.createElement('div');
      tag.setAttribute('class','data-tag tag-border mr2 mv2');
      tag.setAttribute('data-element-id',id);
      
      tag.appendChild(label);
      tag.appendChild(removeBtn);
      
      document.getElementById('selected_category_box').appendChild(tag);
    }

  }

  $(document).ready(function(){

    ClassicEditor
        .create( document.querySelector( '#description' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
        } )
        .catch( error => {
            console.log( error );
        } );

    ClassicEditor
        .create( document.querySelector( '#contact' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
        } )
        .catch( error => {
            console.log( error );
        } );

    const locationList = new SelectingList('location','#selecting_location','#location_label');
    locationList.init();
    locationList.getData();

    const shopForm = new ShopForm()
    shopForm.init();

    Validation.initValidation();

  });
</script>

@stop