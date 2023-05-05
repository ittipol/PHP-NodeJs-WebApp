@extends('shared.main')
@section('content')

<div class="c-form-container mt4">
  <div class="mb4 mb5-ns">
    <h4>เหรียญ</h4>
    <p>อัตราการแลกเปลี่ยน <a href="">รายละเอียด</a></p>
  </div>
</div>

{{Form::open(['id' => 'item_form', 'method' => 'post', 'enctype' => 'multipart/form-data'])}}

  <div class="c-form-container">
    @include('shared.form_error')
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">

        <div class="form-group">
          <label class="form-control-label required">ระบุจำนวนเงิน</label>
          {{ Form::text('amount', null, array(
            'class' => 'form-control',
            'autocomplete' => 'off'
          )) }}
        </div>

        <hr class="mv4">

        <div class="form-group">
          <label>
            <input type="radio" name="method" class="method-rdo" value="method_1" checked> บัตรเครคิต
          </label>
        </div>

        <div id="method_1" style="display:none;">

          <div class="alert alert-secondary" role="alert">
            เราจะไม่เก็บข้อมูลบัตรเครดิตหรืออะไรทั้งสิ้น โดยไม่ได้รับอนุญาตจากคุณ <br>
            ข้อมูลทั้งหมดที่คุณกรอกมาจะถูกส่งผ่านการเชื่อมต่อที่ปลอดภัย แบบ SSL คุณจึงมั่นใจได้ในความปลอดภัยของข้อมูล 
          </div>

          <div id="card_error" class="alert alert-danger fade in" style="display:none"></div>

          <div class="form-group">
            <label class="form-control-label required">ชื่อผู้ถือบัตร</label>
            {{ Form::text('holder_name', null, array(
              'id' => 'holder_name',
              'class' => 'form-control',
              'autocomplete' => 'off'
            )) }}
          </div>

          <div class="form-group">
            <label class="form-control-label required">หมายเลขบัตร</label>
            {{ Form::text('holder_name', null, array(
              'id' => 'card_number',
              'class' => 'form-control cc-input',
              'placeholder' => '---- ---- ---- ----',
              'autocomplete' => 'off'
            )) }}
          </div>

          <div class="form-group">
            <label class="form-control-label required">CVC <a href="#" data-toggle="modal" data-c-modal-target="#model_cvc">คืออะไร</a></label>
            {{ Form::text('holder_name', null, array(
              'id' => 'cvc',
              'class' => 'form-control cc-input',
              'autocomplete' => 'off'
            )) }}
          </div>

          <div class="form-group">
            <label class="form-control-label required">วันหมดอายุ</label>
            {{ Form::text('holder_name', null, array(
              'id' => 'card_expire',
              'class' => 'form-control cc-input',
              'placeholder' => 'MM / YY',
              'autocomplete' => 'off'
            )) }}
          </div>
    
        </div>

        <hr class="mv4">

        <!-- ####################################################################################### -->

        <div class="form-group">
          <label>
            <input type="radio" name="method" class="method-rdo" value="method_2"> โอนเงินผ่านธนาคาร
          </label>
        </div>

        <div id="method_2" style="display:none;">

          <div class="alert alert-secondary" role="alert">
            ท่านสามารถโอนเงินผ่านบัญชีธนาคารได้ตามเลขที่บัญชีที่ระบุ 
            <a href="#" data-toggle="modal" data-c-modal-target="#model_bank_account">เลขที่บัญชีธนาคาร</a>
            หลังจากโอนเงินแล้ว กรุณาแจ้งโอนเงินได้ที่แบบฟอร์มด้านล่าง
          </div>

          <div class="form-group  col-md-12">
            <label class="form-control-label required">บัญชีที่รับโอน</label>
            <label class="radio db">
              {{Form::radio('bank_acc', 1, true)}} ธ.กสิกรไทย
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;999-9999-9999
            </label>
            <label class="radio db">
              {{Form::radio('bank_acc', 2, false)}} ธ.ไทยพาณิชย์ 
              &nbsp;&nbsp;&nbsp;999-9999-9999
            </label>
            <label class="radio db">
              {{Form::radio('bank_acc', 3, false)}} ธ.กรุงไทย 
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;999-9999-9999
            </label>
          </div>

          <div class="row">
            <div class="form-group col-md-6">
              <label class="form-control-label required">วันที่โอน</label>
              <div class="input-group">
                <span class="input-group-addon" id="location-addon">
                  <i class="fa fa-calendar"></i>
                </span>
                {{Form::text('date', null, array('id' => 'date', 'class' => 'form-control' ,'autocomplete' => 'off', 'readonly' => 'true'))}}
                <a class="date-clear" data-date-clear="#date_2"><span aria-hidden="true">×</span></a>
              </div>
            </div>

            <div class="form-group col-md-6">
              <label class="form-control-label">เวลา</label>
              <div class="input-group">
                {{ Form::select('end_time_hour', $hour, null, array('class' => 'form-control')) }}
                {{ Form::select('end_time_min', $min, null, array('class' => 'form-control')) }}
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
  </div>

  <div class="c-form-container mb4">
    <div class="row">
      <div class="col-12">
        <div class="mb3">
          การคลิก "แจ้งการโอน" แสดงว่าคุณยินยอมตาม<a href="#" data-toggle="modal" data-c-modal-target="#modal_exchange_term_and_condition">ข้อกำหนดและเงื่อนไข</a>แล้ว
        </div>

        {{Form::submit('แจ้งการโอน', array('class' => 'btn c-btn c-btn-bg btn-block'))}}
      </div>
    </div>
  </div>

