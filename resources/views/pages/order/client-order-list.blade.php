@extends('shared.account-with-menu')
@section('account-content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-main-container pv2">
    <div class="row">

      <div class="col-7">
        <div class="pv3">
          <h4 class="mt1">
            คำสั่งซื้อจากลูกค้า
          </h4>
        </div>
      </div>

      <div class="col-5">
        <div class="pv2 clearfix">
          <div class="fr">
            <a href="#" data-toggle="modal" data-c-modal-target="#model_sort" class="btn btn-secondary icon-green b--transparent mt2"><i class="fas fa-sort-amount-down"></i> จัดเรียง</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>


@if(($data->total() > 0) && ($data->currentPage() <= $data->lastPage()))

<div class="c-main-container">

	<div class="cart-list c-box-shadow bg-white">
	  @foreach($data as $order)

      <?php $value = $order->order->buildDataList(); ?>

	    <div class="cart-card-item order-info-box mt0 clearfix">
	      <div class="cart-card-item-right fl w-100">
	        <div class="cart-card-item-inner cart-card-item-borderless clearfix">
	          <div class="item-image-frame fl">
	            <div class="item-image" style="background-image: url('{{ $value['images']['_preview_url'] }}')"></div>
	          </div>
	          <div class="item-content fl">
	            <div class="item-primary-text"><a href="/client-order-detail/{{ $value['id'] }}">คำสั่งซื้อที่ {{ $value['id'] }}</a> ({{ $value['orderStatusLabel'] }})</div>
	            <div class="item-secondary-text">{{ $value['total'] }}</div>
	            <div class="item-quantity-text">
	              <div>{{ $value['orderItemAmount'] }} รายการสินค้า (x {{ $value['quantity'] }})</div>
	            </div>
	          </div>
	        </div>
	      </div>
	    </div>
	  @endforeach
	</div>
  {{$data->links('shared.pagination', ['paginator' => $data])}}

</div>

@elseif($searching)

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5>ไม่พบคำสั่งซื้อจากลูกค้าที่ตรงกับการค้นหา</h5>
    </div>
  </div>
</div>

@else

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5>ยังไม่มีคำสั่งซื้อจากลูกค้า</h5>
    </div>
  </div>
</div>

@endif


{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div id="model_sort" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner">

    <div class="c-panel-container">

      <a class="modal-close fixed-outer">
        <span aria-hidden="true">&times;</span>
      </a>

      <div class="mv4">
        <h5 class="mb3"><small>แสดง</small></h5>
        <div>
          @foreach($orderStatuses as $key => $orderStatus)
          <div class="c-input">
            {{Form::checkbox('order_status[]', $orderStatus->id, null, array('id' => 'order_status_'.$key))}}
            <label for="order_status_{{$key}}">
              {{$orderStatus->label}}
            </label>
          </div>
          @endforeach
        </div>
      </div>

      <div class="mv4">
        <h5 class="mb3"><small>จัดเรียงตาม</small></h5>
        <div>
          <div class="c-input">
            {{Form::radio('sort', 'post_n', true, array('id' => 'sort1'))}}
            <label for="sort1">
              คำสั่งซื้อ - ใหม่ไปเก่า
            </label>
          </div>
          <div class="c-input">
            {{Form::radio('sort', 'post_o', false, array('id' => 'sort2'))}}
            <label for="sort2">
              คำสั่งซื้อ - เก่าไปใหม่
            </label>
          </div>
        </div>
      </div>

    </div>

    <div class="c-panel-container">
      <div class="row">
        <div class="col-12">
          <button type="submit" class="btn c-btn c-btn-bg br0">จัดเรียง</button>
        </div>
      </div>
    </div>

  </div>
</div>
{{Form::close()}}

@stop