var Validation = function () {

    return {
        
        //Validation
        initValidation: function () {

	        $("#shop_create_form").validate({  

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
              contact:
              {
                required: true
              },
              'ShopToCategory[category_id][]':
              {
                required: true
              },
              'ItemToLocation[location_id]':
              {
                number: true
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
              contact:
              {
                required: 'ยังไม่ได้ป้อนช่องทางการติดต่อ'
              },
              'ShopToCategory[category_id][]':
              {
                required: 'ยังไม่ได้เลือกประเภทสินค้าที่ขายในร้านขายสินค้า'
              },
              'ItemToLocation[location_id]':
              {
                number: 'ข้อมูลไม่ถูกต้อง'
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
              if($(element).data('c-error') !== undefined) {
                $($(element).data('c-error')).append(error);
              }else {
                error.insertAfter(element.parent());
              }
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