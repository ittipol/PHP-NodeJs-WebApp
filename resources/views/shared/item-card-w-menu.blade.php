<div class="c-grid__col">
  <div class="c-card c-card--to-edge {{$value['theme']}} mb4">
    <div class="c-card--inner">

      <a href="/ticket/v/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}" class="c-card__portrait-layout-preview">
        @if(!empty($value['save']))
        <div class="price-discount-pct-flag">
          -{{$value['save']}}
        </div>
        @endif

        @if(!empty($value['image']['_preview_url']))
          <div class="c-card__image_preview_bg lazy" data-src="{{$value['image']['_preview_url']}}"></div>
        @else
          <div class="bg-cover-default-color h-100">
            <i class="no-img-icon fas fa-image f1 white"></i>
          </div>
        @endif
      </a>

      <div class="c-card__primary-title">
        <h2 class="title">
          <a href="/ticket/v/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}">{{$value['title']}}</a>
        </h2>

        @if($value['date_type'] == 0)
          <div class="pa2 tc">
            <small>วันที่ใช้งาน</small>
            <div>ไม่ระบุ</div>
          </div>
        @elseif($value['date_type'] == 1)
          <div class="pa2 tc">
            <small>บัตรหมดอายุในอีก</small>
            <strong><div class="ticket-countdown" id="countdown_{{$value['id']}}">-</div></strong>
          </div>
        @elseif($value['date_type'] == 2)
          <div class="pa2 tc">
            <small>งานจะเริ่มขึ้นในอีก</small>
            <strong><div class="ticket-countdown" id="countdown_{{$value['id']}}">-</div></strong>
          </div>
        @elseif($value['date_type'] == 3)
          <div class="pa2 tc">
            <small>เริ่มเดินทางในอีก</small>
            <strong><div class="ticket-countdown" id="countdown_{{$value['id']}}">-</div></strong>
          </div>
        @endif
      </div>

      <div class="price-section c-card__price px-2 pt-0 pb-2 tc">
        <span class="price">{{$value['price']}}</span>
        @if(!empty($value['original_price']))
        <span class="original-price">{{$value['original_price']}}</span>
        @endif
        @if(!empty($value['save']))
          <span class="price-saving-flag">-{{$value['save']}}</span>
        @endif
      </div>

      
      <div class="c-card__avatar_image">
        @if($value['hasShop'])
        <a class="avatar-frame" href="/shop/page/{{$value['owner']['slug']}}">       
          <div class="avatar">
            <img class="lazy" data-src="/shop/{{$value['owner']['slug']}}/avatar?d=1">
          </div>
          <div class="online_status_indicator_{{$value['created_by']}} online-status-indicator @if($value['user']['online']) is-online @endif fixed-top-left "></div>
        </a>
        @else
        <a class="avatar-frame" href="/profile/{{$value['created_by']}}/item">      
          <div class="avatar">
            <img class="lazy" data-src="/avatar/{{$value['created_by']}}?d=1">
          </div>
          <div class="online_status_indicator_{{$value['created_by']}} online-status-indicator @if($value['user']['online']) is-online @endif fixed-top-left "></div>
        </a>
        @endif
      </div>
      

      <div class="c-card__header">
        <div class="c-card__title">
          <div class="title">
            <i class="fas fa-ticket-alt color-orange"></i> <strong>{{$value['category']['name']}}</strong>
          </div>
          <div class="subtitle"><small>ลงสินค้าเมื่อ {{$value['created_at']}}</small></div>
        </div>
        <div class="c-card__avatar ma0">
          <a href="#" data-toggle="modal" data-c-modal-target="#model_additional_menu_{{$value['id']}}" class="db icon-circle fixed-color">
            <i class="fas fa-ellipsis-v"></i>
          </a>
        </div>
        @if(Auth::guest() || (Auth::check() && (Auth::user()->id != $value['created_by'])))
          <div class="c-card__date">
            <a href="#" data-chat-box="1" data-chat-data="m|Item|{{$value['id']}}" data-chat-close="1" class="seller-chat-btn">
              <div class="online_status_indicator_{{$value['created_by']}} online-status-indicator @if($value['user']['online']) is-online @endif"></div>
              <i class="fa fa-comments" aria-hidden="true"></i>
            </a>
          </div>
        @endif
      </div>

    </div>
  </div>
</div>

<div id="model_additional_menu_{{$value['id']}}" class="c-modal {{$value['theme']}}">
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

    <ul class="list-group addition-menu">
      <li class="list-group-item">
        <a href="/ticket/v/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}"><i class="fas fa-arrow-right"></i>อ่าน</a>
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
        <a href="#" data-blocking="1" data-blocking-ident="Item_{{$value['id']}}" data-blocked-type="Item" data-blocked-id="{{$value['id']}}" class="user-blocking-item">
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
      <li class="list-group-item">
        @if($value['hasShop'])
          <a href="#" data-blocking="1" data-blocking-ident="Shop_{{$value['owner']['id']}}" data-blocked-type="Shop" data-blocked-id="{{$value['owner']['id']}}" class="user-blocking-item">
            <div>
              <span class="avatar-frame page-profile-image">
                <img src="/shop/{{$value['owner']['slug']}}/avatar?d=1">
              </span>
              @if($value['blockedUser'])
              <span class="user-blocking-label">
                ยกเลิกไม่สนใจรายการขายจากร้านนี้
              </span>
              @else
              <span class="user-blocking-label">
                ไม่สนใจรายการขายจากร้านนี้
              </span>
              @endif
            </div>
          </a>
        @else
          <a href="#" data-blocking="1" data-blocking-ident="User_{{$value['created_by']}}" data-blocked-type="User" data-blocked-id="{{$value['created_by']}}" class="user-blocking-item">
            <span class="avatar-frame">
              <img src="/avatar/{{$value['created_by']}}?d=1">
            </span>
            @if($value['blockedUser'])
            <span class="user-blocking-label">
              ยกเลิกไม่สนใจรายการขายผู้ใช้รายนี้
            </span>
            @else
            <span class="user-blocking-label">
              ไม่สนใจรายการขายผู้ใช้รายนี้
            </span>
            @endif
          </a>
        @endif
      </li>
      @endif
      <li class="list-group-item">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/ticket/v/{{$value['slug']}}" target="_blank"><i class="fab fa-facebook-f social-color"></i>แชร์ Facebook</a>
      </li>
      <li class="list-group-item">
        <a href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/ticket/v/{{$value['slug']}}&amp;text={{$value['title']}}"><i class="fab fa-twitter social-color"></i></i>แชร์ Twitter</a>
      </li>
      <li class="list-group-item">
        <a href="https://plus.google.com/share?url={{URL::to('/')}}/ticket/v/{{$value['slug']}}"><i class="fab fa-google-plus-g social-color"></i>แชร์ Google+</a>
      </li>
    </ul>

  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    TicketCountdown.init("#countdown_{{$value['id']}}",{{$value['expireDate']}});
  });
</script>
