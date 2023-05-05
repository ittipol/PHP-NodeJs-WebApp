var ShippingConfirmation = function () {

    return {
        
        //Validation
        initValidation: function () {

          $("#shipping_confirmation_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':hidden, :disabled',

              // Rules for form validation
            rules:
            {
              shipping_detail:
              {
                required: true
              }
            },
                                
            // Messages for form validation
            messages:
            {
              shipping_detail:
              {
                required: 'ยังไม่ได้ป้อนรายละเอียดการจัดส่ง'
              }
            },    

            submitHandler: function(form) {
              $(form).find('input[type="submit"]').prop('disabled','disabled').addClass('disabled');
              Loading.show();
              $(form).get(0).submit();
            },            
              
            // Do not change code below
            errorPlacement: function(error, element)
            {
              error.insertAfter(element.parent());
            }
          });
        }

    };
}();