<div class="col-12 col-md-6 col-lg-4 col-xl-3">
  <div class="c-card c-card--to-edge {{$value['theme']}} mb4">
    <div class="c-card--inner">

      <a href="/ticket/view/{{$value['id']}}" data-detail-box="1" data-detail-id="{{$value['id']}}" class="c-card__portrait-layout-preview">
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

      <div class="c-card__primary-title pb3">
        <h2 class="title">
          <a href="/ticket/view/{{$value['id']}}" data-detail-box="1" data-detail-id="{{$value['id']}}">{{$value['title']}}</a>
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

<script type="text/javascript">
  $(document).ready(function(){
    TicketCountdown.init("#countdown_{{$value['id']}}",{{$value['expireDate']}});
  });
</script>