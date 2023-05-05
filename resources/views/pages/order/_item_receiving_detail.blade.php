<h4 class="mid-gray pb3">ยืนยันการรับสินค้า</h4>

@if($hasData)

	<div>จากผู้ขาย <strong>{{ $data['seller'] }}</strong></div>

	<div class="row">
		<div class="col-12">
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

		<div class="col-12">
			{{Form::open(['url' => 'item-receiving-confirmation', 'class' => 'user-form mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

				{{ Form::hidden('order_id', $orderId) }}
				{{ Form::hidden('user_id', $userId) }}

				<div class="mv4">
					<h4>คุณได้รับสินค้า <span class="f2 color-green">{{ count($orderItems) }}</span> รายการตามที่ระบุแล้ว?</h4>
				</div>

				{{Form::submit('ยืนยัน', array('class' => 'btn btn-primary br0'))}}
				<button data-close="modal" type="button" class="btn btn-link br0">ยกเลิก</button>

			{{Form::close()}}
		</div>

	</div>

@else
		<div class="tc">
	      <h4 class="mv4">ผู้ขายยังไม่ได้ยืนยันการจัดส่งสินค้า</h4>
	      <button data-close="modal" class="btn c-btn c-btn-bg">กลับ</button>
	    </div>
@endif