@extends('shared.main')
@section('content')

@include('shared.filter-leftside-nav')

<div class="bg-drop bg-drop-full bg-drop-light-gray-2"></div>

<div class="container-fliud">

  <div class="main-panel overflow-hidden">

    <div class="relative overflow-hidden">

      @if(config('app.module_enabled.pinned_item') && (count($pinnedItems) > 0))
      <div class="relative zi-5 c-shadow-2">

        <div class="tc pt4 ml0 ml4-ns">
          <h4><small>ขอแนะนำ</small></h4>
          <hr class="w-60 w-20-ns center bg-silver">
        </div>

        <div class="ph4 pb4">
          <div class="flash-sale-item owl-carousel owl-theme">
            @foreach($pinnedItems as $_value)
              <?php $value = $_value->buildDataList(); ?>
              @include('shared.item-card-minimal')
            @endforeach
          </div>
        </div>
      </div>
      @endif

      @if(false)
      <div class="tc pa4 relative zi-5">
        <h4 class="mb3"><small>เลือกซื้อสินค้าโดยตรงจากที่พักต่างๆ หรือ ตัวแทนขาย</small></h4>
        <p>เยี่ยมชมและเลือกซื้อสินค้าหรือแพคเกจโดยตรงจาก รีสอร์ท บังกะโล โฮสเทล หรือที่พักในแบบต่างๆ หรือจากตัวแทนขายได้จากที่นี่</p>
        <hr class="w-60 w-20-ns center bg-near-black">
        <a href="/shop" class="btn btn-primary c-shadow-3">
          เยี่ยมชมและเลือกซื้อสินค้าตอนนี้
        </a>
      </div>

      @if(!empty($recommendedShop))

      <div class="shop-recommend-box">

        <div class="shop-recommend-bg box-bg-image c-blur-10 absolute" style="background-image: url({{ $recommendedShop['cover'] }});"></div>

        <div class="shop-recommend-wrapper box-overlay">
          <div class="shop-recommend-content clearfix">
            <div class="shop-recommend-content-left relative w-100 w-50-ns fn fl-ns">
              <div class="avatar frame-border c-shadow-2 tc mb3">
                @if(empty($recommendedShop['profileImage']))
                  <img src="/assets/images/common/shop-avatar.png">
                @else
                  <img src="{{ $recommendedShop['profileImage'] }}">
                @endif
              </div>
            </div>

            <!-- <hr class="bg-white w-90 center db dn-ns"> -->
            <div class="shop-recommend-content-right w-100 w-50-ns fn fl-ns">

              <div class="ph5 pv4 pv5-ns relative zi-10">
                <small>ร้านออนไลน์บนเว็บไซต์</small>
                <h4>
                  <a class="white" href="/shop/page/{{ $recommendedShop['slug'] }}">{{ $recommendedShop['name'] }}</a>
                </h4>

                @if(!empty($recommendedShop['locations']))
                <div class="location-wrapper">
                  <i class="fas fa-map-marker f6 mr1 color-orange"></i>
                  @foreach($recommendedShop['locations'] as $path)
                    <small class="location-name">{{$path['name']}}</small>
                  @endforeach
                </div>
                @endif

                <hr class="bg-near-white">

                <ul class="list-group addition-menu list-group-light list-group-light w-100">
                  <li class="list-group-item">
                    <a href="/shop/page/{{ $recommendedShop['slug'] }}"><i class="fas fa-home"></i> หน้าหลัก</a>
                  </li>
                  <li class="list-group-item">
                    <a href="/shop/page/{{ $recommendedShop['slug'] }}/item"><i class="fas fa-tags"></i> สินค้าในร้าน</a>
                  </li>
                  <li class="list-group-item">
                    <a href="/shop/page/{{ $recommendedShop['slug'] }}/about"><i class="fas fa-ellipsis-h"></i> เกี่ยวกับ</a>
                  </li>
                </ul>

                <hr class="bg-near-white">
                
                <div class=" social-btn-group tc tl-ns">
                  <span class="mb2"><small>แชร์</small></span>
                  <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u=/shop/page/{{ $recommendedShop['slug'] }}" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                  <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url=/shop/page/{{ $recommendedShop['slug'] }}&amp;text={{$recommendedShop['name']}}" target="_blank">
                    <i class="fab fa-twitter"></i>
                  </a>
                  <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url=/shop/page/{{ $recommendedShop['slug'] }}" target="_blank">
                    <i class="fab fa-google-plus-g"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @endif

      @endif

    </div>

    <hr class="w-90 center bg-light-silver mt0 mb5">

    <div class="box-content-item mt5">
      <div class="ph0 ph4-ns pv4 pv5-ns">

        <div class="container">
          <div class="row">

            <div class="col-md-6 pa0">
              
              <div class="image-gallery image-gallery-slide owl-carousel owl-theme">
                @foreach($itemDetail['images'] as $image)
                <figure class="image-item-list c-border-radius-1 c-shadow-2 bg-gray">
                  <a href="/ticket/v/{{$itemDetail['slug']}}" data-size="{{$image['size']}}">
                    <img src="{{$image['_url']}}" />
                    <div class="image-cover"></div>
                  </a>
                </figure>
                @endforeach
              </div>

            </div>
            <div class="col-md-6">
              
              <div class="pt3 tc tl-ns relative zi-5">
                
                <h5 class="mt0 mb3 mb2-ns"><small><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i><i class="fas fa-star gold"></i></small></h5>

                <!-- <h5 class="mt0 mb2 navy"><small><strong>รายการที่น่าสนใจ</strong></small></h5> -->

                <h4>
                  <a class="color-dark-orange" href="/ticket/v/{{$itemDetail['slug']}}">{{$itemDetail['title']}}</a>
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

                <div class="black">
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
                  <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/ticket/v/{{$itemDetail['slug']}}" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                  <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/ticket/v/{{$itemDetail['slug']}}&amp;text={{$itemDetail['title']}}" target="_blank">
                    <i class="fab fa-twitter"></i>
                  </a>
                  <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{URL::to('/')}}/ticket/v/{{$itemDetail['slug']}}" target="_blank">
                    <i class="fab fa-google-plus-g"></i>
                  </a>
                </div>

              </div>

            </div>
          </div>
        </div>

      </div>
    </div>

    <hr class="w-90 center bg-light-silver mv5">

    <div class="relative mb5">
      
      <div class="c-grid-layout clearfix ph2 ph1-ns mt4">

        <div class="min-vh-100">

          <div class="card-loading-animation">
            <div class="thumb pulse"></div>
            <div class="line pulse"></div>
            <div class="line pulse"></div>
            <div class="line pulse"></div>
            <div class="line pulse"></div>
            <div class="line pulse"></div>
            <div class="line pulse"></div>
          </div>

          <div id="item_list_panel"></div>
        </div>
      </div>

      <div class="c-grid-layout ph2 ph0-ns">
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

    <hr class="w-90 center bg-light-silver mt0 mb0">

    <footer class="mb4 mb0-ns tc tl-ns">
      <div class="footer-inner w-90 center pv4 clearfix">
        <div class="w-100 w-50-ns fl">
          <strong>Ticket Easys</strong> © Copyright {{date('Y')}}. All Rights Reserved.
        </div>
        <div class="w-50 fl tr dn db-ns">
          <a href="">
            <i class="fab fa-facebook"></i>
          </a>
        </div>
      </div>
    </footer>

  </div>
