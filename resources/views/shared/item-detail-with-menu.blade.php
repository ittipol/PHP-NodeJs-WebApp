@extends('shared.main')
@section('content')

<div class="fiter-panel-toggle">
  <button data-toggle="left-sidebar" data-left-sidebar-target="#left_sidebar_item_info" type="button" class="btn btn-primary btn-block"><i class="fas fa-user-tag"></i></button>
</div>

<div id="left_sidebar_item_info" class="left-sidenav pt-4">

	<button type="button" class="close" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
	
	<div class="c-sidebar-container mt3">
		<div class="row">
		  <div class="col-12">
		  	<div class="avatar avatar-md frame-border tc mb3">
		  		@if($data['hasShop'])
		  	  <img src="/shop/{{$data['owner']['slug']}}/avatar?d=1">
		  	  @else
		  	  <img src="/avatar/{{$data['created_by']}}?d=1">
		  	  @endif
		  	</div>

		  	<div class="mt4 tc">
		  		@if($data['hasShop'])
		  			<!-- <small><strong>ร้านขายสินค้า</strong></small>	 -->
		  		  <h5 class="text-overflow-ellipsis">
		  		  	<a href="/shop/page/{{$data['owner']['slug']}}" class="text-overflow-ellipsis">{{$data['owner']['name']}}</a>
		  		  </h5>
	  		  @else
	  		  	<!-- <small><strong>ผู้ขายสินค้า</strong></small> -->
	  		    <h5 class="text-overflow-ellipsis">
	  		    	<a href="/profile/{{$data['created_by']}}/item" class="text-overflow-ellipsis">{{$data['owner']['name']}}</a>
	  		    </h5>
	  		  @endif

    		  <div class="online-name">
    		  	<div class="relative">
  	  		    <div class="online_status_indicator_{{$data['created_by']}} online-status-indicator @if($data['owner']['online']) is-online @endif dib"></div>
  	  		    @if($data['owner']['online'])
  	  		      <small class="online-status-text ml3">ออนไลน์</small>
  	  		    @else
  	  		      <small class="online-status-text ml3">ออนไลน์ล่าสุด {{$data['owner']['last_active']}}</small>
  	  		    @endif
    		    </div>
    		  </div>

		  	</div>

		  	<hr>

		  </div>

		  <div class="col-12">
	
		  	<div class="pa2">
		  		@if(Auth::guest() || (Auth::check() && (Auth::user()->id != $data['created_by'])))

		  		  <div class="mt3">
		  		    <a href="#" data-chat-box="1" data-chat-data="m|Item|{{$data['id']}}" class="btn btn-primary btn-block">
		  		      <i class="fas fa-comments mr-2"></i>คุยกับผู้ขาย
		  		    </a>

		  		    <hr>

		  		    @if($data['hasShop'])
		  		    <a href="/shop/page/{{$data['owner']['slug']}}" class="c-btn c-btn-bg btn-block tc">
		  		      <i class="fas fa-tag mr-2"></i>ดูสินค้าทั้งหมดในร้านนี้
		  		    </a>
		  		    @else
		  		    <a href="/profile/{{$data['created_by']}}/item" class="c-btn c-btn-bg btn-block tc">
		  		      <i class="fas fa-tag mr-2"></i>ดูสินค้าทั้งหมดของผู้ขายรายนี้
		  		    </a>
		  		    @endif
		  		  </div>

		  		@else

		  		  <div class="mt3">
		  		    <div class="clearfix">
		  		      <div class="w-50 fl">
		  		        <a href="/ticket/edit/{{$data['id']}}" class="btn btn-primary btn-block"><i class="far fa-edit"></i>แก้ไข</a>
		  		      </div>
		  		      <div class="w-50 fl">
		  		        <a href="javascript:void(0);" data-t-id="{{$data['id']}}" data-t-title="{{$data['title']}}" data-t-cancel-modal="1" class="btn btn-secondary btn-block br0"><i class="fas fa-times"></i>ยกเลิก</a>
		  		      </div>
		  		    </div>

		  		    <small class="db pt2">ปิดประกาศของคุณเมื่อ <strong>ขายสินค้านี้แล้ว</strong> หรือหากต้องการ <strong>ยกเลิกรายการ</strong></small>
		  		  </div>

		  		  <hr>

		  		  <div class="w-100">

		  		    @if($data['pullPost']['allowed'])
		  		    <div class="tc pa0">
		  		      <a class="c-btn c-btn-bg w-100 ma0 br0 db" href="/ticket/pull/{{$data['id']}}"><i class="fa fa-retweet"></i> เลื่อนประกาศขึ้นสู่ตำแหน่งบน</a>
		  		    </div>
		  		    @else
		  		    <div class="ma0 f6 f5-ns">
		  		      ยังไม่สามารถเลื่อนประกาศได้ในตอนนี้ จะสามารถเลื่อนประกาศขึ้นสู่ตำแหน่งบนได้ในอีก <strong>{{$data['pullPost']['daysLeft']}}</strong>
		  		    </div>
		  		    <hr>
		  		    @endif

		  		    <small class="db mb4 pt2">หลังจากเมื่อคุณได้เลื่อนตำแหน่งประกาศแล้ว จะสามารถเลื่อนประกาศในครั้งถัดไปได้เมื่อครบกำหนดทุก 3 วัน</small>
		  		  </div>

		  		@endif
		  	</div>

		  </div>
		</div>
	</div>
</div>

<div class="menu-wave-animation-wrapper">
  <div class="wave"></div>
  <div class="wave"></div>
  <div class="wave"></div>
</div>

<div class="container-fliud mb5 mb7-ns">
	<div class="main-panel">
		@yield('item-content')
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