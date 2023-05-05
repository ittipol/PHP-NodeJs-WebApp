@extends('shared.account-with-menu')
@section('account-content')

	<div class="c-form-container mt4">
	  <div class="mb4 mb5-ns">
	    <h4>ชำระสินค้า</h4>
	    <!-- <p></p> -->

      <div class="order-info-box">

      	<h5><small>จำนวนราคาสินค้าที่ต้องชำระเงิน</small></h5>
      	<h4>{{ $order['total'] }}</h4>

      	<hr>

    	  <div class="mt-4">
    	  	@foreach($order['summary'] as $summary)
    	  	<div class="cart-summary-row">
    	  	  <div class="cart-summary-item {{$summary['class']}}">
    	  	    <div class="clearfix">
    	  	      <div class="cart-summart-value-title fl w-50">
    	  	        {{$summary['title']}}
    	  	      </div>
    	  	      <div class="fl w-50">
    	  	        <div class="cart-summart-value tr">{{$summary['value']}}</div>
    	  	      </div>
    	  	    </div>
    	  	  </div>
    	  	</div>
    	  	@endforeach
    	  </div>
      </div>
	  </div>
	</div>

	{{Form::open(['id' => 'payment_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

	  <div class="c-form-container">
	    @include('shared.form_error')
	  </div>

	  <div class="bb b--moon-gray mb4">
	    <div class="c-form-container">
	      <h5 class="mb3">วิธีการชำระเงิน</h5>
	    </div>
	  </div>

	  <div class="c-form-container mb4">
	    <div class="row">
	      <div class="col-12">
	        <!-- <div class="mb3">
	          การคลิก "เริ่มการขาย" แสดงว่าคุณยินยอมตาม<a href="#" data-toggle="modal" data-c-modal-target="#modal_publishing_term_and_condition">ข้อกำหนดและเงื่อนไข</a>แล้ว
	        </div> -->

	        {{Form::submit('ชำระเงิน', array('class' => 'btn btn-primary btn-block'))}}
	      </div>
	    </div>
	  </div>


	 {{Form::close()}}

	 <div class="clearfix margin-top-200"></div>

@stop