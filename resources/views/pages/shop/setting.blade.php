@extends('shared.shop-with-menu')
@section('shop-content')

<div class="mb4 pv2">
  <div class="c-form-container pv2">
    <div class="row">

      <div class="col-12">
        <div class="pv2">
          <h4 class="mt1">ตั้งค่า</h4>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="c-form-container pv2">
  <div class="list-group">
    <a href="/shop/page/{{$slug}}/edit" class="list-group-item list-group-item-action"><i class="far fa-edit"></i>&nbsp;แก้ไขข้อมูลร้านขายสินค้า</a>
    <a href="/shop/page/{{$slug}}/remove" class="list-group-item list-group-item-action"><i class="fas fa-unlink"></i>&nbsp;ลบร้านขายสินค้า</a>
  </div>
</div>

@stop