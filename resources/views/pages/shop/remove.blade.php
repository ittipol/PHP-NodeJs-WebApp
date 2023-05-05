@extends('shared.main')
@section('content')

<div class="mb4 pv2">
  <div class="c-form-container pv2">
    <div class="row">
      <div class="col-12">
        <div class="pv2">
          <h4 class="mt1"><a href="/shop/page/{{$slug}}" class="c-btn c-btn mr3"><i class="fa fa-chevron-left" aria-hidden="true"></i></a> ลบร้านขายสินค้า</h4>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="c-main-container mv6 mv7-ns">
  <div class="tc">
    <h5 class="mb4 lh-copy">การลบร้านขายสินค้าของคุณจะทำให้ไม่มีใครสามารถดูหรือค้นหาร้านขายสินค้าได้อีก รายการขายทั้งหมดของคุณจะถูกลบทั้งหมด</h5>
    <a href="#" class="btn c-btn c-btn-bg" data-toggle="modal" data-c-modal-target="#model_shop_remove">ลบร้านขายสินค้า</a>
  </div>
</div>

<div id="model_shop_remove" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <div class="tc">

      <!-- <h4 class="item-title f4 f3-ns mb-3 mb4-ns">ลบร้านขายสินค้า</h4> -->

      <div>
        เมื่อคุณคลิกลบแล้ว ร้านขายสินค้าและข้อมูลร้านขายสินค้าของคุณจะถูกลบ รวมถึงรายการขายของร้านขายสินค้าจะถูกลบทั้งหมด
      </div>

      <a href="javascript:void(0);" id="shop_remove_btn" class="btn c-btn c-btn-bg dib mt3">ลบ {{$name}}</a>
    </div>

  </div>
</div>

<script type="text/javascript">
  
  class RemoveShop {

    constructor() {
      this.io = null;
    }

    init() {

      this.io = new IO();

      this.bind();
      this.socketEvent();
    }

    bind() {

      let _this = this;

      $('#shop_remove_btn').on('click',function(){
        Loading.show();
        _this.io.socket.emit('shop-remove');
      });

    }

    socketEvent() {

      this.io.socket.on('shop-removed', function(res){
        // Loading.hide();

        const snackbar = new Snackbar();
        snackbar.setTitle('ร้านขายสินค้าของคุณถูกลบแล้ว');
        snackbar.display();

        setTimeout(function(){
          location.href = '/';
        },2400);

      });

    }

  }

  $(document).ready(function(){

    const removeShop = new RemoveShop();
    removeShop.init();

  });

</script>

@stop