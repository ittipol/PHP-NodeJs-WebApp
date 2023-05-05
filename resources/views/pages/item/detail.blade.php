@extends('shared.item-detail-with-menu')
@section('item-content')

<div class="bg-drop bg-drop-full"></div>

<div class="detail-wrapper detail-page {{$data['theme']}}">
  <!-- @if(!$data['approved'])
  <div class="pv2 pv3-ns bg-red">
    <div class="c-detail-container">
      <div class="alert ma0 pv0 white">
        <h4 class="alert-heading mb0">รายการขายอยู่ระหว่างการตรวจสอบ</h4>
        <p class="ma0">รายการขายนี้อยู่ระหว่างการตรวจสอบ <strong>การตรวจสอบจะใช้เวลา 5 - 10 นาที</strong><br>เฉพาะเจ้าของรายการขายที่สามารถเห็นรายการขายนี้ได้ในระหว่างการตรวจสอบ</p>
      </div>
    </div>
  </div>
  @endif -->

  @if($data['cancel_option'] != 0)
  <div class="pv3 pv4-ns bg-moon-gray">
    <div class="c-detail-container">
      <div class="alert ma0 pv0 tc">
        ผู้ขายยกเลิกรายการขายนี้แล้ว
      </div>
    </div>
  </div>
  @endif

  @if(!empty($data['banner']))
  <div class="banner banner-header tc">
    <div class="banner-bg dn db-l" style="background-image: url('{{$data['banner']}}');"></div>
    <img src="{{$data['banner']}}">
  </div>
  @endif

  <div class="bg-white">

    <!-- <div class="c-detail-container pt4">
      <div class="row">
        <div class="col-md-8">
          <h3 class="detail-title">{{$data['title']}}</h3>
        </div>
      </div>
    </div> -->

    <div class="c-detail-container pt4 tc">
      <div class="w-90 center">
        <h3 class="detail-title">{{$data['title']}}</h3>
      </div>
    </div>

    <div class="c-detail-container social-btn-group tc">
      <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
        <i class="fab fa-facebook-f"></i>
      </a>
      <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/ticket/view/{{$data['id']}}&amp;text={{$data['title']}}" target="_blank">
        <i class="fab fa-twitter"></i>
      </a>
      <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{Request::fullUrl()}}" target="_blank">
        <i class="fab fa-google-plus-g"></i>
      </a>
    </div>

    <div>
      @include('shared.image-gallery')
    </div>

    <div class="c-detail-container pb3">
      <div class="row">

        <div class="col-12">
          <div class="row relative">
            <div class="col-md-8 item-content-section">

              @if($data['quantity'] == 0)
              <div class="alert alert-danger mt-3" role="alert">
                สินค้าหมด
              </div>
              @endif

              <div class="c-detail-container pv3 bb b--moon-gray">
                @if($data['date_type'] == 0)
                  <div class="pa2">
                    <small>วันที่ใช้งาน</small>
                    <div>ไม่ระบุ</div>
                  </div>
                @elseif($data['date_type'] == 1)
                  <div class="pa2">
                    <h5>บัตรหมดอายุในอีก</h5>
                    <strong><div class="ticket-countdown" id="countdown_{{$data['id']}}">-</div></strong>
                  </div>
                @elseif($data['date_type'] == 2)
                  <div class="pa2">
                    <small>งานจะเริ่มขึ้นในอีก</small>
                    <strong><div class="ticket-countdown" id="countdown_{{$data['id']}}">-</div></strong>
                  </div>
                @elseif($data['date_type'] == 3)
                  <div class="pa2">
                    <small>เริ่มเดินทางในอีก</small>
                    <strong><div class="ticket-countdown" id="countdown_{{$data['id']}}">-</div></strong>
                  </div>
                @endif
              </div>

              <div class="data-detail-section pt2-ns mv3">{!! $data['description'] !!}</div>

              <hr>

              <div id="display_contact" class="display-contact">

                <div id="display_contact_btn" class="tc">
                  <button class="btn c-btn__red style-invert">
                    <i class="fas fa-phone"></i> ดูการติดต่อผู้ขาย
                  </button>
                </div>
                
                <div id="loading_contact" class="global-loading-indicator loading-content"></div>
              </div>

            </div>

            <div class="col-md-4 pa0 pt3">
              <div class="data-side-content mb0">

                @if(!empty($data['save']))
                  <div class="price-saving-flag flag-lg mt2">-{{$data['save']}}</div>
                @endif

                <div class="price-section mt2">
                  <span class="price">{{$data['price']}}</span>
                  @if(!empty($data['original_price']))
                  <span class="original-price">{{$data['original_price']}}</span>
                  @endif
                </div>

                <div class="mt3 mb3 pb3 bb b--moon-gray">
                  @if(!empty($data['place_location']))
                    <ol class="c-breadcrumb mb0 pa0">
                      <li class="c-breadcrumb-item">
                        <a href="javascript:void(0);" class="clearfix">
                          <i class="fas fa-map-marked mr2"></i>
                          <span>{{$data['place_location']}}</span>
                        </a>
                      </li>
                    </ol>
                  @endif

                  @if(!empty($data['category']))
                  <ol class="c-breadcrumb mb0 pa0">
                    <li class="c-breadcrumb-item">
                      <a href="javascript:void(0);" class="clearfix">
                        <i class="fas fa-ticket-alt mr2"></i>
                        <span>{{$data['category']}}</span>
                      </a>
                    </li>
                  </ol>
                  @endif
                </div>

                <div class="mb4">

                  <div class="owner-section pa3 clearfix">

                    @if($data['hasShop'])

                    <h5 class="db dn-ns"><small>ร้านขายสินค้า</small></h5>
                    <div class="clearfix">
                      <div class="avatar-frame fl">       
                        <div class="avatar">
                          <img src="/shop/{{$data['owner']['slug']}}/avatar?d=1">
                        </div>
                      </div>
                      <div class="online-name fl">
                        <div><a href="/shop/page/{{$data['owner']['slug']}}" class="text-overflow-ellipsis">{{$data['owner']['name']}}</a></div>
                        <div class="relative">
                          <div class="online_status_indicator_{{$data['created_by']}} online-status-indicator @if($data['owner']['online']) is-online @endif"></div>
                          @if($data['owner']['online'])
                            <small class="dark-gray ml4">ออนไลน์</small>
                          @else
                            <small class="dark-gray ml4">ออนไลน์ล่าสุด {{$data['owner']['last_active']}}</small>
                          @endif
                        </div>
                      </div>
                    </div>

                    @else

                    <h5 class="db dn-ns"><small>ผู้ขายสินค้า</small></h5>
                    <div class="clearfix">
                      <div class="avatar-frame fl">       
                        <div class="avatar">
                          <img src="/avatar/{{$data['created_by']}}?d=1">
                        </div>
                      </div>
                      <div class="online-name fl">
                        <div><a href="/profile/{{$data['created_by']}}/item" class="text-overflow-ellipsis">{{$data['owner']['name']}}</a></div>
                        <div class="relative">
                          <div class="online_status_indicator_{{$data['created_by']}} online-status-indicator @if($data['owner']['online']) is-online @endif"></div>
                          @if($data['owner']['online'])
                            <small class="dark-gray ml4">ออนไลน์</small>
                          @else
                            <small class="dark-gray ml4">ออนไลน์ล่าสุด {{$data['owner']['last_active']}}</small>
                          @endif
                        </div>
                      </div>
                    </div>

                    @endif

                  </div>

                  @if(Auth::guest() || (Auth::check() && (Auth::user()->id != $data['created_by'])))  

                    <div class="mt3 db dn-ns">
                      <a href="#" data-chat-box="1" data-chat-data="m|Item|{{$data['id']}}" class="btn btn-primary btn-block">
                        <i class="fas fa-comments mr2"></i>คุยกับผู้ขาย
                      </a>
                    </div>

                  @endif

                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>

  <div class="related-item-wrapper bg-near-white pv4">
    
    <div class="c-detail-container pa4">

      <h5 class="tc near-black mb4">รายการที่มาจากผู้ขายเดียวกัน</h5>

      @if(!empty($relatedWithShop))
        <div class="related-item-box owl-carousel owl-theme">
        @foreach($relatedWithShop as $value)
          @include('shared.item-card-minimal')
        @endforeach
        </div>
      @else
        <h6 class="tc">ไม่มีสินค้าจากผู้ขายเดียวกัน</h6>
      @endif

    </div>

  </div>

  <div class="pa0">
    <div class="data-nav-panel-outer">
      <div class="data-nav-panel">
        <div class="data-nav-panel-inner tc">
          <div class="c-detail-container z-5">
          รายการที่เกี่ยวข้องหรือคล้ายกัน
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="c-detail-container mt6 mb7 pa4">

      @if(!empty($relatedWithCategory))
        <div class="related-item-box owl-carousel owl-theme">
        @foreach($relatedWithCategory as $value)
          @include('shared.item-card-minimal')
        @endforeach
        </div>
      @else
        <h6 class="white tc">ไม่มีสินค้าสินค้าที่คล้ายกัน</h6>
      @endif
  </div>

  @if(config('app.module_enabled.cart'))
  <div id="shopping_btn" class="c-floating-btn shopping-btn btn-hide-bottom btn-hide-when-input">
    <a href="#" class="floating-icon-content" data-toggle="modal" data-c-modal-target="#modal_shopping">
      <i class="fas fa-cart-plus mr2"></i><span>เพิ่มลงตระกร้า</span>
    </a>
  </div>
  @endif

  <div id="modal_shopping" class="c-modal">
    <a class="close"></a>
    <div class="c-modal-inner">

      <a class="modal-close">
        <span aria-hidden="true">&times;</span>
      </a>

      <div>สั่งซื้อ</div>
      <h4 class="item-title f4 f3-ns mb-3 mb4-ns">{{$data['title']}}</h4>

      @if(!empty($data['save']))
        <div class="price-saving-flag flag-lg mt2">-{{$data['save']}}</div>
      @endif

      <div class="price-section mt2">
        <span class="price">{{$data['price']}}</span>
        @if(!empty($data['original_price']))
        <span class="original-price">{{$data['original_price']}}</span>
        @endif
      </div>

      <div>
        * ราคายังไม่รวมภาษีมูลเพิ่ม
      </div>

      <hr>

      <div class="alert alert-info" role="alert">
        มีสินค้าทั้งหมด {{$data['quantity']}} ชิ้น
      </div>

      <div class="form-group">
        <label class="form-control-label required">จำนวนที่ต้องการสั่งซื้อ</label>
        <input id="input_qty" class="form-control" autocomplete="off" placeholder="จำนวนที่ต้องการสั่งซื้อ" name="qty" type="text" value="1">
      </div>

      <hr>

      <div class="tc">
        <button id="add_to_cart_btn" class="btn c-btn c-btn-bg">เพิ่มลงตระกร้า</button>
      </div>
      
    </div>
  </div>

  @if(!empty($_sharingTitle))
  <!-- modal socail -->
  <div id="modal_share" class="c-modal">
    <a class="close"></a>
    <div class="c-modal-inner c-modal-bg c-modal-border-radius lazy" data-src="{{ $__banner__sm__ }}">

      <a class="modal-close">
        <span aria-hidden="true">&times;</span>
      </a>

      <h4 class="white tc">{{ $_sharingTitle }}</h4>

      <div class="c-md-container modal-share">
        <div class="row">
          <div class="col-12 col-md-4 tc">
            <a class="btn btn-facebook btn-share f2 f1-ns" href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-facebook-f"></i>
            </a>      
          </div>
          <div class="col-12 col-md-4 tc">
            <a class="btn btn-twitter btn-share f2 f1-ns" href="https://twitter.com/intent/tweet?url={{Request::fullUrl()}}&amp;text={{$data['title']}}" target="_blank">
              <i class="fab fa-twitter"></i>
            </a>      
          </div>
          <div class="col-12 col-md-4 tc">
            <a class="btn btn-googleplus btn-share f2 f1-ns" href="https://plus.google.com/share?url={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-google-plus-g"></i>
            </a>              
          </div>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function(){

      setTimeout(function(){
        const _modal = new ModalDialog();
        _modal.show('#modal_share');
      },3000);
      
    });
  </script>
  @endif

