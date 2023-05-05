<div class="col-12 col-md-6 col-lg-4 col-xl-3">
  <div class="c-card c-card--to-edge {{$value['theme']}} mb4">
    <div class="c-card--inner">

      <!-- <div class="c-card__badge">
        @if($value['pullPost']['allowed'])
        <div class="card-badge"><i class="fa fa-retweet"></i></div>
        @endif
      </div> -->

      <a class="c-card__addition-menu c-card__addition-menu-bg" href="#" data-toggle="modal" data-c-modal-target="#model_additional_menu_{{$value['id']}}">
        <i class="fas fa-ellipsis-v"></i>
      </a>

      <a href="/ticket/view/{{$value['id']}}" data-detail-box="1" data-detail-id="{{$value['id']}}" class="c-card__portrait-layout-preview">
        @if(!empty($value['image']['_preview_url']))
          <div class="c-card__image_preview_bg lazy" data-src="{{$value['image']['_preview_url']}}"></div>
        @else
          <div class="bg-cover-default-color h-100">
            <i class="no-img-icon fas fa-image f1 white"></i>
          </div>
        @endif
      </a>

      <div class="c-card__primary-title pb3">
        <div class="pl-3">
          <a><small><i class="fas fa-ticket-alt color-orange mr1"></i></small>{{$value['category']['name']}}</a>
        </div>
        <h2 class="title">
          <a href="/ticket/view/{{$value['id']}}" data-detail-box="1" data-detail-id="{{$value['id']}}">{{$value['title']}}</a>
        </h2>
      </div>

      <div class="c-card__price clearfix">
        <div class="c-card__price-content fr clearfix">
          @if(!empty($value['save']))
          <div class="fl discount-pct">-{{$value['save']}}</div>
          @endif
          <div class="fl price-box">
            @if(!empty($value['original_price']))
            <div class="discount-original-price">{{$value['original_price']}}</div>
            @endif
            <div class="final-price">{{$value['price']}}</div>
          </div>
        </div>
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

    <h5 class="pr4">{{$value['title']}}</h5>

    <ul class="list-group addition-menu">
      <li class="list-group-item">
        <a href="/ticket/edit/{{$value['id']}}"><i class="far fa-edit"></i>แก้ไขรายการขาย</a>
      </li>
      <li class="list-group-item">
        <a href="javascript:void(0);" data-t-id="{{$value['id']}}" data-t-title="{{$value['title']}}" data-t-cancel-modal="1"><i class="fas fa-times"></i>ปิดรายการขาย</a>
      </li>
      <li class="list-group-item">
        @if($value['pullPost']['allowed'])
        <div>
          <a class="btn btn-secondary w-100 db" href="/ticket/extend/{{$value['id']}}"><i class="fa fa-retweet"></i> เลื่อนรายการขายขึ้นสู่ตำแหน่งบน</a>
        </div>
        @else
        <div class="c-card__notice db">
          <h6><i class="fa fa-retweet"></i>เลื่อนรายการขายได้ในอีก <strong>{{$value['pullPost']['daysLeft']}}</strong></h6>
        </div>
        @endif
      </li>
      <li class="list-group-item">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{Request::fullUrl()}}" target="_blank"><i class="fab fa-facebook-f social-color"></i>แชร์ Facebook</a>
      </li>
      <li class="list-group-item">
        <a href="https://twitter.com/intent/tweet?url={{Request::fullUrl()}}&amp;text={{$value['title']}}"><i class="fab fa-twitter social-color"></i></i>แชร์ Twitter</a>
      </li>
      <li class="list-group-item">
        <a href="https://plus.google.com/share?url={{Request::fullUrl()}}"><i class="fab fa-google-plus-g social-color"></i>แชร์ Google+</a>
      </li>
    </ul>

  </div>
</div>