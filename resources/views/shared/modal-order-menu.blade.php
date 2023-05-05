@if(Auth::check())
<div id="model_order_menu" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <h4>การซื้อ-ขายของฉัน</h4>

    <hr class="bg-light-silver">

    <div class="row">
      <div class="col-12 col-md-6">

        <h5 class="mb3 relative">
          <small>คำสั่งซื้อจากลูกค้า</small>
          <div class="count-badge inline">{{ $_total_client_order }}</div>
        </h5>
        
        <a href="/client-order" class="btn c-btn c-btn-bg">
          <i class="fas fa-user-tag mr2"></i></i>ดูรายการคำสั่งซื้อจากลูกค้า
        </a>
        
        <hr>

        <div>
          <div>
            <span class="f2 color-green">{{ $_total_client_order }}</span> คำสั่งซื้อจากลูกค้าที่ยังไม่เสร็จสมบูรณ์
          </div>

          <table class="table">
            <thead>
              <tr>
                <th scope="col">สถานะคำสั่งซื้อ</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($_order_client_lists as $list)
              <tr>
                <td>{{ $list['label'] }}</td>
                <td class="tr"><span class="f4 color-green">{{ $list['total'] }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-12 col-md-6">
        
        <hr class="db dn-ns mv4 bg-light-silver">

        <h5 class="mb3 relative">
          <small>คำสั่งซื้อสินค้าของฉัน</small>
          <div class="count-badge inline">{{ $_total_my_order }}</div>
        </h5>

        <a href="/order" class="btn c-btn c-btn-bg">
          <i class="fas fa-cart-arrow-down mr2"></i>ดูรายการสั่งซื้อสินค้าของฉัน
        </a>

        <hr>

        <div>
          <div>
            <span class="f2 color-green">{{ $_total_my_order }}</span> คำสั่งซื้อของฉันที่ยังไม่เสร็จสมบูรณ์
          </div>

          <table class="table">
            <thead>
              <tr>
                <th scope="col">สถานะคำสั่งซื้อ</th>
                <th scope="col"></th>
              </tr>
            </thead>
            <tbody>
              @foreach($_order_lists as $list)
              <tr>
                <td>{{ $list['label'] }}</td>
                <td class="tr"><span class="f4 color-green">{{ $list['total'] }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>
@endif