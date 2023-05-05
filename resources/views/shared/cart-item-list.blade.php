@if(!empty($items))

  @foreach($items as $item)
    <div class="cart-card-item @if(!empty($item['errors'])) cart-card-item-error @endif clearfix" data-cart-id="{{$item['token']}}" >
      @if(!empty($item['errors']))
      <div class="alert alert-danger" role="alert">
        <div><i class="fas fa-exclamation"></i>&nbsp;&nbsp;ไม่สามารถสั่งซื้อ {{$item['name']}} ได้</div>
        
        <ul class="mb0">
          @foreach($item['errors'] as $error)
            <li>{{$error['message']}}</li>
          @endforeach
        </ul>

        <!-- @foreach($item['errors'] as $error)
          <div>- {{$error['message']}}</div>
        @endforeach -->
      </div>
      @endif
      <div class="cart-card-item-left fl">
        <a href="javascript:void(0);" data-cart="delete" data-cart-token="{{$item['token']}}">
          <i class="fas fa-minus"></i>
        </a>
      </div>
      <div class="cart-card-item-right fl">
        <div class="cart-card-item-inner clearfix">
          <div class="item-image-frame fl">
            <div class="item-image" style="background-image: url('{{$item['image']['_preview_url']}}')"></div>
          </div>
          <div class="item-content fl">
            <div class="item-primary-text">{{$item['name']}}</div>
            <div class="item-secondary-text">{{$item['price']['price']}}</div>
            <div class="item-quantity">
              <input type="text" data-cart="update" data-cart-item-id="{{$item['itemId']}}" value="{{$item['quantity']}}">
              <i class="fas fa-caret-down"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endforeach

@else
  <div class="cart-empty-text mt5 tc f4">ไม่มีสินค้า</div>
@endif