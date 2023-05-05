<div class="col-12 col-md-6 col-lg-4 col-xl-3">
  <div class="c-card c-card--to-edge mb4">
    <div class="c-card--inner">

      <a class="c-card__addition-menu c-card__addition-menu-bg" href="#" data-toggle="modal" data-c-modal-target="#model_additional_menu_{{$value['id']}}">
        <i class="fas fa-ellipsis-v"></i>
      </a>

      <a href="/shop/page/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}" class="c-card__portrait-layout-preview">
      @if(!empty($value['cover']))
        <div class="c-card__image_preview_bg" style="background-image:url({{$value['cover']}})"></div>
      @else
        <div class="bg-moon-gray h-100">
          <i class="no-img-icon fas fa-hotel f1 gray"></i>
        </div>
      @endif
      </a>

      <div class="card-shop-avatar-frame">
        <div class="avatar avatar-md frame-border tc mb3">
          <img src="/shop/{{$value['slug']}}/avatar?d=1">
        </div>
      </div>

      <div class="c-card__primary-title tc">
        <h2 class="title h-auto pb0">
          <a href="/shop/page/{{$value['slug']}}">{{$value['name']}}</a>
          <div class="online_status_indicator_{{$value['created_by']}} online-status-indicator dib @if($value['user']['online']) is-online @endif"></div>
        </h2>
        <div class="subtitle">
          @if(!empty($value['locations']))           
            <i class="fas fa-map-marker mr1 color-orange"></i>
            @foreach($value['locations'] as $path)
              <span>{{$path['name']}}</span>
            @endforeach
          @endif
        </div>
      </div>

      <div class="pa2 pt0 mb2">
        <a href="/shop/page/{{$value['slug']}}" class="btn btn-block c-btn c-btn c-btn-bg-bg"><i class="fas fa-home"></i> หน้าหลัก</a>
      </div>

    </div>
  </div>
</div>

<div id="model_additional_menu_{{$value['id']}}" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-addition-menu-inner c-addition-menu-modal-sheet">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="c-addition-menu-modal-sheet-header"></div>

    <div class="c-addition-menu-modal-sheet-avatar">
      <a class="avatar-frame" href="/shop/page/{{$value['slug']}}">
        <img src="/shop/{{$value['slug']}}/avatar?d=1">
      </a>
    </div>

    <h5 class="pr4"></i>{{$value['name']}}</h5>

    <ul class="list-group addition-menu">
      <li class="list-group-item">
        <a href="/shop/page/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}"><i class="fas fa-home"></i>หน้าหลัก</a>
      </li>
      <li class="list-group-item">
        <a href="/shop/page/{{$value['slug']}}/item" data-detail-box="1" data-detail-id="{{$value['id']}}"><i class="fas fa-tags"></i>สินค้า</a>
      </li>
      <li class="list-group-item">
        <a href="/shop/page/{{$value['slug']}}/about" data-detail-box="1" data-detail-id="{{$value['id']}}"><i class="fas fa-ellipsis-h"></i>เกี่ยวกับ</a>
      </li>
      @if(Auth::guest() || (Auth::check() && (Auth::user()->id != $value['created_by'])))
      <li class="list-group-item">
        <a href="#" data-chat-box="1" data-chat-data="m|Shop|{{$value['id']}}" data-chat-close="1"><i class="fas fa-comments"></i>แชท</a>
      </li>
      @endif
      @if(Auth::check() && (Auth::user()->id != $value['created_by']))
      <li class="list-group-item">
        <a href="#" data-blocking="1" data-blocking-ident="Shop_{{$value['id']}}" data-blocked-type="Shop" data-blocked-id="{{$value['id']}}" class="user-blocking-item">
          <div>
            <span class="avatar-frame page-profile-image">
              <img src="/shop/{{$value['slug']}}/avatar?d=1">
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
      </li>
      @endif
      <li class="list-group-item">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/shop/page/{{$value['slug']}}/item" target="_blank"><i class="fab fa-facebook-f social-color"></i>แชร์ Facebook</a>
      </li>
      <li class="list-group-item">
        <a href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/shop/page/{{$value['slug']}}/item&amp;text={{$value['name']}}"><i class="fab fa-twitter social-color"></i></i>แชร์ Twitter</a>
      </li>
      <li class="list-group-item">
        <a href="https://plus.google.com/share?url={{URL::to('/')}}/shop/page/{{$value['slug']}}/item"><i class="fab fa-google-plus-g social-color"></i>แชร์ Google+</a>
      </li>
    </ul>

  </div>
</div>