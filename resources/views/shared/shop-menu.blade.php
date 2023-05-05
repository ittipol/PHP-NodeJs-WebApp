@if(Auth::check() && (Auth::user()->id == $shop['created_by']))
  <a href="/shop/page/{{$shop['slug']}}/setting" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
    <i class="fas fa-sliders-h"></i>&nbsp;ตั้งค่า
  </a>
  <a href="/account/sale" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
    <i class="far fa-list-alt"></i>&nbsp;จัดการขาย
  </a>
@endif

<a href="/shop/page/{{$shop['slug']}}/item" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
  <i class="fas fa-tags"></i>&nbsp;สินค้า
</a>

@if(Auth::check() && (Auth::user()->id != $shop['created_by']))
  <a href="#" data-chat-box="1" data-chat-data="m|Shop|{{$shop['id']}}" data-chat-close="1" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
    <i class="fas fa-comments"></i>&nbsp;แชท
  </a>
  <a href="#" data-blocking="1" data-blocking-ident="Shop_{{$shop['id']}}" data-blocked-type="Shop" data-blocked-id="{{$shop['id']}}" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
    @if($blocked)
    <span class="user-blocking-icon">
      <i class="fas fa-stop"></i>
    </span>
    <span class="user-blocking-label">
      ยกเลิกไม่สนใจรายการขายจากร้านนี้
    </span>
    @else
    <span class="user-blocking-icon">
      <i class="fas fa-ban"></i>
    </span>
    <span class="user-blocking-label">
      ไม่สนใจรายการขายจากร้านนี้
    </span>
    @endif
  </a>
@endif

<a href="/shop/page/{{$shop['slug']}}/about" class="btn btn-secondary w-100 w-auto-ns br0 mt2 mt0-ns">
  <i class="fas fa-ellipsis-h"></i>&nbsp;เกี่ยวกับ
</a>