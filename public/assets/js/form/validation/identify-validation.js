var Validation = function () {

    return {
        
        //Validation
        initValidation: function () {

	        $("#identify_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':hidden, :disabled',

	          // Rules for form validation
            rules:
            {
              email:
              {
                required: true,
                email: true,
                maxlength: 255,
              }
            },
                                
            // Messages for form validation
            messages:
            {
              email:
              {
                required: 'โปรดป้อนอีเมลของคุณเพื่อร้องขอการรีเซ็ตรหัสผ่าน',
                email: 'อีเมลไม่ถูกต้อง',
                maxlength: 'จำนวนตัวอักษรเกินกว่าที่กำหนด'
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