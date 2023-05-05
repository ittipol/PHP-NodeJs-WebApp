@extends('shared.main')
@section('content')

<div id="blocking_alert" class="pv2 pv3-ns bg-red @if($blocked) db @else dn @endif">
  <div class="c-main-container">
    <div class="alert ma0 pv0 tc white">
      <h5 class="alert-heading mb0">@if(empty($shop)) คุณไม่สนใจรายการขายผู้ใช้รายนี้ @else คุณไม่สนใจรายการขายจากร้านนี้ @endif</h5>
    </div>
  </div>
</div>

<div class="c-list-container mv4">
  <div class="account-header-secondary">
    <div class="account-profile-image">
      <img src="{{$user['avatar']}}">
    </div>

    <div class="account-header-content">
      <h3 class="pv3">
        <a href="/profile/{{$user['id']}}" class="black no-underline">{{$user['name']}}</a>
        <div class="online_status_indicator_{{$user['id']}} online-status-indicator dib @if($user['online']) is-online @endif"></div>
      </h3>

      <div>

        @if(Auth::check())

          @if(Auth::user()->id == $user['id'])
            <a href="/account/edit" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
              <i class="far fa-edit"></i>แก้ไขช้อมูลของฉัน
            </a>
            <a href="/account/sale" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
              <i class="fas fa-tags"></i>รายการขาย
            </a>
          @else
            <a href="/profile/{{$user['id']}}/item" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
              <i class="fas fa-tags"></i>รายการขาย
            </a>
            <a href="#" data-blocking="1" data-blocking-ident="User_{{$user['id']}}" data-blocked-type="User" data-blocked-id="{{$user['id']}}" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
              @if($blocked)
              <span class="user-blocking-icon">
                <i class="fas fa-stop"></i>
              </span>
              <span class="user-blocking-label">
                ยกเลิกไม่สนใจรายการขายผู้ใช้รายนี้
              </span>
              @else
              <span class="user-blocking-icon">
                <i class="fas fa-ban"></i>
              </span>
              <span class="user-blocking-label">
                ไม่สนใจรายการขายผู้ใช้รายนี้
              </span>
              @endif
            </a>
            @if(!empty($user['contact']))
            <div class="break-word">
              <h5 class="bb b--moon-gray pb2">ข้อมูลการติดต่อ</h5>
              {!!$user['contact']!!}
            </div>
            @endif
          @endif

        @elseif(!empty($shop))
          <a href="/shop/page/{{$shop['slug']}}" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
            <i class="fas fa-home"></i>ร้านขายสินค้า
          </a>
        @else
          <a href="/profile/{{$user['id']}}/item" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
            <i class="fas fa-tags"></i>รายการขาย
          </a>
        @endif

      </div>

    </div>
  </div>
</div>

@if(empty($shop))

  @if(count($items) > 0)

  <div class="c-list-container c-list-container-lg pa4 bt b--silver">

    <div class="row">

      @foreach($items as $_value)

        <?php $value = $_value->buildDataList2(); ?>

        @include('shared.item-card')

        <div id="model_additional_menu_{{$value['id']}}" class="c-modal">
          <a class="close"></a>
          <div class="c-modal-addition-menu-inner c-addition-menu-modal-sheet">

            <a class="modal-close">
              <span aria-hidden="true">&times;</span>
            </a>

            <div class="c-addition-menu-modal-sheet-header"></div>

            <div class="c-addition-menu-modal-sheet-avatar">
              @if($value['hasShop'])
              <a class="avatar-frame" href="/shop/page/{{$value['owner']['slug']}}">
                <img src="/shop/{{$value['owner']['slug']}}/avatar?d=1">
              </a>
              @else
              <a class="avatar-frame" href="/profile/{{$value['created_by']}}/item">
                <img src="/avatar/{{$value['created_by']}}?d=1">
              </a>
              @endif
            </div>

            <h5 class="pr4">{{$value['title']}}</h5>

            <ul class="list-group addition-menu list-group-light">
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

  </div>

  <div class="c-list-container clearfix mb6">
    <div class="fr">
      <a href="/profile/{{$user['id']}}/item" class="btn c-btn c-btn-bg">แสดงรายการขายทั้งหมด</a>
    </div>
  </div>

  @include('shared.modal.detail-box')

  @else

  <div class="c-list-container mv7">
    <div class="message-panel tc">
      <div class="center w-90 w-100-ns">
        <h5>ยังไม่มีรายการขาย</h5>
        <a href="/ticket/new" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">ขายบัตรของคุณตอนนี้</a>
      </div>
    </div>
  </div>

  @endif

