@extends('shared.shop-with-menu')
@section('shop-content')

<div class="bg-drop bg-drop-full"></div>

@include('pages.shop.layout.page-blocking-bar')

<!-- page header -->
@include('pages.shop.layout.page-header')
<!--  -->

@include('pages.shop.layout.page-menu')

<div class="mb4">
  <div class="c-list-container pv2">
    <div class="row">

      <div class="col-6">
        <div class="pa3">
          <h4 class="mb0 white">
            สินค้าในร้าน
          </h4>
        </div>
      </div>

      <div class="col-6">
        <div class="pv2 clearfix">
          <div class="fr">
            <a href="#" data-toggle="modal" data-c-modal-target="#model_filter" class="btn btn-secondary icon-green b--transparent"><i class="fas fa-sort-amount-down"></i> จัดเรียง</a></a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@if(($data->total() > 0) && ($data->currentPage() <= $data->lastPage()))

<div class="c-list-container c-list-container-lg ph4">
  <div class="row">

    @foreach($data as $_value)

      <?php $value = $_value->buildDataList2(); ?>

      @include('shared.item-card')

      <div id="model_additional_menu_{{$value['id']}}" class="c-modal">
        <a class="close"></a>
        <div class="c-modal-addition-menu-inner c-addition-menu-modal-sheet">

          <a class="modal-close">
            <span aria-hidden="true">&times;</span>
          </a>

          <div class="c-addition-menu-modal-sheet-header"></div>

          <h5 class="pr4">{{$value['title']}}</h5>

          <ul class="list-group addition-menu">
            <li class="list-group-item">
              <a href="/ticket/view/{{$value['id']}}" data-detail-box="1" data-detail-id="{{$value['id']}}"><i class="fas fa-arrow-right"></i>อ่าน</a>
            </li>
            @if(Auth::check() && (Auth::user()->id == $value['created_by']))
            <li class="list-group-item">
              <a href="/ticket/edit/{{$value['id']}}"><i class="far fa-edit"></i>แก้ไขรายการขาย</a>
            </li>
            <li class="list-group-item">
              <a href="javascript:void(0);" data-t-id="{{$value['id']}}" data-t-title="{{$value['title']}}" data-t-cancel-modal="1"><i class="fas fa-times"></i>ปิดรายการขาย</a>
            </li>
            @else
            <li class="list-group-item">
              <a href="#" data-chat-box="1" data-chat-data="m|Item|{{$value['id']}}" data-chat-close="1"><i class="fas fa-comments"></i>แชท</a>
            </li>
            @endif
            @if(Auth::check() && (Auth::user()->id != $value['created_by']))
            <li class="list-group-item">
              <a href="#" data-blocking="1" data-blocked-type="Item" data-blocked-id="{{$value['id']}}" class="user-blocking-item">
                @if($value['blockedItem'])
                <span class="user-blocking-icon">
                  <i class="fas fa-stop"></i>
                </span>
                <span class="user-blocking-label">
                  ยกเลิกไม่สนใจรายการขายนี้
                </span>
                @else
                <span class="user-blocking-icon">
                  <i class="fas fa-ban"></i>
                </span>
                <span class="user-blocking-label">
                  ไม่สนใจรายการขายนี้
                </span>
                @endif
              </a>
            </li>
            @endif
            <li class="list-group-item">
              <a href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/ticket/view/{{$value['id']}}" target="_blank"><i class="fab fa-facebook-f social-color"></i>แชร์ Facebook</a>
            </li>
            <li class="list-group-item">
              <a href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/ticket/view/{{$value['id']}}&amp;text={{$value['title']}}"><i class="fab fa-twitter social-color"></i></i>แชร์ Twitter</a>
            </li>
            <li class="list-group-item">
              <a href="https://plus.google.com/share?url={{URL::to('/')}}/ticket/view/{{$value['id']}}"><i class="fab fa-google-plus-g social-color"></i>แชร์ Google+</a>
            </li>
          </ul>

        </div>
      </div>

    @endforeach

  </div>
  {{$data->links('shared.pagination', ['paginator' => $data])}}
</div>

@include('shared.modal.detail-box')

@elseif($searching)

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5>ไม่พบรายการขายที่ตรงกับการค้นหา</h5>
      <p>โปรดลองค้นหาอีกครั้ง ด้วยคำที่แตกต่างหรือคำที่มีความหมายใกล้เคียง</p>
    </div>
  </div>
</div>

@else

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5 class="white">ยังไม่มีรายการขาย</h5>
      @if(Auth::check() && (Auth::user()->id == $shop['created_by']))  
      <a href="/ticket/new" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">ขายบัตรของคุณตอนนี้</a>
      @endif
    </div>
  </div>
