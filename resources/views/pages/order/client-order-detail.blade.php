@extends('shared.account-with-menu')
@section('account-content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container c-list-container-lg pv2">
    <div class="row">

      <div class="col-12">
        <div class="pv3">
          <h4 class="mt1 mb2">
            คำสั่งซื้อสินค้า #{{ $order['id'] }}
          </h4>
          <a href="/order" class="btn c-btn c-btn-bg"><i class="fas fa-chevron-left"></i> กลับไปยังหน้าคำสั่งซื้อจากลูกค้า</a>
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
    <h5><small>จำนวนเงินจากการขายสินค้าของคุณ</small></h5>

    <div class="mb2">
      <h4>{{ $income }}</h4>
      @if($paid)
      <span class="green b">ลูกค้าชำระเงินแล้ว</span>
      @else
      <span class="red b">ลูกค้ายังไม่ได้ชำระเงิน</span>
      @endif
    </div>

  </div>

  <div class="order-info-box">
    <h5>การจัดส่งสินค้า</h5>

    <h5><small>รายการสั่งซื้อสินค้า</small></h5>

    <div class="cart-list">
      @foreach($orderItems as $item)
        <div class="cart-card-item mt-0 clearfix">
          <div class="cart-card-item-right fl w-100">
            <div class="cart-card-item-inner clearfix">
              <div class="item-image-frame fl">
                <div class="item-image" style="background-image: url('{{ $item['image']['_preview_url'] }}')"></div>
              </div>
              <div class="item-content fl">
                <div class="item-primary-text">{{ $item['name'] }}</div>
                <div class="item-secondary-text">{{ $item['subTotal'] }}</div>
                <div class="item-quantity-text">
                  <div>x {{ $item['quantity'] }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div>
      @if($shippingConfirmed)
      <div class="mv4">
        <span class="green b">ยืนยันการจัดส่งสินค้าเสร็จสมบูรณ์</span>
      </div>
      @else
      <button data-toggle="modal" data-c-modal-target="#model_shipping_confirmation" class="btn btn-primary btn-block mt3 mb2">
        ยืนยันการจัดส่งสินค้า
      </button>
      @endif
    </div>

  </div>

  <div class="order-info-box">
    <div class="row">
      <div class="col-12 col-md-6">
        <h5><small>ชื่อผู้ซื้อ</small></h5>
        <div>{{ $order['buyer_name'] }}</div>
      </div>
      <div class="col-12 col-md-6">
        <hr class="db dn-l mv3 bg-light-silver">
        <h5><small>ที่อยู่สำหรับการจัดส่ง</small></h5>
        <div>{{ $order['shipping_address'] }}</div>
      </div>  
    </div>
  </div>

</div>

{{Form::open(['url' => 'shipping-confirmation/'.$order->id, 'id' => 'shipping_confirmation_form', 'class' => 'mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

<div id="model_shipping_confirmation" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner fullscreen">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="c-page-container">

      <h5 class="mid-gray pb3">ยืนยันการจัดส่งสินค้า</h5>

      <div class="row">
          
        <div class="col-12 col-md-6 mb4">

          <h5 class="mid-gray pt3">สินค้าและจำนวนสินค้าที่ต้องจัดส่ง</h5>

          <div class="cart-list">
            @foreach($orderItems as $item)
              <div class="cart-card-item mt-0 clearfix">
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

        <div class="col-12 col-md-6">

          <h5>ระบุการจัดส่งสินค้า</h5>
          <p>กรอกข้อมูลการจัดส่งสินค้าพร้อมรายละเอียดที่เกี่ยวข้อง ข้อมูลทั้งหมดจะถูกส่งไปยังลูกค้า</p>

          <div class="form-group">
            <label class="form-control-label required b">รายละเอียดการจัดส่ง</label>
            <textarea name="shipping_detail" class="form-control"></textarea>
          </div>

          <div class="form-group">
            <label class="form-control-label b">รูปภาพที่เกี่ยวข้องกับการจัดส่ง</label>
            <div class="alert alert-secondary ma0 br0">
              <ul class="ma0">
                <li>ขนาดไฟล์สูงสุดไม่เกิน 5 MB</li> 
                <li>อัพโหลดไฟล์ได้ในรูปแบบ JPG หรือ PNG</li>
              </ul>
            </div>

            <div class="image-total pv4 ph2">
              <div id="image_item_list">

              </div>
              <div class="pt2 mt1">
                <h5 id="image_total" class="f5 tc mb-3">ไม่มีรูปภาพ</h5>
              </div>
            </div>

            <a class="btn btn-secondary btn-block br0" href="#" data-toggle="modal" data-c-modal-target="#model_upload_item_image"><i class="far fa-image"></i> เพิ่ม/แก้ไขรูปภาพ</a>
            
          </div>

          {{Form::submit('ยืนยัน', array('class' => 'btn c-btn c-btn-bg btn-block'))}}

        </div>

      </div>

    </div>

  </div>
</div>

<div id="model_upload_item_image" class="c-modal">
  <div class="c-modal-inner w-100 h-100">
    <a class="btn btn-secondary modal-close f5">
      <span aria-hidden="true">ตกลง</span>
    </a>
    <h4 class="item-title f4 f3-ns mb3 mb4-ns">รูปภาพที่เกี่ยวกับการจัดส่ง</h4>

    <div>
      <div id="_image_group" class="upload-image"></div>
    </div>
  </div>
</div>

{{Form::close()}}

<script type="text/javascript" src="/assets/js/form/validation/shipping-confirmation-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/upload_image.js"></script>

<script type="text/javascript">
  $(document).ready(function(){

    ShippingConfirmation.initValidation();

    const images = new UploadImage('#shipping_confirmation_form','#_image_group','OrderShippingConfirmation','photo',10);
    images.init();
    images.setImages();
  });
</script>

@stop