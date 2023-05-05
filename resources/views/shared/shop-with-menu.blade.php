@extends('shared.main')
@section('content')

<div class="fiter-panel-toggle">
  <button data-toggle="left-sidebar" data-left-sidebar-target="#left_sidebar_shop_nav" type="button" class="btn btn-primary btn-block"><i class="fas fa-hotel"></i></button>
</div>

<div id="left_sidebar_shop_nav" class="left-sidenav">

	<button type="button" class="close" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>

	<div class="owner-section pa3 mt3">
    <div class="clearfix">
      <div class="avatar-frame fl">       
        <div class="avatar bg-gray c-shadow-1">
        	@if(empty($_shop['profileImage']))
        		<img src="/assets/images/common/shop-avatar.png">
        	@else
          	<img src="{{$_shop['profileImage']}}">
          @endif
        </div>
      </div>
      <div class="online-name w-70 fl">
        <div><a href="/shop/page/{{$_shop['slug']}}" class="sidebar-primary-text db">{{$_shop['name']}}</a></div>
	        <div class="relative">
	        	<div class="online_status_indicator_{{$_shop['id']}} online-status-indicator @if($_shop['owner']['online']) is-online @endif"></div>
		        @if($_shop['owner']['online'])
		          <small class="online-status-text ml4">ออนไลน์</small>
		        @else
		          <small class="online-status-text ml4">ออนไลน์ล่าสุด {{$_shop['owner']['last_active']}}</small>
		        @endif
	        </div>
       </div>
	   </div>
	</div>
	
	<div class="container mb5">
		<div class="row">

		  <div class="col-12 mt4 mb5">
		    <ul class="list-group addition-menu list-group-dark">
		      <li class="list-group-item">
		        <a href="/shop/page/{{$_shop['slug']}}"><i class="fas fa-home"></i> หน้าหลัก</a>
		      </li>
		      <li class="list-group-item">
		        <a href="/shop/page/{{$_shop['slug']}}/item"><i class="fas fa-tags"></i> สินค้าในร้าน</a>
		      </li>
		      <li class="list-group-item">
		        <a href="/shop/page/{{$_shop['slug']}}/about"><i class="fas fa-ellipsis-h"></i> เกี่ยวกับ</a>
		      </li>
		      @if(Auth::check() && (Auth::user()->id == $_shop['created_by']))
		      	<hr class="bg-near-white">
  	        <!-- <li class="list-group-item">
  		        <a href="#">
  		          <i class="fas fa-user-tag"></i> คำสั่งซื้อสินค้า
  		        </a>
  	        </li> -->
		        <li class="list-group-item">
			        <a href="/account/sale">
			          <i class="far fa-list-alt"></i> จัดการสินค้า
			        </a>
		        </li>
		      	<li class="list-group-item">
			        <a href="/shop/page/{{$_shop['slug']}}/setting">
			          <i class="fas fa-sliders-h"></i> ตั้งค่า
			        </a>
		        </li>
		      @endif
		      @if(Auth::check() && (Auth::user()->id != $_shop['created_by']))
		      	<hr class="bg-near-white">
		      	<li class="list-group-item">
			        <a href="#" data-chat-box="1" data-chat-data="m|Shop|{{$_shop['id']}}" data-chat-close="1">
			          <i class="fas fa-comments"></i> แชท
			        </a>
		        </li>
		        <li class="list-group-item">
			        <a href="#" data-blocking="1" data-blocking-ident="Shop_{{$_shop['id']}}" data-blocked-type="Shop" data-blocked-id="{{$_shop['id']}}">
			          @if($_blocked)
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
		        </li>
		       @endif
		    </ul>
		  </div>
		</div>
	</div>

</div>

<div class="menu-wave-animation-wrapper">
  <div class="wave"></div>
  <div class="wave"></div>
  <div class="wave"></div>
</div>

<div id="model_upload_page_profile_image" class="c-modal">
  <div class="c-modal-inner w-100 h-100">
    <h4 class="item-title f4 f3-ns mb3 mb4-ns">รูปภาพโปรไฟล์</h4>
    <div class="page-profile-image-panel">
      <div class="page-profile-image-view">
        <img class="page-profile-image-edit" src="">
      </div>

      <img class="page-profile-image-edit o-30" src="">

      <div class="f4 tc mt3">ลากเพื่อปรับตำแหน่ง</div>

      <div class="clearfix tc mt3 pa0">
        <a id="page_profile_image_save_btn" class="c-btn c-btn c-btn__secondary fl w-50 ma0 br0 db" href="javascript:void(0);">บันทึก</a>
        <a id="page_profile_image_cancel_btn" class="c-btn fl  w-50 ma0 br0 db" href="javascript:void(0);">ยกเลิก</a>
      </div>
    </div>
  </div>
</div>

<div id="modal_shop_cover" class="c-modal">

	<a class="close"></a>
	<div class="c-modal-inner fullscreen">

	  <div class="global-overlay show"></div>

	  <div class="c-page-container page-wrapper">
	    <div class="page-header top">

	      <div id="page_cover_wrapper" class="page-cover-wrapper">
	        <img id="c_page_cover" class="page-cover dn cursor-move">
	      </div>

	      <div class="page-cover-message f4">ลากเพื่อปรับตำแหน่ง</div>

	      <div class="page-cover-btn clearfix tc pa0">
	        <a id="page_cover_save_btn" class="c-btn c-btn__secondary fl ma0 br0 db" href="javascript:void(0);">บันทึก</a>
	        <a id="page_cover_cancel_btn" class="c-btn fl ma0 br0 db" href="javascript:void(0);">ยกเลิก</a>
	    	</div>

	    </div>

	  </div>

	</div>
</div>

<div class="container-fliud mb5 mb7-ns">
	<div class="main-panel">
		@yield('shop-content')
	</div>
</div>

<script type="text/javascript" src="/assets/js/user-blocking.js"></script>
<script type="text/javascript" src="/assets/js/page-profile-image.js"></script>
<script type="text/javascript" src="/assets/js/page-cover.js"></script>
<script type="text/javascript" src="/assets/js/left-sidebar.js"></script>
<script type="text/javascript">
  
  $(document).ready(function(){
    const leftSideBar = new LeftSideBar();
    leftSideBar.init();

    @if(Auth::check() && (Auth::user()->id != $_shop['created_by']))
      const userBlocking = new UserBlocking();
      userBlocking.init();
    @endif

    @if(Auth::check() && (Auth::user()->id == $_shop['created_by']))

      const pageProfileImage = new PageProfileImage({{$_shop['id']}},'{{ csrf_token() }}');
      pageProfileImage.init();

      const pageCover = new PageCover({{$_shop['id']}},'{{ csrf_token() }}');
      pageCover.init();

    @endif
  });

</script>

@stop