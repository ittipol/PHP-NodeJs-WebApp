{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div class="main-search-box">
  {{ Form::text('q', null, array(
    'id' => 'search_box',
    'placeholder' => 'ค้นหา',
    'autocomplete' => 'off'
  )) }}
</div>

<div class="main-search-bar">
  <div id="model_search_panel" class="c-modal">
    <a class="close"></a>
    <div class="c-modal-inner">

      <a class="modal-close">
        <span aria-hidden="true">&times;</span>
      </a>

      <div class="c-panel-container">

        <div class="mv4">
          <h5 class="mb3"><small>ราคา</small></h5>

          <div class="clearfix">
            <div class="w-50 fr">
              {{ Form::text('price_start', null, array(
                'id' => 'price_start',
                'class' => 'w-100 pa2',
                'placeholder' => 'ราคาเริ่มต้น',
                'autocomplete' => 'off'
              )) }}
            </div>

            <div class="w-50 fr">
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
          <h5 class="mb3"><small>ค้นหาตามพื่นที่</small></h5>

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
          <h5 class="mb3"><small>รายการขายจาก</small></h5>

          <label class="control control--checkbox mb2 mr3 dib">
            บุคคลทั่วไป
            {{Form::checkbox('user', '1')}}
            <div class="control__indicator"></div>
          </label>
          <label class="control control--checkbox mb2 dib">
            ร้านขายสินค้า
            {{Form::checkbox('shop', '1')}}
            <div class="control__indicator"></div>
          </label>
        </div>

        <div class="mv4">
          <h5 class="mb3"><small>การขาย</small></h5>

          <label class="control control--checkbox mb2 mr3 dib">
            ขาย
            {{Form::checkbox('sell', '1')}}
            <div class="control__indicator"></div>
          </label>
          <label class="control control--checkbox mb2 dib">
            ซื้อ
            {{Form::checkbox('buy', '1')}}
            <div class="control__indicator"></div>
          </label>
        </div>

        <div class="mv4">
          <h5 class="mb3"><small>รูปแบบสินค้า</small></h5>

          <label class="control control--checkbox mb2 mr3 dib">
            สินค้าใหม่
            {{Form::checkbox('new', '1')}}
            <div class="control__indicator"></div>
          </label>
          <label class="control control--checkbox mb2 dib">
            สินค้ามือสอง
            {{Form::checkbox('old', '1')}}
            <div class="control__indicator"></div>
          </label>
          <label class="control control--checkbox mb2 dib">
            ผลิตภัณฑ์จากธุรกิจครัวเรือน
            {{Form::checkbox('homemade', '1')}}
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
</div>

<div id="selecting_location" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner h-100">

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

        if($('#search_box').val().trim() === '') {
          $('#search_box').removeAttr('name');
        }

        Loading.show();

      });

    }

  }

  class SearchBox {

    constructor() {
      this.lastScrollTop = 0;
    }

    init() {
      this.bind();
    }

    bind() {

      let _this = this;

      $('#search_box').on('focus',function(){
        $('.main-search-box').addClass('focus');

        const modal = new ModalDialog();
        modal.show('#model_search_panel');
      });

      $('#model_search_panel').on('click','.close',function(){
        $('.main-search-box').removeClass('focus');
      });

      $('#model_search_panel').on('click','.modal-close',function(){
        $('.main-search-box').removeClass('focus');
      });

      $(window).on('scroll',function(){
        let st = $(this).scrollTop();
        if (st > _this.lastScrollTop){
          $('.main-search-box').addClass('hide');
        } else {
          $('.main-search-box').removeClass('hide');
        }
        _this.lastScrollTop = st;
      });
    }

  }

  $(document).ready(function(){

    const filter = new Filter();
    filter.init();

    const searchBox = new SearchBox();
    searchBox.init();

    const locationList = new SelectingList('location','#selecting_location','#location_label');
    locationList.init();
    @if(empty($locationSearchingData))
      locationList.getData();
    @else
      locationList.setDataId({{$locationSearchingData['id']}});
      locationList.setDataPath({!!$locationSearchingData['path']!!});
      locationList.setSelectedLabel();
    @endif

  });
</script>