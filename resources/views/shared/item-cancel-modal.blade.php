<div id="cancel_item_modal" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <h4 class="item-title f4 f3-ns mb-3 mb4-ns"></h4>

    <h5 class="f5"><strong>คุณยกเลิกรายการขายนี้เพราะ</strong>?</h5>

    {{Form::open(['url' => '/ticket/cancel', 'id' => 'cancel_item_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}
      <div class="row">
        <div class="col-md-4 md-radio tc-ns">
          <input id="cancel_option_1" class="close-option" type="radio" value="1" name="cancel_option" checked>
          <label for="cancel_option_1">ขายสินค้านี้แล้ว</label>
        </div>
        <div class="col-md-4 md-radio tc-ns">
          <input id="cancel_option_2" class="close-option" type="radio" value="2" name="cancel_option">
          <label for="cancel_option_2">ยกเลิกการขาย</label>
        </div>
        <div class="col-md-4 md-radio tc-ns">
          <input id="cancel_option_3" class="close-option" type="radio" value="3" name="cancel_option">
          <label for="cancel_option_3">เหตุผลอื่น</label>
        </div>
      </div>

      <textarea id="cancel_reason" class="modal-textarea form-control w-100 mt3" name="cancel_reason"></textarea>
      <small>โปรดระบุเหตุผล</small>
      <button type="submit" class="btn c-btn c-btn-bg btn-block br0 mt3">ตกลง</button>
    {{Form::close()}}

  </div>
</div>

<script type="text/javascript" src="/assets/js/item-cancel.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    const _itemCancel = new ItemCancel();
    _itemCancel.init();
  });
</script>
