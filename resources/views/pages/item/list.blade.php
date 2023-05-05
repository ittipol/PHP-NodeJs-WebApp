@extends('shared.main')
@section('content')

@include('shared.filter-leftside-nav')

<div class="bg-drop bg-drop-full bg-drop-light-gray"></div>

<div class="container-fliud mb5 mb7-ns">
  
  <div class="c-header-shape">
    <span></span>
    <span></span>
    <span></span>
    <span></span>
    <span></span>
  </div>

  <div class="main-panel">

    <div class="c-jumbotron tc" style="background-image:url('{{ $__banner__sm__ }}');">
      <div class="jumbotron jumbotron-fluid mb0">
        <div class="container">
          <h1>บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆที่ไม่ได้ใช้แล้วสามารถนำมาขายได้ที่นี่</h1>
          <a href="/ticket/new" class="btn btn-primary">
            ขายบัตรของคุณตอนนี้
          </a>
        </div>
      </div>
    </div>

    <div class="box-content-item">
      <div class="pv3 pv6-ns ph0 ph4-ns">

        <div class="container">
          <div class="row">
            <div class="col-md-6 pa0">
              
              <div class="image-gallery image-gallery-slide owl-carousel owl-theme">
                @foreach($itemDetail['images'] as $image)
                <figure class="image-item-list c-border-radius-1 c-shadow-2 bg-gray">
                  <a href="/ticket/view/{{$itemDetail['id']}}" data-size="{{$image['size']}}">
                    <img src="{{$image['_url']}}" />
                    <div class="image-cover"></div>
                  </a>
                </figure>
                @endforeach
              </div>

            </div>
            <div class="col-md-6">
              
              <div class="pt4 tc tl-ns">
                
                <!-- <h5 class="mt0 mb2"><small><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i></small></h5> -->

                <h5 class="mt0 mb2 white"><small><strong>รายการแนะนำ</strong></small></h5>

                <h4>
                  <a class="color-dark-orange" href="/ticket/view/{{$itemDetail['id']}}">{{$itemDetail['title']}}</a>
                </h4>

                <div class="mv3">
                  @if(!empty($itemDetail['save']))
                    <div class="price-saving-flag flag-lg mt2">-{{$itemDetail['save']}}</div>
                  @endif

                  <div class="price-section mt2">
                    <span class="price">{{$itemDetail['price']}}</span>
                    @if(!empty($itemDetail['original_price']))
                    <span class="original-price">{{$itemDetail['original_price']}}</span>
                    @endif
                  </div>
                </div>

                <div class="white">
                  @if(!empty($itemDetail['category']))
                  <span>{{$itemDetail['category']}}</span>
                  <span class="mh2">|</span>
                  @endif

                  @if($itemDetail['hasShop'])
                    <a href="/shop/page/{{$itemDetail['owner']['slug']}}" class="text-overflow-ellipsis">{{$itemDetail['owner']['name']}}</a>
                  @else
                    <a href="/profile/{{$itemDetail['created_by']}}/item" class="text-overflow-ellipsis">{{$itemDetail['owner']['name']}}</a>
                  @endif
                </div>

                <hr class="mv3 w-90 center">

                <div class="social-btn-group">
                  <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/ticket/view/{{$itemDetail['id']}}" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                  <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/ticket/view/{{$itemDetail['id']}}&amp;text={{$itemDetail['title']}}" target="_blank">
                    <i class="fab fa-twitter"></i>
                  </a>
                  <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{URL::to('/')}}/ticket/view/{{$itemDetail['id']}}" target="_blank">
                    <i class="fab fa-google-plus-g"></i>
                  </a>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="relative">
      
      <div class="box-top-bg-image box-overlay bg-black-10">
        <div class="box-bg-image lazy" data-src="{{ $__banner__xl__ }}"></div>
      </div>

      <div class="pt5 pb4 relative zi-5">
        
        <div class="c-panel-container">

          <div class="white lh-copy">
            <h3 class="mb3">บทความที่น่าสนใจที่เกี่ยวกับสถานที่ท่องเที่ยว ที่พัก และอื่นๆอีกมากมายจาก Ticket Easys</h3>

            <p>Ticket Easys มีบทความมากมายพร้อมเนื้อหาที่หลากหลายและที่น่าสนใจสำหรับคุณ พร้อมอัพเดททุกวัน</p>

            <div class="mt4">
              <a href="{{ config('app.blog_site_url') }}" class="btn c-btn c-btn-bg c-btn-lg c-btn-shadow c-btn-border-less c-bg-emphasis" target="_blank">ดูบทความจาก Ticket Easys ตอนนี้</a>
            </div>
          </div>

          @if(!$hasShop)

          <hr class="mv4 w-90 center bg-white">

          <div class="tr white lh-copy">
            <h3 class="mb3">เปิดร้านขายตั๋วของคุณ</h3>

            <p>สร้างหน้าร้านของคุณเพื่อใช้ซื้อ-ขาย บัตรคอนเสิร์ต ตั๋ว วอชเชอร์ และอื่นๆ และรวมถึงแบรน์ของคุณได้ฟรี</p>

            <div class="mt4">
              <a href="/shop/create" class="btn c-btn c-btn-bg c-btn-lg c-btn-shadow c-btn-border-less c-bg-emphasis">สร้างร้านขายตั๋วของคุณตอนนี้</a>
            </div>
          </div>

          @endif

        </div>

      </div>

    </div>

    @if(config('app.module_enabled.pinned_item') && (count($pinnedItems) > 0))
    <div class="mb5 pv4 relative zi-5">

      <div class="flash-sale-box c-shadow-2">
        <div class="pt4 ml4">
          <h4 class="red"><i class="fas fa-forward mr-2"></i>FLASH SALE</h4>
        </div>

        <div class="pa4">
          <div class="flash-sale-item owl-carousel owl-theme">
            @foreach($pinnedItems as $_value)
              <?php $value = $_value->buildDataList(); ?>

              @include('shared.item-card-minimal')
            @endforeach
          </div>
        </div>
      </div>

    </div>
    @endif
    
    <div class="c-grid-layout clearfix ph2 ph4-l mt4">
      <h4 class="tc pv3">รายการจากผู้ขายท้่วประเทศ</h4>
      <div id="item_list_panel"></div>
    </div>

    <div class="pagination-btn-group">
      <div class="pagination-btn clearfix">
        <div class="btn fr tr">
          <a id="load_next_btn" class="btn">แสดงถัดไป <i class="fas fa-angle-right"></i></a>
        </div>
        <div class="btn fr">
          <a id="load_prev_btn" class="btn dn"><i class="fas fa-angle-left"></i>ก่อนหน้า</a>
        </div>
      </div>
    </div>

  </div>
