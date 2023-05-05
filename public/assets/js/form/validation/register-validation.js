var RegisterValidation = function () {

    return {
        
        //Validation
        initValidation: function () {

	        $("#register_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':hidden, :disabled',

	            // Rules for form validation
            rules:
            {
              name:
              {
                required: true,
                maxlength: 255,
              },
              email:
              {
                required: true,
                email: true,
                maxlength: 255,
              },
              password:
              {
              	required: true,
                minlength: 4,
              },
              password_confirmation:
              {
                equalTo : '#password_field'
              }
            },
                                
            // Messages for form validation
            messages:
            {
              name:
              {
                required: 'ยังไม่ได้ป้อนชื่อ นามสกุล',
                maxlength: 'จำนวนตัวอักษรเกินกว่าที่กำหนด'
              },
              email:
              {
                required: 'ยังไม่ได้ป้อนอีเมล',
                email: 'อีเมลไม่ถูกต้อง',
                maxlength: 'จำนวนตัวอักษรเกินกว่าที่กำหนด'
              },
              password:
              {
                required: 'รหัสผ่านห้ามว่าง',
                minlength: 'รัสผ่านต้องมีอย่างน้อย 4 อักขระ'
              },
              password_confirmation:
              {
                equalTo: 'รหัสผ่านไม่ตรงกัน'
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