</div>

@endif

{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div id="model_filter" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner">
    
    <a class="modal-close fixed-outer">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="c-panel-container">

      <div class="mv4">
        <h5 class="mb3"><small>ค้นหา</small></h5>
        {{ Form::text('q', null, array(
          'class' => 'w-100 pa2',
          'placeholder' => 'ชื่อบัตร สถานที่ หรือ คำค้นอื่นๆ',
          'autocomplete' => 'off'
        )) }}
      </div>

      <div class="mv4">
        <h5 class="mb3"><small>ราคา</small></h5>

        <div class="row">
          <div class="col-6">
            {{ Form::text('price_start', null, array(
              'id' => 'price_start',
              'class' => 'w-100 pa2',
              'placeholder' => 'ราคาเริ่มต้น',
              'autocomplete' => 'off'
            )) }}
          </div>

          <div class="col-6">
            {{ Form::text('price_end', null, array(
              'id' => 'price_end',
              'class' => 'w-100 pa2',
              'placeholder' => 'สูงสุด',
              'autocomplete' => 'off'
            )) }}
          </div>
        </div>

      </div>

      <div class="mv4">
        <h5 class="mb3"><small>การแสดงรายการ</small></h5>
        <label class="control control--checkbox mb2 dib">
          แสดงสินค้าที่หมดอายุการใช้งานแล้ว
          {{Form::checkbox('expired', '1')}}
          <div class="control__indicator"></div>
        </label>
      </div>

      <div class="mv4">
        
        <h5 class="mb3"><small>จัดเรียงตาม</small></h5>

        <div>
          <div class="c-input">
            {{Form::radio('sort', 'post_n', true, array('id' => 'sort1'))}}
            <label for="sort1">
              รายการขาย - ใหม่ไปเก่า
            </label>
          </div>
          <div class="c-input">
            {{Form::radio('sort', 'post_o', false, array('id' => 'sort2'))}}
            <label for="sort2">
              รายการขาย - เก่าไปใหม่
            </label>
          </div>
          <div class="c-input">
            {{Form::radio('sort', 'price_h', false, array('id' => 'sort3'))}}
            <label for="sort3">
              ราคา - สูงไปต่ำ
            </label>
          </div>
          <div class="c-input">
            {{Form::radio('sort', 'price_l', false, array('id' => 'sort4'))}}
            <label for="sort4">
              ราคา - ต่ำไปสูง
            </label>
          </div>
        </div>
      </div>

    </div>

    <div class="c-panel-container">
      <div class="row">
        <div class="col-12">
          <button type="submit" class="btn c-btn c-btn-bg br0">ค้นหา</button>
        </div>
      </div>
    </div>

  </div>
</div>
{{Form::close()}}

<!-- <div class="clearfix margin-top-200"></div> -->

<!-- <script type="text/javascript" src="/assets/js/detail-box.js"></script> -->

<script type="text/javascript">

  class Filter {

    constructor() {}

    init() {
      this.bind();
    }

    bind() {

      let _this = this;

      $('#filter').on('submit',function(){

        let priceStart = $('#price_start').val().trim();
        let priceEnd = $('#price_end').val().trim();

        if((priceStart !== '') && (!/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/g.test(priceStart))) {
          const snackbar = new Snackbar();
          snackbar.setTitle('จำนวนราคาไม่ถูกต้อง');
          snackbar.display();

          return false;
        }else if((priceEnd !== '') && (!/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/g.test(priceEnd))) {
          const snackbar = new Snackbar();
          snackbar.setTitle('จำนวนราคาไม่ถูกต้อง');
          snackbar.display();

          return false;
        }else if(((priceStart !== '') && (priceEnd !== '')) && (parseInt(priceStart) >= parseInt(priceEnd))) {
          const snackbar = new Snackbar();
          snackbar.setTitle('จำนวนราคาเริ่มต้นหรือสิ้นสุดไม่ถูกต้อง');
          snackbar.display();

          return false;
        }

        if(priceStart === '') {
          $('#price_start').removeAttr('name');
        }

        if(priceEnd === '') {
          $('#price_end').removeAttr('name');
        }

        Loading.show();

      });

    }

  }

  $(document).ready(function(){

    const filter = new Filter();
    filter.init();

    // const detailBox = new DetailBox('{{ csrf_token() }}');
    // detailBox.init();

    // $('.lazy').lazy({
    //   effect: "fadeIn",
    //   effectTime: 220,
    //   threshold: 0
    // });

  });
</script>

@stop