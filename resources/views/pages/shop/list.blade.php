@extends('shared.main')
@section('content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container pv2">
    <div class="row">

      <div class="col-6">
        <div class="pv2 pv3-ns">
          <h4 class="mt1 white">ร้านขายตั๋ว</h4>
        </div>
      </div>

      <div class="col-6">
        <div class="pv2 clearfix">
          <div class="fr">
            <a href="#" data-toggle="modal" data-c-modal-target="#model_search" class="btn btn-secondary icon-green b--transparent"><i class="fas fa-search"></i> ค้นหา</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@if(($data->total() > 0) && ($data->currentPage() <= $data->lastPage()))

@if(!$hasShop)
<div class="c-list-container c-list-container-lg mv5">
  <div class="alert alert-secondary tc">
    คุณยังไม่มีร้านขายตั๋ว <a href="/shop/create">สร้างร้านขายตั๋วของคุณตอนนี้</a>
  </div>
</div>
@endif

<div class="c-list-container c-list-container-lg ph4 mv5">
  <div class="row">
    @foreach($data as $_value)
      <?php $value = $_value->buildDataList(); ?>

      @include('shared.shop-card')
    @endforeach
  </div>
  {{$data->links('shared.pagination', ['paginator' => $data])}}
</div>

@elseif($searching)

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5 class="white">ไม่พบร้านขายตั๋วที่ตรงกับการค้นหา</h5>
      <p>โปรดลองค้นหาอีกครั้ง ด้วยคำที่แตกต่างหรือคำที่มีความหมายใกล้เคียง</p>
    </div>
  </div>
</div>

@else

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5 class="white">ยังไม่มีร้านขายตั๋ว</h5>
      <a href="/shop/create" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">สร้างร้านขายตั๋วของคุณตอนนี้</a>
    </div>
  </div>
</div>

@endif

{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div id="model_search" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner">

    <a class="modal-close fixed-outer">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="c-panel-container">

      <div class="mv4">
        <div>
          {{ Form::text('q', null, array(
            'id' => 'q',
            'class' => 'c-search-field pa2 w-100',
            'placeholder' => 'ชื่อร้านขายตั๋วที่ต้องการค้นหา',
            'autocomplete' => 'off'
          )) }}
        </div>
      </div>

      <div class="mv4">
        <h5 class="mb3"><small>เลือกประเภทสินค้าที่มีขายในร้านขายตั๋ว</small></h5>

        <div>
          <a href="#" data-toggle="modal" data-c-modal-target="#modal_select_catagory" class="btn btn-secondary"><i class="fas fa-tasks"></i> เลือกประเภทสินค้า</a>
        </div>
      </div>

      <div class="mv4">
        <h5 class="mb3"><small>ตำแหน่งที่ตั้ง</small></h5>

        <div class="selecting-lable-box">
          <div id="location_label" class="selected-value" data-toggle="modal" data-c-modal-target="#selecting_location"  data-selecting-empty-label="เลือกที่ตั้งร้านขายตั๋ว">
            เลือกที่ตั้งร้านขายตั๋ว
          </div>
          
          <a class="selected-value-delete">
            <span aria-hidden="true">&times;</span>
          </a>
        </div>
      </div>

      <div class="mv4">
        <h5 class="mb3"><small>จัดเรียงตาม</small></h5>

        <div class="c-input">
          {{Form::radio('sort', 'post_n', true, array('id' => 'sort1'))}}
          <label for="sort1">
            ร้านขายตั๋ว - ใหม่ไปเก่า
          </label>
        </div>
        <div class="c-input">
          {{Form::radio('sort', 'post_o', false, array('id' => 'sort2'))}}
          <label for="sort2">
            ร้านขายตั๋ว - เก่าไปใหม่
          </label>
        </div>
        <div class="c-input">
          {{Form::radio('sort', 'name_asc', false, array('id' => 'sort3'))}}
          <label for="sort3">
            ตัวอักษร - A ไป Z ก ไป ฮ
          </label>
        </div>
        <div class="c-input">
          {{Form::radio('sort', 'name_desc', false, array('id' => 'sort4'))}}
          <label for="sort4">
            ตัวอักษร - A ไป Z ก ไป ฮ
          </label>
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
  
  {{ Form::hidden('location') }}
</div>

<div id="modal_select_catagory" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner w-100">

    <h4 class="f4 f3-ns mb-3 mb4-ns">เลือกประเภทสินค้าที่มีขายในร้านขายตั๋ว</h4>

    <a class="btn btn-secondary modal-close f5">
      <span aria-hidden="true">ตกลง</span>
    </a>

    <div class="row">
      @foreach($categories as $key => $category)
      <div class="col-12 col-md-6 col-lg-4 col-xs-3">
        <div class="c-input">
          {{Form::checkbox('category[]', $category->id, null, array('id' => 'cat'.$key, 'data-c-error' => '#cat_error_message'))}}
          <label for="cat{{$key}}">
            {{$category->name}}
          </label>
        </div>
      </div>
      @endforeach
    </div>

  </div>
</div>
{{Form::close()}}

<script type="text/javascript" src="/assets/js/user-blocking.js"></script>
<script type="text/javascript" src="/assets/js/form/selecting-list.js"></script>

<script type="text/javascript">

  class Filter {

    constructor() {}

    init() {
      this.bind();
    }

    bind() {

      let _this = this;

      $('#filter').on('submit',function(){

        if($('input[name="location"]').val() === '') {
          $('input[name="location"]').removeAttr('name');
        }

        if($('#q').val().trim() === '') {
          $('#q').removeAttr('name');
        }

        Loading.show();

      });

    }

  }

  $(document).ready(function(){

    const filter = new Filter();
    filter.init();
    
    const locationList = new SelectingList('location','#selecting_location','#location_label');
    locationList.init();
    @if(empty($locationSearchingData))
      locationList.getData();
    @else
      locationList.setDataId({{$locationSearchingData['id']}});
      locationList.setDataPath({!!$locationSearchingData['path']!!});
      locationList.setSelectedLabel();
    @endif

    @if(Auth::check())
      const userBlocking = new UserBlocking();
      userBlocking.init();
    @endif
    
  });
</script>

@stop