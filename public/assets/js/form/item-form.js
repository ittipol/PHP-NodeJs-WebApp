class ItemForm {

  constructor() {
    this.handle;
  }

  init() {
    this.calDiscount();
    // this.disableContact($('input[name="use_specific_contact"]').is(':checked'));
    this.disableLocation($('input[name="use_shop_location"]').is(':checked'));
    this.dateInputField($('#date_type_select').val());
    this.bind();
    this.textEditor();
  }

  bind() {

    let _this = this;

    $('#price_input').on('keyup',function(){
      _this.calDiscount();
    })

    $('#original_price_input').on('keyup',function(){
      _this.calDiscount();
    })

    // $('input[name="use_specific_contact"]').on('click',function(e){
    //   _this.disableContact($(this).is(':checked'));
    // })

    $('input[name="use_shop_location"]').on('click',function(e){
      _this.disableLocation($(this).is(':checked'));
    })

    $('#date_type_select').on('change',function(){
      $('#date_input_1').val('');
      $('#date_input_2').val('');

      $('.date-readable').text('');

      _this.dateInputField($(this).val());
    })

    $('.date-clear').on('click',function(){
      let dateElem = $(this).data('date-clear');

      $(dateElem+' input[type="text"]').val('');
      $(dateElem+' .date-readable').text('');
    })

  }

  textEditor() {

    let floatingButton = new FloatingButton();

    ClassicEditor
        .create( document.querySelector( '#description' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
        } )
        .then(e=>{
            if(window.innerWidth <= 1024) {
              e.ui.focusTracker.on( 'change:isFocused', ( evt, name, value ) => {
                floatingButton.hideButton(value);
              });
            }
        })
        .catch( error => {
            console.log( error );
        } );

    ClassicEditor
        .create( document.querySelector( '#contact' ), {
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ]
        } )
        .then(e=>{
            const t = document.querySelector('input[name="use_specific_contact"]');

            if(t.checked) {
              e.isReadOnly = true;
            }

            t.addEventListener("click", ()=>{
              e.isReadOnly = !e.isReadOnly
            })

            // e.model.document.on('change:data', () => {
            //   // keypress
            // })

            if(window.innerWidth <= 1024) {
              e.ui.focusTracker.on( 'change:isFocused', ( evt, name, value ) => {
                floatingButton.hideButton(value);
              });
            }

        })
        .catch( error => {
            console.log( error );
        } );

  }

  dateInputField(type) {
    switch(type) {

      case '1':
            // $('#date_1').removeClass('col-12').addClass('col-md-6');
            $('#date_1 > label').text('วันที่เริ่มใช้').removeClass('required');
            $('#date_2 > label').text('ใช้ได้ถึง').addClass('required');
            $('#date_input_1').removeClass('date-required');
            $('#date_input_2').addClass('date-required');
            $('#date_group_1').css('display','flex');
            $('#date_group_2').css('display','flex');
            // $('hr.date-separate').removeClass('dn');

    
            // $('#date_input_1').rules("add", 
            // {
            //     required: false
            // });

            // $('#date_input_2').rules("add", 
            // {
            //     required: true
            // });

          break;
      case '2':
            $('#date_group_2').css('display','none');
            // $('#date_1').addClass('col-12').removeClass('col-md-6');
            $('#date_1 > label').text('วันที่แสดง').addClass('required');
            $('#date_input_1').addClass('date-required');
            $('#date_input_2').removeClass('date-required');
            $('#date_group_1').css('display','flex');
            // $('hr.date-separate').addClass('dn');


            // $('#date_input_1').rules("add", 
            // {
            //     required: true
            // });

            // $('#date_input_2').rules("add", 
            // {
            //     required: false
            // });

          break;
      case '3':
            // $('#date_1').removeClass('col-12').addClass('col-md-6');
            $('#date_1 > label').text('วันที่เดินทาง').addClass('required');
            $('#date_2 > label').text('วันที่กลับ').removeClass('required');
            $('#date_input_1').addClass('date-required');
            $('#date_input_2').removeClass('date-required');
            $('#date_group_1').css('display','flex');
            $('#date_group_2').css('display','flex');
            // $('hr.date-separate').removeClass('dn');


            // $('#date_input_1').rules("add", 
            // {
            //     required: true
            // });

            // $('#date_input_2').rules("add", 
            // {
            //     required: true
            // });
            
          break;
        default:
            $('#date_group_1').css('display','none');
            $('#date_group_2').css('display','none');
            // $('hr.date-separate').addClass('dn');


            $('#date_input_1').rules("add", 
            {
                required: false
            });

            $('#date_input_2').rules("add", 
            {
                required: false
            });
          break;

    }
  }

  calDiscount() {

    clearTimeout(this.handle);

    if(
        (typeof $('#price_input').val() == 'undefined') || ($('#price_input').val() < 1) 
        ||
        (typeof $('#original_price_input').val() == 'undefined') || ($('#original_price_input').val() < 1)
      ) {
      return false;
    }

    let price = $('#price_input').val().replace(/,/g,'');
    let originalPrice = $('#original_price_input').val().replace(/,/g,'');

    if(price - originalPrice > 0) {
      $('#percent_input').val(0);
      return false;
    }

    this.handle = setTimeout(function(){
      let percent = 100 - ((price * 100) / originalPrice);
      $('#percent_input').val(Math.round(percent,2));
    },300);

  }

  disableContact(checked) {
    if(checked) {
      $('textarea[name="contact"]').prop('disabled',true).addClass('dn');
    }else {
      $('textarea[name="contact"]').prop('disabled',false).removeClass('dn');
    }
  }

  disableLocation(checked) {
    if(checked) {
      $('#location_selecting_box').addClass('dn');
      $('input[name="ItemToLocation[location_id]"]').prop('disabled',true);
    }else {
      $('#location_selecting_box').removeClass('dn');
      $('input[name="ItemToLocation[location_id]"]').prop('disabled',false);
    }
  }

  // usingDateRule() {

  //   switch($('#date_type_select').val()) {

  //     case '1':

  //         $('#date_input_1').rules("add", 
  //         {
  //             required: false
  //         });

  //         $('#date_input_2').rules("add", 
  //         {
  //             required: true
  //         });

  //       break;
  //     case '2':

  //         $('#date_input_1').rules("add", 
  //         {
  //             required: true
  //         });

  //         $('#date_input_2').rules("add", 
  //         {
  //             required: false
  //         });

  //       break;
  //     case '3':

  //         $('#date_input_1').rules("add", 
  //         {
  //             required: true
  //         });

  //         $('#date_input_2').rules("add", 
  //         {
  //             required: true
  //         });

  //       break;
  //     default:

  //         $('#date_input_1').rules("add", 
  //         {
  //             required: false
  //         });

  //         $('#date_input_2').rules("add", 
  //         {
  //             required: false
  //         });

  //       break;

  //   }

  // }

}