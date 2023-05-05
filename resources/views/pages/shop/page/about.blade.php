@extends('shared.shop-with-menu')
@section('shop-content')

<div class="bg-drop bg-drop-full"></div>

@include('pages.shop.layout.page-blocking-bar')

<!-- page header -->
@include('pages.shop.layout.page-header')
<!--  -->

@include('pages.shop.layout.page-menu')

<div class="shop-about pv4 ph3 ph5-ns">
  <div class="c-list-container">
    <h4 class="mt0 mb3 white">เกี่ยวกับ</h4>

    <div class="row">

      <div class="col-md-6">
        <div class="shop-about-box min-vh-40">
          <h5 class="bb b--light-gray pb2">คำอธิบายของร้าน</h5>
          @if(empty($shop['description']))
            <div class="mv3 tc">
              <div>ยังไม่มีคำอธิบายของร้าน</div>
              @if(Auth::check() && (Auth::user()->id == $shop['created_by']))  
              <a href="/shop/page/{{$shop['slug']}}/edit" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">เพิ่มคำอธิบายของร้าน</a>
              @endif
            </div>
          @else
            {!! $shop['description'] !!}
          @endif
        </div>
      </div>

      <div class="col-md-6">

        <div class="shop-about-box">
          <h5 class="bb b--light-gray pb2">การติดต่อ</h5>
          @if(empty($shop['contact']))
            <div class="mv3 tc">
              <div>ยังไม่มีข้อมูลการติดต่อ</div>
              @if(Auth::check() && (Auth::user()->id == $shop['created_by']))  
              <a href="/shop/page/{{$shop['slug']}}/edit" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">เพิ่มข้อมูลการติดต่อ</a>
              @endif
            </div>
          @else
            {!! $shop['contact'] !!}
          @endif
        </div>

        <div class="shop-about-box">
          <h5 class="bb b--light-gray pb2">ข้อมูลทั่วไป</h5>

          <div class="mt3">
            <h6>ประเภทสินค้าที่ขายภายในร้าน</h6>
            <div class="mt3 mb2">
              <ol class="c-breadcrumb mb0 pa0">
                @foreach($shop['categories'] as $path)
                <li class="c-breadcrumb-item">
                  <a href="javascript:void(0);" class="clearfix">
                    <i class="fas fa-ticket-alt mr2"></i>
                    <span>{{$path['name']}}</span>
                  </a>
                </li>
                @endforeach
              </ol>
            </div>
          </div>

          <hr class="bg-silver w-90 mv3 center">
          
          <div class="mt3">
            <h6>ตำแหน่งที่ตั้ง</h6>

            @if(empty($shop['locations']))
              <div class="mv3 tc">
                <div>ยังไม่มีข้อมูลตำแหน่งที่ตั้ง</div>
                @if(Auth::check() && (Auth::user()->id == $shop['created_by']))  
                <a href="/shop/page/{{$shop['slug']}}/edit" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">เพิ่มข้อมูลตำแหน่งที่ตั้ง</a>
                @endif
              </div>
            @else
              <div class="location-wrapper mt3 mb2">
                <i class="fas fa-map-marker f6 mr1 color-orange"></i>
              @foreach($shop['locations'] as $path)
                <small class="location-name">{{$path['name']}}</small>
              @endforeach
              </div>
            @endif

          </div>

        </div>

      </div>

    </div>
  </div>
</div>

@stop