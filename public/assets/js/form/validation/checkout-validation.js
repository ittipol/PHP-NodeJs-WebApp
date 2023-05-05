var CheckoutValidation = function () {

    return {
        
        //Validation
        initValidation: function () {

          $("#checkout_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':hidden, :disabled',

              // Rules for form validation
            rules:
            {
              buyer_name:
              {
                required: true
              },
              shipping_address:
              {
                required: true
              }
            },
                                
            // Messages for form validation
            messages:
            {
              buyer_name:
              {
                required: 'ยังไม่ได้ป้อนชื่อผู้ซื้อ'
              },
              shipping_address:
              {
                required: 'ยังไม่ได้ป้อนที่อยู่สำหรับจัดส่ง'
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