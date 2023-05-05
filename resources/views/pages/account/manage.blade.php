@extends('shared.account-with-menu')
@section('account-content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container c-list-container-lg pv2">
    <div class="row">

      <div class="col-7">
        <div class="pv3">
          <h4 class="mt1 white">รายการขายของฉัน</h4>
        </div>
      </div>

      <div class="col-5">
        <div class="pv2 clearfix">
          <div class="fr">
            <a href="#" data-toggle="modal" data-c-modal-target="#model_filter" class="btn btn-secondary icon-green b--transparent mt2"><i class="fas fa-sort-amount-down"></i> จัดเรียง</a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

@if(($data->total() > 0) && ($data->currentPage() <= $data->lastPage()))

<div class="c-list-container c-list-container-lg ph4">
  <div class="row">
    @foreach($data as $_value)

      <?php $value = $_value->buildManagingDataList(); ?>

      @include('shared.item-card-managing')

    @endforeach
  </div>
  {{$data->links('shared.pagination', ['paginator' => $data])}}

</div>

@else

<div class="c-list-container mv7">
  <div class="message-panel tc">
    <div class="center w-90 w-100-ns">
      <h5 class="white">ยังไม่มีรายการขาย</h5>
      <a href="/ticket/new" class="pv2 ph4 mt3 btn btn-primary c-shadow-3">ขายบัตรของคุณตอนนี้</a>
    </div>
  </div>
</div>

@endif


{{Form::model($filter, ['id' => 'filter_panel', 'method' => 'get', 'enctype' => 'multipart/form-data'])}}
<div id="model_filter" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-sidebar-inner">

    <div class="c-panel-container">

      <a class="modal-close fixed-outer">
        <span aria-hidden="true">&times;</span>
      </a>

      <div class="mv4">
        
        <h5 class="mb3"><small>จัดเรียงตาม</small></h5>

        <div>
          <div class="c-input">
            {{Form::radio('sort', 'post_n', true, array('id' => 'sort1'))}}
            <label for="sort1">
              รายการขาย - ใหม่ไปเก่า
            </label>
          </div>
          <div class="c-input">
            {{Form::radio('sort', 'post_o', false, array('id' => 'sort2'))}}
            <label for="sort2">
              รายการขาย - เก่าไปใหม่
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

@include('shared.item-cancel-modal')

<script type="text/javascript">
  // $('.lazy').lazy({
  //   effect: "fadeIn",
  //   effectTime: 220,
  //   threshold: 0
  // });
</script>

@stop