@else

  <div class="fixed-bg-white bg-user-page"></div>
  <div class="bg-drop"></div>

  <!-- <div class="bb b--moon-gray"></div> -->

  <div class="banner banner-header tc">
    @if(!empty($shop['cover']))
    <div class="banner-bg" style="background-image: url('{{$shop['cover']}}');"></div>
    <img src="{{$shop['cover']}}">
    @else
    <div class="banner-bg"></div>
    <img src="/assets/images/common/cover.png">
    @endif
  </div>

  <div class="c-page-container page-wrapper page-item-style">

    <div class="page-header-secondary">

      <div class="page-profile-image">
        <img @if(!empty($shop['profileImage'])) src="{{$shop['profileImage']}}" @endif>
      </div>

      <div class="page-header-content">
        <div class="clearfix">
          <div class="w-100 w-60-ns fl pv3 ph2 bb bn-ns b--moon-gray">
            <h3 class="ma0">
              <a href="/shop/page/{{$shop['slug']}}" class="black no-underline">{{$shop['name']}}</a>
            </h3>
          </div>
          <div class="w-100 w-40-ns fl tr pv2">
            <!-- <small><strong>แชร์</strong></small> -->
            <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{Request::fullUrl()}}&amp;text={{$shop['name']}}" target="_blank">
              <i class="fab fa-twitter"></i>
            </a>
            <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{Request::fullUrl()}}" target="_blank">
              <i class="fab fa-google-plus-g"></i>
            </a>
          </div>
        </div>
      </div>

      <div class="tl tr-ns">
        @if(Auth::check() && (Auth::user()->id != $shop['created_by']))
          <a href="#" data-chat-box="1" data-chat-data="m|Shop|{{$shop['id']}}" data-chat-close="1" class="btn btn-secondary">
            <i class="fas fa-comments"></i>แชท
          </a>
          <a href="#" data-blocking="1" data-blocking-ident="Shop_{{$shop['id']}}" data-blocked-type="Shop" data-blocked-id="{{$shop['id']}}" class="btn btn-secondary">
            @if($blocked)
            <span class="user-blocking-icon">
              <i class="fas fa-stop"></i>
            </span>
            <span class="user-blocking-label">
              ยกเลิกไม่สนใจรายการขายจากร้านนี้
            </span>
            @else
            <span class="user-blocking-icon">
              <i class="fas fa-ban"></i>
            </span>
            <span class="user-blocking-label">
              ไม่สนใจรายการขายจากร้านนี้
            </span>
            @endif
          </a>
        @endif
      </div>

    </div>

    <div class="pv2 pv4-ns bg-blue mb6">
      <div class="c-main-container">
        <div class="alert ma0 pv0 white tc">
          <p class="ma0">รายการขายของผู้ใช้รายนี้จะแสดงอยู่บนร้านขายสินค้า <a href="/shop/page/{{$shop['slug']}}/item" class="navy">แสดงร้านขายสินค้า</a></p>
        </div>
      </div>
    </div>

  </div>

@endif

<script type="text/javascript" src="/assets/js/user-blocking.js"></script>

@if(count($items) > 0)
<script type="text/javascript" src="/assets/js/detail-box.js"></script>
@endif

<script type="text/javascript">

  $(document).ready(function(){

    @if(Auth::check() && (Auth::user()->id != $user['id']))
      const userBlocking = new UserBlocking();
      userBlocking.init();
    @endif

  });

</script>

@stop