</div>


@if(Auth::check())
  @include('shared.item-cancel-modal')
@endif

<script type="text/javascript" src="/assets/js/user-blocking.js"></script>

<script type="text/javascript">

  class TicketFilter {

    constructor() {}

    init() {
      this.bind();
      this.layout();
    }

    bind() {

      let _this = this;

      // $('body').on('click','[data-toggle="left-sidebar"]',function(e){
      //   $(this).attr('disabled',true);
      //   $('body').css('overflow-y','hidden');
      //   $($(this).data('left-sidebar-target')).addClass('show');  
      // });

      // $('body').on('click','[data-toggle="left-sidebar"] > button.close',function(e){
      //   console.log('xxxx');  
      // });

      // $('#fiter_panel_toggle').on('click', function() {
      //   $('#fiter_panel_toggle').attr('disabled',true);
      //   $('body').css('overflow-y','hidden');
      //   $('#left_side_main_filter').addClass('show');
      // });

      // $('#left_side_main_filter > button.close').on('click',function(){
      //   $('#fiter_panel_toggle').attr('disabled',false);
      //   $('body').css('overflow-y','auto');
      //   $('#left_side_main_filter').removeClass('show');
      // });

      $('#location_label').on('click',function(){

        if($(window).height() > 480) {
          $('#left_side_main_filter').css('overflow-y','hidden');
        }
        
      });

      $('#selecting_location').on('click','.close',function(){
        $('#left_side_main_filter').css('overflow-y','auto');
      });

      $('#selecting_location').on('click','.modal-close',function(){
        $('#left_side_main_filter').css('overflow-y','auto');
      });

      $(window).resize(function(){
        _this.layout();
      });

    }

    layout() {

      let wH = window.innerHeight;
      // let wW = window.innerWidth;
      let navbarH = 60;

      $('#left_side_main_filter').css({
        'height': (wH-navbarH)+'px',
        // 'top': navbarH+'px'
      });

    }

  }

  class TicketLoading {
    constructor(token) {
      this.token = token;
      this.data = {
        q: null,
        category: [],
        price_start: 0,
        price_end: 0,
        location: null,
        start_date: null,
        end_date: null,
        sort: 'post_n',
        page: 1,
      };
      this.handleTimeout = null;
    }

    init() {
      this.loading();
      this.bind();
    }

    bind() {
      let _this = this;

      $('#load_next_btn').on('click',function(e) {
        e.preventDefault();
        _this.loading('next');
      });

      $('#load_prev_btn').on('click',function(e) {
        e.preventDefault();
        _this.loading('prev');
      });

      $('#q').on('keyup',function(e) {

        let q = $(this).val();

        _this.data.q = q;

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){

          if(q != '') {
            _this.loading();
          }

        },500);
        
      });

      $('input[name="category[]"]').on('click',function(e) {

        let i = _this.arrayIndexOf(_this.data.category,$(this).val());

        if(i > -1) {
          _this.data.category.splice(i,1);
        }else {
          _this.data.category.push($(this).val());
        }

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          _this.loading();
        },500);
      });

      $('body').on('click','#selecting_location .list-item-label > a',function(e) {
        _this.data.location = $(this).data('id');

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          _this.loading();
        },500);
      });

      $('#price_start').on('keyup',function(e) {

        let price = $(this).val();

        _this.data.price_start = price;

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          if((price !== '') && (/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/g.test(price))) {
            _this.loading();
          }
        },500);

      });

      $('#price_end').on('keyup',function(e) {

        let price = $(this).val();

        _this.data.price_end = price;

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          if((price !== '') && (/^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/g.test(price))) {
            _this.loading();
          }
        },500);

      });

      $('#start_date').on('change',function(e) {
        this.data.start_date = $(this).val();

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          _this.loading();
        },500);
      });

      $('#end_date').on('change',function(e) {
        this.data.end_date = $(this).val();

        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          _this.loading();
        },500);
      });

      $('input[name="sort"]').on('click',function(e) {

        _this.data.sort = $(this).val();
        
        clearTimeout(_this.handleTimeout);
        
        _this.handleTimeout = setTimeout(function(){
          _this.loading();
        },500);
      });
    }

    loading(action = null) {

      let _this = this;

      switch(action) {
        case 'next':
          this.data.page++;
        break;

        case 'prev':
          this.data.page--;
        break;

        default:
          this.data.page = 1;
      }

      let request = $.ajax({
        url: "/ticket-list",
        type: "GET",
        headers: {
          'x-csrf-token': this.token
        },
        data: this.data,
        dataType: 'json',
        // contentType: false,
        // cache: false,
        // processData:false,
        beforeSend: function( xhr ) {
          Loading.show();
          $('#item_list_panel').text('');
          $('.pagination-btn').removeClass('db').addClass('dn');
        },
        // mimeType:"multipart/form-data"
      });

      request.done(function (response, textStatus, jqXHR){

        $('#item_list_panel').html(response.html);

        $(document).scrollTop($('#item_list_panel').offset().top-140);

        setTimeout(function(){
          $('.item-list-panel').css('opacity',1);
        },50);

        if(response.hasData) {

          if(_this.data.page == 1) {
            $('#load_prev_btn').removeClass('db').addClass('dn');
          }else {
            $('#load_prev_btn').removeClass('dn').addClass('db');
          }

          if(response.next) {
            $('#load_next_btn').removeClass('dn').addClass('db');
          }else {
            $('#load_next_btn').removeClass('db').addClass('dn');
          }

        }else {
          $('#load_next_btn').removeClass('db').addClass('dn');
          $('#load_prev_btn').removeClass('db').addClass('dn');
        }

        setTimeout(function(){
          $('.pagination-btn').removeClass('dn').addClass('db');
          Loading.hide();
        },400);

      });

      request.fail(function (jqXHR, textStatus, errorThrown){
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown
        );
      });

    }

    arrayIndexOf(haystack, needle){
      for(var i = 0; i < haystack.length; ++i){
        if(haystack[i] == needle) {
          return i;
        }
      }
      return -1;
    }
  }

  $(document).ready(function(){

    const _ticketLoading = new TicketLoading('{{ csrf_token() }}');
    _ticketLoading.init();

    const _ticketFilter = new TicketFilter();
    _ticketFilter.init();

    // const detailBox = new DetailBox('{{ csrf_token() }}');
    // detailBox.init();

    @if(Auth::check())
      const userBlocking = new UserBlocking();
      userBlocking.init();
    @endif

    $('.flash-sale-item').owlCarousel({
      loop: true,
      margin: 15,
      nav: false,
      dots: true,
      autoplay:true,
      autoplayTimeout:4000,
      autoplayHoverPause:true,
      responsiveClass: true,
      responsive: {
        0: {
          items: 1,
        },
        768: {
          items: 2
        },
        1024: {
          items: 3,
          // nav: true
        },
        1440: {
          items: 4,
        },
        1920: {
          items: 5,
          // nav: true
          // margin: 20
        }
      }
    });

    @if(count($itemDetail['images']) == 1)

    $('.image-gallery-slide').owlCarousel({
      loop: false,
      nav: false,
      dots: false,
      margin: 10,
      autoplay:false,
      responsiveClass: true,
      responsive: {
        1920: {
          items: 1
        }
      }
    });
    
    @else

    $('.image-gallery-slide').owlCarousel({
      loop: true,
      nav: false,
      dots: true,
      margin: 10,
      autoplay:true,
      autoplayTimeout:4000,
      autoplayHoverPause:true,
      responsiveClass: true,
      items: 1
    });

    @endif
  });

</script>

@stop