</div>

@if(Auth::check())
<script type="text/javascript" src="/assets/js/user-blocking.js"></script>
@endif

<script type="text/javascript" src="/assets/js/particle.js"></script>

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
      this.firstLoading = true;
      this.focusAfterContentRender = false;
      this.inputCount = 0;
      this.outputCount = 0;
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

      $('#filter_location_clear').on('click',function(){
        if(_this.data.location != null) {
           _this.data.location = null;

          _this.handleTimeout = setTimeout(function(){
            _this.loading();
          },500);
        }      
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

      ++this.inputCount;

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
          // Loading.show();
          if(!_this.firstLoading) {
            $(document).scrollTop($('#item_list_panel').offset().top-100);
          }

          _this.firstLoading = false;

          $('#item_list_panel').text('');
          $('.pagination-btn').removeClass('db').addClass('dn');
          $('.card-loading-animation').removeClass('dn').addClass('db');
        },
        // mimeType:"multipart/form-data"
      });

      request.done(function (response, textStatus, jqXHR){

        if(++_this.outputCount == _this.inputCount) {

          $('#item_list_panel').html(response.html);

          if(_this.focusAfterContentRender) {
            $(document).scrollTop($('#item_list_panel').offset().top-100);
          }

          setTimeout(function(){
            $('.item-list-panel').css('opacity',1);
            $('.card-loading-animation').removeClass('db').addClass('dn');
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
            // Loading.hide();
          },400);

          _this.inputCount = 0;
          _this.outputCount = 0;          
        }

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

