<div id="modal_cart_checkout" class="c-modal" data-remove-modal-elem="1">
  <a class="close"></a>
  <div class="c-modal-inner fullscreen">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="bg-drop bg-drop-full"></div>

    <div class="c-page-container">

      <h5 class="mid-gray pb3">สั่งซื้อ</h5>

      @if(!empty($items))

        @if(Auth::guest())
          <div class="alert alert-danger" role="alert">
            คุณยังไม่ได้เข้าสู่ระบบ <a href="#" data-toggle="modal" data-c-modal-target="#modal_user_menu">เข้าสู่ระบบ</a> | ยังไม่มีบัญชี? <a href="#" data-toggle="modal" data-c-modal-target="#model_register_form">สมัครใช้งาน</a>
          </div>
        @endif

        <div class="row">
          
          <div class="col-12 col-md-6 mb4">

            <h5 class="mid-gray pt3">{{$info['amount']}} รายการ</h5>

            <div class="cart-list">
              @foreach($items as $item)
                <div class="cart-card-item @if(!empty($item['errors'])) cart-card-item-error @endif clearfix">
                  @if(!empty($item['errors']))
                  <div class="alert alert-danger" role="alert">
                    <div><i class="fas fa-exclamation"></i>&nbsp;&nbsp;ไม่สามารถสั่งซื้อ {{$item['name']}} ได้</div>
                    <ul class="mb0">
                      @foreach($item['errors'] as $error)
                        <li>{{$error['message']}}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif
                  <div class="cart-card-item-right fl w-100">
                    <div class="cart-card-item-inner clearfix ma0">
                      <div class="item-image-frame fl">
                        <div class="item-image" style="background-image: url('{{ $item['image']['_preview_url'] }}')"></div>
                      </div>
                      <div class="item-content fl">
                        <div class="item-primary-text">{{ $item['name'] }}</div>
                        <div class="item-secondary-text">{{ $item['price']['price'] }}</div>
                        <div class="item-quantity-text">
                          <div>x {{ $item['quantity'] }}</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="cart-summary mt3">

              <div class="cart-summary-row">
                <div class="cart-summary-item">
                  <div class="row">
                    <div class="cart-summart-value-title w-50">
                      จำนวนสินค้าทั้งหมด
                    </div>
                    <div class="w-50">
                      <div class="cart-summart-value tr">{{$info['quantity']}}</div>
                    </div>
                  </div>
                </div>
              </div>

              <hr>

              @foreach($info['summary'] as $summary)
              <div class="cart-summary-row">
                <div class="cart-summary-item {{$summary['class']}}">
                  <div class="row">
                    <div class="cart-summart-value-title w-50">
                      {{$summary['title']}}
                    </div>
                    <div class="w-50">
                      <div class="cart-summart-value tr">{{$summary['value']}}</div>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

          </div>

          <div class="col-12 col-md-6">

            {{Form::open(['url' => 'checkout', 'id' => 'checkout_form', 'class' => 'mb6', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

              <div class="form-group">
                <label class="form-control-label required b">ชื่อผู้ซื้อ</label>
                <input type="text" name="buyer_name" class="form-control" placeholder="ชื่อ" value="{{$buyer['name']}}">
              </div>

              <div class="form-group">
                <div>
                  <label class="control control--checkbox mb-2">
                    เปลี่ยนซื่อของฉันตามที่ระบุใหม่
                    <input type="checkbox" name="update_buyer_name" value="1">
                    <div class="control__indicator"></div>
                  </label>
                </div>
              </div>

              <div class="form-group">
                <label class="form-control-label required b">ที่อยู่สำหรับจัดส่ง</label>
                <div class="alert alert-secondary" role="alert">
                  <span class="red">ที่อยู่สำหรับจัดส่งจะไม่สามารถแก้ไขได้หลักจากคลิก "ยืนยัน" แล้ว</span>
                </div>
                <textarea name="shipping_address" class="form-control">{{$buyer['shippingAddress']}}</textarea>
              </div>

              <div class="form-group">
                <div>
                  <label class="control control--checkbox mb-2">
                    จดจำที่อยู่สำหรับจัดส่งนี้เป็นค่าเริ่มต้น
                    <input type="checkbox" name="update_shipping_address" value="1">
                    <div class="control__indicator"></div>
                  </label>
                </div>
              </div>

              @if(Auth::check())
                {{Form::submit('ยืนยัน', array('class' => 'btn c-btn c-btn-bg btn-block'))}}
              @else
                <button id="checkout_not_login" class="btn c-btn c-btn-bg btn-block">ยืนยัน</button>
              @endif

            {{Form::close()}}

          </div> 

        </div>

      @else

        <div class="tc">
          <h4 class="mv4">ไม่มีสินค้าในตระกร้า</h4>
          <button data-close="all-modal" class="btn c-btn c-btn-bg">กลับ</button>
        </div>

      @endif

    </div>

  </div>

</div>

<script type="text/javascript" src="/assets/js/form/validation/checkout-validation.js"></script>

<script type="text/javascript">

  $(document).ready(function(){

    CheckoutValidation.initValidation();

    $('#checkout_not_login').on('click',function(e){
      e.preventDefault();
      snackbar.setTitle('กรุณาเข้าสู่ระบบเพื่อทำการสั่งซื้อสินค้า');
      snackbar.display();
    });
    
  });

</script>