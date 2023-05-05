
@extends('shared.shop-with-menu')
@section('shop-content')

<div class="bg-drop bg-drop-full"></div>

@include('pages.shop.layout.page-blocking-bar')

<!-- page header -->
@include('pages.shop.layout.page-header')
<!--  -->

@include('pages.shop.layout.page-menu')


@if(count($items) > 0)

<!-- <hr class="w-90 center bg-white mv3"> -->

<div class="tc pt4 ml0 ml4-ns mb4">
  <h4 class="white"><small>สินค้าที่นำเสนอ</small></h4>
  <hr class="w-60 w-90-ns center bg-white">
</div>

<div class="c-list-container c-list-container-lg ph4">
  <div class="row">

    @foreach($items as $_value)

      <?php $value = $_value->buildDataList(); ?>

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

          <ul class="list-group addition-menu">
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

<!-- <hr class="w-90 center bg-white mv3"> -->

<div class="c-list-container pa4">
  <div class="row">

    <div class="col-6 dn db-ns">
      <!-- <h4 class="ma0 white"><small>ยังมีสินค้าอีกมายมายให้คุณได้เลือกดู</small></h4> -->
    </div>

    @if(count($items) > 0)
    <div class="col-12 col-md-6">
      <div class="tc tr-ns">
        <a href="/shop/page/{{$shop['slug']}}/item" class="btn c-btn c-btn-bg c-btn-revert-color w-100 w-auto-ns">ดูสินค้าทั้งหมด</a>
      </div>
    </div>
    @endif

  </div>
</div>

@else

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5 class="white">ยังไม่มีรายการขาย</h5>
      @if(Auth::check() && (Auth::user()->id == $shop['created_by']))  
      <a href="/ticket/new" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">ขายบัตรของคุณตอนนี้</a>
      @endif
    </div>
  </div>
</div>

@endif

<!-- <div class="clearfix margin-top-200"></div> -->

@stop