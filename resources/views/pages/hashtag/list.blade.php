@extends('shared.main')
@section('content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container c-list-container-lg pv2">
    <div class="row">

      <div class="col-md-6">
        <div class="pv3">
          <h4 class="mt1">
            Hashtag {{$hashtag}}
          </h4>
        </div>
      </div>

      <div class="col-md-6">
        <div class="pv2 clearfix">
          <div class="fr">
            <a href="#" data-toggle="modal" data-c-modal-target="#model_hashtag" class="btn btn-secondary icon-green b--transparent"><i class="fas fa-hashtag"></i> Hashtag</a>
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

      <?php $value = $_value->buildDataList(); ?>

      @include('shared.item-card-w-menu')

    @endforeach
  </div>
  {{$data->links('shared.pagination', ['paginator' => $data])}}
</div>

@include('shared.modal.detail-box')

@else

<div class="c-list-container c-list-container-lg mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5>ไม่พบรายการขายที่เกี่ยวกับ {{$hashtag}}</h5>
    </div>
  </div>
</div>

@endif

<div id="model_hashtag" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner pt4">

    <div class="c-panel-container">

      <a class="modal-close fixed-outer">
        <span aria-hidden="true">&times;</span>
      </a>

      <div class="mt7">
        <div class="pt3 pt6-ns">
          {{ Form::text('hashtag', null, array(
            'id' => 'hashtag',
            'class' => 'c-search-field pa2 w-100',
            'placeholder' => '#hashtag',
            'autocomplete' => 'off'
          )) }}
          <small class="ml2 mt1 db">ไม่ต้องกรอก # หน้ากลุ่มคำ</small>
          <div class="tr">
            <button type="submit" id="hashtag_submit" class="btn c-btn c-btn-bg mt3 br0">ค้นหา Hashtag</button>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div id="model_filter" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner">

    <a class="modal-close fixed-outer">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="c-panel-container">

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
        <h5 class="mb3"><small>พื่นที่</small></h5>

        <div class="selecting-lable-box">
          <div id="location_label" class="selected-value" data-toggle="modal" data-c-modal-target="#selecting_location" data-selecting-empty-label="เลือกพื่นที่">
            <i class="fas fa-map-marker color-orange mr3"></i>เลือกพื่นที่
          </div>
          
          <a class="selected-value-delete">
            <span aria-hidden="true">&times;</span>
          </a>
        </div>
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
          <button type="submit" class="btn c-btn c-btn-bg br0">กรอง</button>
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
{{Form::close()}}

<div class="clearfix margin-top-200"></div>

<script type="text/javascript" src="/assets/js/form/selecting-list.js"></script>
<script type="text/javascript" src="/assets/js/detail-box.js"></script>


<script type="text/javascript">

  class Hashtag {

    constructor() {}

    init() {
      this.bind();
    }

    bind() {

      let _this = this;

      $('#hashtag').on('keypress',function(event){

        if((event.keyCode == 13) && ($(this).val().trim() !== '')) {
          _this.search($(this).val().trim());
        }

      });

      $('#hashtag_submit').on('click',function(){

        if($('#hashtag').val().trim() != '') {
          _this.search($('#hashtag').val().trim());
        }

      });

    }

    search(hashtag) {
      Loading.show();
      location.href = '/hashtag/'+hashtag.replace('#','');
    }

  }

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

        if($('input[name="location"]').val() === '') {
          $('input[name="location"]').removeAttr('name');
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

    const hashtag = new Hashtag();
    hashtag.init();

    // const detailBox = new DetailBox('{{ csrf_token() }}');
    // detailBox.init();

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