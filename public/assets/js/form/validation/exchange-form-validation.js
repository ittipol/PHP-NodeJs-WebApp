var Validation = function () {

    return {
        
        //Validation
        initValidation: function () {

          $.validator.addMethod("regx", function(value, element, regexpr) {          
              return regexpr.test(value);
          }, "");

	        $("#exchange_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':hidden, :disabled',

	            // Rules for form validation
            rules:
            {
              amount:
              {
              	required: true,
                regx: /^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/,
              }
            },
                                
            // Messages for form validation
            messages:
            {
              amount:
              {
                required: 'จำนวนเงินห้ามว่าง',
                regx: 'จำนวนเงินไม่ถูกต้อง'
              }
            },

            submitHandler: function(form) {
              $(form).find('input[type="submit"]').prop('disabled','disabled').addClass('disabled');
              Loading.show();
              $(form).get(0).submit();
            },

            errorPlacement: function(error, element)
            {
              error.insertAfter(element.parent());
            },

            showErrors: function(errorMap, errorList) {

              if(errorList.length > 0) {
                const snackbar = new Snackbar();
                snackbar.setTitle('คุณป้อนข้อมูลยังไม่ครบถ้วน');
                snackbar.display();

                this.defaultShowErrors();
              }
              
            }
	        });
        }

    };
}();