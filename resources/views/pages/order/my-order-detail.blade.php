@extends('shared.account-with-menu')
@section('account-content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container c-list-container-lg pv2">
    <div class="row">

      <div class="col-12">
        <div class="pv3">
          <h4 class="mt1 mb2">
            คำสั่งซื้อสินค้า #{{ $data['id'] }}
          </h4>
          <a href="/order" class="btn c-btn c-btn-bg"><i class="fas fa-chevron-left"></i> กลับไปยังหน้ารายการสั่งซื้อสินค้าของฉัน</a>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="c-main-container mb6">

	<div class="order-status-box clearfix">
		
		@foreach($timelines as $timeline)
		<div class="order-status-item @if($timeline['succeeded']) step-success @endif fl">
			<div class="order-status-icon">
				<i class="{{ $timeline['icon'] }}"></i>
			</div>
			<div class="order-status-label">
				{{ $timeline['label'] }}
			</div>
			<div class="order-status-secondary-label">
				{{ $timeline['date'] }}
			</div>
		</div>
		@endforeach

		<div class="order-status-progress-line">
			<div class="order-status-progress" style="width: {{$percent}}"></div>
		</div>
	</div>

	<div class="order-info-box">
		<div>
			@if($paid)
			<h5><small>รายละเอียดการชำระเงิน</small></h5>
			<div>
				<div>จำนวนเงิน</div>
				<h4>{{ $orderPayment['amount'] }}</h4>

				<div>วิธีการชำรเงิน</div>
				<h4>{{ $orderPayment['paymentMethod'] }}</h4>

				<div>วันที่ชำระเงิน</div>
				<h4>{{ $orderPayment['paymentDate'] }}</h4>
			</div>
			@else
			<h5><small>จำนวนราคาสินค้าที่ต้องชำระเงิน</small></h5>
			<h4>{{ $data['total'] }}</h4>
			@endif

			<hr>
			
			<div class="mt-4">
				@foreach($data['summary'] as $summary)
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
			<hr>
			<div class="clearfix">
				@if($paid)
				<h4 class="green b fn fr-ns tc">
					<small><i class="fas fa-check"></i> คุณได้ชำระเงินแล้ว</small>
				</h4>
				@else
				<a href="/order/payment/{{ $data['id'] }}" id="order_payment_btn" class="btn btn-primary br0 c-btn-bg mb2 w-100 w-auto-ns fr">ชำระเงิน</a>
				@endif
			</div>
		</div>
	</div>

	<div class="order-info-box">
		<div class="row">
			<div class="col-12 col-md-6">
				<h5><small>ชื่อผู้ซื้อ</small></h5>
				<div>{{ $data['buyer_name'] }}</div>
			</div>
			<div class="col-12 col-md-6">
				<hr class="db dn-l mv3 bg-light-silver">
				<h5><small>ที่อยู่สำหรับการจัดส่ง</small></h5>
				<div>{{ $data['shipping_address'] }}</div>
			</div>	
		</div>
	</div>

	<div class="order-info-box">
		<h5><small>ผู้ขายสินค้า</small></h5>
		
		<hr>

		<div class="order-seller-w-item">		
			@foreach($sellers as $seller)

				<div class="order-seller-w-item-list">

					<div class="row">
						<div class="col-12">
							<div>จากผู้ขาย <strong>{{ $seller['name'] }}</strong></div>
							<div><span class="f2 color-green">{{ count($seller['items']) }}</span> รายการสินค้า</div>
						</div>
						<div class="col-12">
							<hr>
							@if($seller['orderShipped'])
							<div class="green b">ผู้ขายได้จัดส่งสินค้าแล้ว</div>
							<button data-toggle="modal" data-c-modal-target="#model_shipping_detail" data-seller-id="{{ $seller['id'] }}" data-order-id="{{ $data['id'] }}" class="btn btn-primary btn-sm br0 mt-3">
								<i class="fas fa-box"></i> แสดงรายละเอียดการจัดส่งสินค้า
							</button>
							@else
							<span class="red b">ยังไม่ได้จัดส่งสินค้า</span>
							@endif
						</div>
					</div>

					<div class="cart-list">
					  @foreach($seller['items'] as $item)
					    <div class="cart-card-item clearfix">
					      <div class="cart-card-item-right fl w-100">
					        <div class="cart-card-item-inner clearfix">
					          <div class="item-image-frame fl">
					            <div class="item-image" style="background-image: url('{{ $item['image']['_preview_url'] }}')"></div>
					          </div>
					          <div class="item-content fl">
					            <div class="item-primary-text">{{ $item['name'] }}</div>
					            <div class="item-secondary-text">{{ $item['subTotal'] }} (VAT 7% {{ $item['vat'] }})</div>
					            <div class="item-quantity-text">
					              <div>x {{ $item['quantity'] }}</div>
					            </div>
					          </div>
					        </div>
					      </div>
					    </div>
					  @endforeach
					</div>

					<div class="row mv3">
						<div class="col-12">
							<div class="clearfix">
								@if($seller['orderchecked'])
								<h4 class="green b fn fr-ns tc">
									<small><i class="fas fa-check"></i> คุณได้ยืนยันการรับสินค้านี้แล้ว</small>
								</h4>
								@else
								<button data-seller-id="{{ $seller['id'] }}" data-order-id="{{ $data['id'] }}" data-toggle="modal" data-c-modal-target="#model_item_received_confirmation" class="btn btn-primary mt3 w-100 w-auto-ns fr">
									<i class="fas fa-check"></i> ยืนยันการรับสินค้า
								</button>
								@endif
							</div>	
						</div>	
					</div>

				</div>

			@endforeach

		</div>


	</div>

</div>

<div id="model_item_received_confirmation" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

   	<div id="item_received_confirmation_detail">
   		<!--  -->
  	</div>

  </div>
</div>

<div id="model_shipping_detail" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner fullscreen">
    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div id="shipping_detail" class="c-page-container">
    	<!--  -->
   	</div>
  </div>
</div>

<script type="text/javascript">

	$('[data-c-modal-target="#model_item_received_confirmation"]').on('click',function(e){
	  e.preventDefault();

	  $('#item_received_confirmation_detail').text('');

  	let request = $.ajax({
  	  url: "/item-receiving-confirmation/"+$(this).data('order-id')+"/"+$(this).data('seller-id'),
  	  type: "GET",
  	  dataType: 'json',
  	  // contentType: false,
  	  // cache: false,
  	  // processData:false,
  	  beforeSend: function( xhr ) {
  	  	Loading.show();
  	  }
  	});

  	request.done(function (response, textStatus, jqXHR){
      $('#item_received_confirmation_detail').html(response.html);
      Loading.hide();
  	});

  	request.fail(function (jqXHR, textStatus, errorThrown){
  	  console.error(
  	      "The following error occurred: "+
  	      textStatus, errorThrown
  	  );
  	});
	});
	
	$('[data-c-modal-target="#model_shipping_detail"]').on('click',function(e){
	  e.preventDefault();

	  $('#shipping_detail').text('');

  	let request = $.ajax({
  	  url: "/get-shipping-detail/"+$(this).data('order-id')+"/"+$(this).data('seller-id'),
  	  type: "GET",
  	  dataType: 'json',
  	  // contentType: false,
  	  // cache: false,
  	  // processData:false,
  	  beforeSend: function( xhr ) {
  	  	Loading.show();
  	  }
  	});

  	request.done(function (response, textStatus, jqXHR){
      $('#shipping_detail').html(response.html);
      Loading.hide();
  	});

  	request.fail(function (jqXHR, textStatus, errorThrown){
  	  console.error(
  	      "The following error occurred: "+
  	      textStatus, errorThrown
  	  );
  	});

	});

</script>

@stop