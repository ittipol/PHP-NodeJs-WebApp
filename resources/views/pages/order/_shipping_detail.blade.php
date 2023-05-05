<h4 class="mid-gray pb3">การจัดส่งสินค้า</h4>

@if($hasData)
	
	<div class="row">
		<div class="col-12 col-md-6">
			<div><span class="f2 color-green">{{ count($orderItems) }}</span> รายการสินค้า</div>

			<div class="cart-list">
			  @foreach($orderItems as $item)
			    <div class="cart-card-item clearfix">
			      <div class="cart-card-item-right fl w-100">
			        <div class="cart-card-item-inner clearfix">
			          <div class="item-image-frame fl">
			            <div class="item-image" style="background-image: url('{{ $item['image']['_preview_url'] }}')"></div>
			          </div>
			          <div class="item-content fl">
			            <div class="item-primary-text">{{ $item['name'] }}</div>
			            <div class="item-quantity-text">
			              <div>x {{ $item['quantity'] }}</div>
			            </div>
			          </div>
			        </div>
			      </div>
			    </div>
			  @endforeach
			</div>
		</div>

		<div class="col-12 col-md-6 mt4 mt0-ns">
			<div>
				<h5>รายละเอียดการจัดส่ง</h5>
				<hr>
				{!! $data['detail'] !!}
			</div>

			<hr>

			@include('shared.shipping-image-gallery')
		</div>
	</div>

@else
	<div class="tc">
      <h4 class="mv4">ผู้ขายยังไม่ได้ยืนยันการจัดส่งสินค้า</h4>
      <button data-close="modal" class="btn c-btn c-btn-bg">กลับ</button>
    </div>
@endif