@if(Auth::check() && (Auth::user()->id == $data['created_by']))
  @include('shared.item-cancel-modal')
@endif

<script type="text/javascript">

  class CartItemAdding {

    constructor(token,item) {
      this.token = token;
      this.item = item;
    }

    init() {
      this.bind();
    }

    bind() {
      let _this = this;
      
      $('#add_to_cart_btn').on('click', function(){

        let qty = Math.floor($('#input_qty').val());

        if(!isNaN(qty) && (qty > 0)) {
          const cart = new Cart(_this.token);
          cart.addItemToCart(_this.item,qty);

          // $('#input_qty').val('1');
        }else {
          snackbar.setTitle('จำนวนสินค้าไม่ถูกต้อง');
          snackbar.display();          
        }

      });

      // $('#input_qty').on('keypress',function(e){

      //   if (e.keyCode == 13) {
      //     let qty = Math.floor($(this).val());

      //     if(!isNaN(qty) && (qty > 0)) {
      //       const cart = new Cart(_this.token);
      //       cart.addItemToCart(_this.item,qty);

      //       // $(this).val('1');
      //     }else {
      //       snackbar.setTitle('จำนวนสินค้าไม่ถูกต้อง');
      //       snackbar.display();          
      //     }
      //   }

      // });
    }

  }

  class ItemDetail {

    constructor(id) {
      this.id = id;
      this.navFixed = false;
      this.navPostitonTop;
      this.sideContentFixed = false;
      this.sideContentPostitonTop;
      this.sideContentDiff;
      this.detailHeight;
      this.sideContentHeight;
      // this.io;
    }

    init() {

      let _this = this;

      // this.io = new IO();
      // this.io.join('item.'+this.id+'.'+this.io.token);

      setTimeout(function(){
        $('#shopping_btn').removeClass('btn-hide-bottom');
      },700);  

      setTimeout(function(){

        $('.data-side-content').css('width',$('.data-side-content').width() + 30);

        _this.detailHeight = $('.item-content-section').height();
        _this.sideContentHeight = $('.data-side-content').height();

        _this.navPostitonTop = $('.data-nav-panel').offset().top - 60;
        _this.sideContentPostitonTop = $('.data-side-content').offset().top - 120;
        // _this.sideContentDiff = _this.navPostitonTop - _this.sideContentHeight - ($('.related-item-wrapper').height() + 60) - 80;

        _this.sideContentDiff = $('.related-item-wrapper').offset().top - _this.sideContentHeight - 140;

        _this.bind();
      },2000);

      // this.socketEvent();
    }

    bind() {

      let _this = this;

      $('#display_contact_btn').on('click',function(){

        $(this).addClass('dn');
        $('#loading_contact').addClass('show');

        // _this.io.socket.emit('get-item-detail-contact', {
        //   id: this.id
        // })

        let request = $.ajax({
          url: "/get-item-contact/"+_this.id,
          type: "GET",
          dataType: 'json'
        });

        request.done(function (response, textStatus, jqXHR){

          $('#display_contact').html(response.contact);

          _this.detailHeight = $('.item-content-section').height();
          _this.navPostitonTop = $('.data-nav-panel').offset().top - 60;
        });

        request.fail(function (jqXHR, textStatus, errorThrown){
          console.error(
              "The following error occurred: "+
              textStatus, errorThrown
          );
        });

      });

      // $('#tab_contact').on('click',function() {
      //   _this.io.socket.emit('get-item-detail-contact', {
      //     id: _this.id
      //   })
      // });

      $(window).scroll(function (event) {
        let scroll = $(window).scrollTop();

        if(scroll >= _this.navPostitonTop) {
          $('.data-nav-panel').addClass('data-nav-fixed');
          $('#shopping_btn').addClass('btn-hide-bottom');
          _this.navFixed = true;
        }else if(_this.navFixed) {
          $('.data-nav-panel').removeClass('data-nav-fixed');
          $('#shopping_btn').removeClass('btn-hide-bottom');
          _this.navFixed = false;
        }

        if(scroll >= _this.sideContentDiff) {
          $('.data-side-content').css('top',(_this.detailHeight - _this.sideContentHeight));
          $('.data-side-content').css('left','auto');
          $('.data-side-content').removeClass('detail-side-content-fixed');
        }else if(scroll >= _this.sideContentPostitonTop) {
          $('.data-side-content').css('top',120);
          $('.data-side-content').css('left',$('.data-side-content').offset().left);
          $('.data-side-content').addClass('detail-side-content-fixed');
          _this.sideContentFixed = true;
        }else if(_this.sideContentFixed) {
          $('.data-side-content').css('top','auto');
          $('.data-side-content').css('left','auto');
          $('.data-side-content').removeClass('detail-side-content-fixed');
          _this.sideContentFixed = false;
        }
      });

      $(window).on('resize', function () {
        _this.navPostitonTop = $('.data-nav-panel').offset().top - 60;
        _this.sideContentPostitonTop = $('.data-side-content').offset().top - 120;
        // _this.sideContentDiff = _this.navPostitonTop - $('.data-side-content').height() - 60;
        _this.sideContentDiff = $('.related-item-wrapper').offset().top - _this.sideContentHeight - 140;
      });

    }

    // socketEvent() {
    //   let _this = this;

    //   this.io.socket.on('get-item-detail-contact', function(res){
    //     $('#display_contact').html(res.contact);
    //   });
    // }

  }

  $(document).ready(function () {
    const itemDetail = new ItemDetail({{$data['id']}});
    itemDetail.init();

    const cartItemAdding = new CartItemAdding('{{ csrf_token() }}',{{$data['id']}});
    cartItemAdding.init();

    @if(Auth::check())

    const pageViewingHistory = new PageViewingHistory('{{ csrf_token() }}','Item',{{$data['id']}},2);
    pageViewingHistory.record();

    @endif

    TicketCountdown.init("#countdown_{{$data['id']}}",{{$data['expireDate']}});

    $('.related-item-box').owlCarousel({
      loop: true,
      margin: 15,
      nav: false,
      dots: true,
      margin: 10,
      autoplay:true,
      autoplayTimeout:4000,
      autoplayHoverPause:true,
      responsiveClass: true,
      responsive: {
        0: {
          items: 1
        },
        480: {
          items: 1
        },
        768: {
          items: 3
        },
        1024: {
          items: 3
        },
        1280: {
          items: 3
        },
        1440: {
          items: 4
        },
        1920: {
          items: 4,
          // nav: true
          // margin: 20
        }
      }
    });

  });
</script>

<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": 
  [{
    "@type": "ListItem",
    "position": 1,
    "item": {
      "@id": "{{url('/')}}",
      "name": "{{$breadcrumb[count($breadcrumb)-1]['name']}}"
    }
  },
  {
    "@type": "ListItem",
    "position": 2,
    "item": {
      "@id": "{{url('/')}}/v/{{$data['slug']}}",
      "name": "{{$data['title']}}"
    }
  }]
}
</script>

@stop