{{Form::close()}}

<div class="clearfix margin-top-200"></div>

<div id="model_cvc" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <h4 class="item-title f4 f3-ns mb-3 mb4-ns">CVC คืออะไร</h4>

    รหัสยืนยันบัตรหรือ CVC เป็นรหัสพิเศษที่พิมพ์อยู่บนบัตรเดบิตหรือบัตรเครดิตของคุณ <br><br>
    สำหรับบัตรอเมริกันเอ็กซ์เพรส CVC จะปรากฏเป็นรหัส 4 หลักที่แยกต่างหากพิมพ์อยู่บนด้านหน้าของบัตรของคุณ ส่วนบัตรอื่น ๆ ทั้งหมด (Visa, Master Card, บัตรของธนาคารอื่น ๆ ) จะเป็นตัวเลขสามหลักที่พิมพ์อยู่ถัดจากแถบลายเซ็นด้านหลังของบัตรของคุณ โปรดสังเกตว่ารหัส CVC จะไม่นูน (ต่างจากหมายเลขบัตร หลักด้านหน้า) <br><br>
    CVC จะไม่ได้ถูกพิมพ์บนใบเสร็จรับเงินใด ๆ ด้วยเหตุนี้มันจึงไม่เป็นที่ทราบหรือพบเห็นโดยบุคคลอื่นที่ไม่ใช่เจ้าของบัตรที่แท้จริง <br><br>
    กรอกรหัส CVC เพื่อยืนยันว่าคุณคือผู้ถือบัตรสำหรับการทำรายการในครั้งนี้และเพื่อหลีกเลี่ยงบุคคลอื่นที่ไม่ใช่คุณไม่ให้สามารถทำการซื้อสินค้าโดยใช้หมายเลขบัตรของคุณได้ <br><br>
    *** โปรดสังเกตว่าชื่อของรหัสนี้อาจเรียกแตกต่างกันไปตามบริษัทผู้ออกบัตร เช่น Card Verification Value (CVV), the Card Security Code หรือ the Personal Security Code ซึ่งทั้งหมดนี้เป็นข้อมูลแบบเดียวกัน

  </div>
</div>

<div id="modal_exchange_term_and_condition" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <h4 class="item-title f4 f3-ns mb-3 mb4-ns">ข้อกำหนดและเงื่อนไข</h4>
    
  </div>
</div>

<div id="model_bank_account" class="c-modal">
  <a class="close"></a>
  <div class="c-modal-inner">

    <a class="modal-close">
      <span aria-hidden="true">&times;</span>
    </a>

    <h4 class="item-title f4 f3-ns mb-3 mb4-ns">บัญชีธนาคาร</h4>

    @include('content.bank_account')
    
  </div>
</div>


<script type="text/javascript" src="/assets/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/assets/js/form/form-datepicker.js"></script>
<script type="text/javascript" src="/assets/js/jquery.payform.min.js"></script>

<script type="text/javascript" src="/assets/js/form/validation/exchange-form-validation.js"></script>
<script type="text/javascript" src="/assets/js/form/validation/credit-card-validation.js"></script>


<script type="text/javascript">

  class ExchangeForm {

    constructor() {
      this.currentMethod = null;
    }

    load() {

      let _this = this;

      this.bind();

      setTimeout(function(){
        _this.currentMethod = $('.method-rdo:checked').val();
        $('#'+_this.currentMethod).slideDown(300);
      },750);
      
    }

    bind() {

      let _this = this;

      // remove
      $("#exchange_form").submit(function () {
        return false;
      }); 

      $('.method-rdo').on('click',function(){

        if($('.method-rdo:checked').val() == _this.currentMethod) {
          return false;
        }

        $('#'+_this.currentMethod).slideUp(300);

        _this.currentMethod = $('.method-rdo:checked').val();
        $('#'+_this.currentMethod).delay(420).slideDown(300);
      });

      $('#date').on('change',function(){

        if($(this).val() != '') {

          let date = $(this).val().split('-');
          $('#date-input-label').text(parseInt(date[2])+' '+_this.findMonthName(parseInt(date[1]))+' '+(parseInt(date[0])+543));

        }

      });

    }

    findMonthName(month) {
      let monthName = [
        'มกราคม',
        'กุมภาพันธ์',
        'มีนาคม',
        'เมษายน',
        'พฤษภาคม',
        'มิถุนายน',
        'กรกฎาคม',
        'สิงหาคม',
        'กันยายน',
        'ตุลาคม',
        'พฤศจิกายน',
        'ธันวาคม',
      ];

      return monthName[month-1];
    }

  }

  $(document).ready(function(){
    const exchangeForm = new ExchangeForm();
    exchangeForm.load();

    const date = new Datepicker('#date');
    date.init();

    Validation.initValidation();
  });

</script>

@stop