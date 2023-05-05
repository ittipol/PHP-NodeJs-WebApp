@extends('shared.main')
@section('content')

<div class="fiter-panel-toggle">
  <button data-toggle="left-sidebar" data-left-sidebar-target="#left_sidebar_account_nav" type="button" class="btn btn-primary btn-block"><i class="fas fa-user"></i></button>
</div>

<div id="left_sidebar_account_nav" class="left-sidenav pt4">

	<button type="button" class="close" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
	
	<div class="container mt3">
		<div class="row">
		  <div class="col-12">
		    <div class="avatar avatar-md frame-border tc mb3">
		      <a href="/account/edit"><img src="/avatar?o=1&d=1"></a>
		    </div>
		    <div class="tc mt4">
		      <h5 class="text-overflow-ellipsis">{{ Auth::user()->name }} <a href="/account/edit"><i class="fas fa-pen-square"></i></a></h5>
		      <hr>
		      @if(config('app.module_enabled.shop'))
		      <div class="mt3">
		        @if($_has_shop)
		        <a href="/shop/page/{{$_shop['slug']}}" class="btn c-btn c-btn-bg c-bg-emphasis">
		          <i class="fas fa-hotel"></i> ร้านขายสินค้าของฉัน
		        </a>
		        @else
		        <a href="/shop/create" class="btn c-btn c-btn-bg c-bg-emphasis">
		          <i class="fas fa-hotel"></i> สร้างร้านขายสินค้า
		        </a>
		        @endif
		      </div>
		      <hr>
		      @endif
		    </div>
		  </div>
		  <div class="col-12 mb5">
		    <ul class="list-group addition-menu list-group-dark">
		      <!-- <h4 class="tc tl-ns">สินค้า</h4> -->
		      <li class="list-group-item">
		        <a href="/account/sale"><i class="fas fa-tags"></i> รายการขายของฉัน</a>
		      </li>
		      <!-- <hr> -->
		      @if(config('app.module_enabled.cart'))
		      <h4 class="tc tl-ns">การซื้อ-ขายของฉัน</h4>
		      <li class="list-group-item">
		        <a href="/client-order"><i class="fas fa-list-ul"></i> ดูรายการคำสั่งซื้อจากลูกค้า</a>
		      </li>
		      <li class="list-group-item">
		        <a href="/order"><i class="fas fa-cart-arrow-down"></i> ดูรายการสั่งซื้อสินค้าของฉัน</a>
		      </li>
		      <hr>
		      @endif
		      <!-- <li class="list-group-item">
		        <a href="/account/coin"><i class="fas fa-coins"></i> เหรียญ</a>
		      </li> -->
		      <li class="list-group-item">
		        <a href="/account/blocking"><i class="fas fa-ban"></i> รายการที่ฉันไม่สนใจ</a>
		      </li>
		    </ul>
		  </div>
		</div>
	</div>
</div>

<div class="container-fliud mb5 mb7-ns">
	<div class="main-panel">
		@yield('account-content')
	</div>
</div>

<script type="text/javascript" src="/assets/js/left-sidebar.js"></script>
<script type="text/javascript">
  
  $(document).ready(function(){
    const leftSideBar = new LeftSideBar();
    leftSideBar.init();
  });

</script>

@stop