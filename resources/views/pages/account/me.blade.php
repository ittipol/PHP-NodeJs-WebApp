@extends('shared.main')
@section('content')

<div class="c-list-container mt4 mb6">
  <div class="account-header-secondary">
    <div class="account-profile-image shadow-1">
      <img src="{{$avatar}}">
    </div>

    <div class="account-header-content">
      <h3 class="pv3">
        <a href="/profile/{{$data['id']}}" class="black no-underline">{{$data['name']}}</a>
      </h3>

      <div>
        <a href="/account/edit" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
          <i class="far fa-edit"></i>แก้ไขข้อมูลของฉัน
        </a>
        <a href="/account/sale" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
          <i class="fas fa-tags"></i>สินค้าและรายการขาย
        </a>
        <a href="/account/blocking" class="btn btn-secondary w-100 w-auto-ns mt2 mt0-ns">
          <i class="fas fa-ban"></i>รายการที่ไม่สนใจ
        </a>
      </div>

    </div>
  </div>

  <hr>

  @if($hasShop)

    <div class="banner banner-header bg tc mt4 c-border-radius-1">
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

        <div class="page-profile-image-frame">
          <div class="page-profile-image">
            <img @if(!empty($shop['profileImage'])) src="{{$shop['profileImage']}}" @endif>
          </div>
        </div>

        <div class="page-header-content">
          <div class="clearfix">
            <div class="w-100 w-60-ns fl pv3 ph2 bb bn-ns b--moon-gray">
              <h3 class="ma0">
                <a href="/shop/page/{{$shop['slug']}}" class="black no-underline">{{$shop['name']}}</a>
              </h3>
            </div>
            <div class="w-100 w-40-ns fl tr pv2">
              <a class="btn btn-facebook btn-share" href="https://www.facebook.com/sharer/sharer.php?u={{URL::to('/')}}/shop/page/{{$shop['slug']}}/item" target="_blank">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a class="btn btn-twitter btn-share" href="https://twitter.com/intent/tweet?url={{URL::to('/')}}/shop/page/{{$shop['slug']}}/item&amp;text={{$shop['name']}}" target="_blank">
                <i class="fab fa-twitter"></i>
              </a>
              <a class="btn btn-googleplus btn-share" href="https://plus.google.com/share?url={{URL::to('/')}}/shop/page/{{$shop['slug']}}/item" target="_blank">
                <i class="fab fa-google-plus-g"></i>
              </a>
            </div>
          </div>
        </div>

      </div>

    </div>

    <div class="tc mv4">
      <div>
        <a href="/shop/page/{{$shop['slug']}}" class="btn c-btn c-btn-bg"><i class="fas fa-hotel"></i> ร้านขายสินค้าของฉัน</a>
        <a href="/shop/page/{{$shop['slug']}}/setting" class="btn c-btn c-btn-bg">ตั้งค่า</a>
      </div>
    </div>

  @else

    <div class="tc">
      <h4>ร้านขายสินค้า</h4>
      <p>คุณยังไม่มีร้านขายสินค้า สร้างร้านขายสินค้าของคุณและขายสินค้าในนามร้านขายสินค้าหรือสนับสนุนสินค้าหรือแบรน์ของคุณได้ฟรี</p>
      <div>
        <a href="/shop/create" class="btn c-btn c-btn-bg"><i class="fas fa-hotel mr-2"></i>สร้างร้านขายสินค้า</a>
        <a href="/support/shop-creating" class="btn c-btn c-btn-bg">เรียนรู้เพิ่มเติม</a>
      </div>
    </div>

  @endif

</div>

@stop