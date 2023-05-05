@extends('shared.main')
@section('content')

<div class="bg-drop bg-drop-full"></div>

<div class="mb4">
  <div class="c-list-container c-list-container-lg pv2">
    <div class="row">

      <div class="col-7">
        <div class="pv3">
          <h4 class="mt1 white">รายการที่ไม่สนใจ</h4>
        </div>
      </div>

    </div>
  </div>
</div>

<div id="blocking_data_list">
  @if(count($blockedData) > 0)
  <div class="c-form-container pv2">
    <div class="list-group">
      @foreach($blockedData as $_value)

        <?php $value = $_value->buildDataList(); ?>

        <?php $notFirst = false; ?>

        <div class="list-group-item list-group-item-action blocked-item clearfix"  data-blocking-ident="{{$value['model']}}_{{$value['modelId']}}">
          <div class="fl w-100 w-80-ns">
            @foreach($value['data'] as $__value)

              @if($notFirst) และ @endif

              <span>{{$__value['label']}} "<a href="{{$__value['url']}}">{{$__value['name']}}</a>"</span>
            
              <?php $notFirst = true; ?>

            @endforeach
          </div>
          <div class="fr w-100 w-20-ns tr">
            <a href="#" class="btn c-btn c-btn-bg" data-remove-blocking="1" data-blocked-type="{{$value['model']}}" data-blocked-id="{{$value['modelId']}}" class="user-blocking-item">ยกเลิก</a>
          </div>
        </div>
      
      @endforeach
    </div>
  </div>
  @else
  <div class="c-form-container mv7">
    <div class="message-panel tc">
      <div class="center w-90 w-100-ns">
        <h5 class="white">ยังไม่มีรายการที่ไม่สนใจ</h5>
      </div>
    </div>
  </div>
  @endif
</div>

<script type="text/javascript" src="/assets/js/user-blocking.js"></script>
<script type="text/javascript">
  const userBlocking = new UserBlocking();
  userBlocking.init();
</script>

@stop