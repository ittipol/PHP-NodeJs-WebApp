<div class="c-card c-card--to-edge {{$value['theme']}}">
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

    <div class="c-card__primary-title pb3">
      <h2 class="title">
        <a href="/ticket/v/{{$value['slug']}}" data-detail-box="1" data-detail-id="{{$value['id']}}">{{$value['title']}}</a>
      </h2>
    </div>

    <div class="price-section c-card__price px-2 pt-0 pb-2 tr">
      <span class="price">{{$value['price']}}</span>
      @if(!empty($value['original_price']))
      <span class="original-price">{{$value['original_price']}}</span>
      @endif
      @if(false && !empty($value['save']))
        <span class="price-saving-flag">-{{$value['save']}}</span>
      @endif
    </div>

    <div class="c-card__avatar_image">
      @if($value['hasShop'])
      <a class="avatar-frame" href="/shop/page/{{$value['owner']['slug']}}">
        <img class="lazy" data-src="/shop/{{$value['owner']['slug']}}/avatar?d=1">
      </a>
      @else
      <a class="avatar-frame" href="/profile/{{$value['created_by']}}/item">
        <img class="lazy" data-src="/avatar/{{$value['created_by']}}?d=1">
      </a>
      @endif
    </div>

  </div>
</div>