var Validation = function () {

    return {
        
        //Validation
        initValidation: function () {

          $.validator.addMethod("regx", function(value, element, regexpr) {          
              return regexpr.test(value);
          }, '');

          $.validator.addMethod("checkDateRequired", function(value, element, params) {

            if($(params).hasClass('date-required') && ($(params).val() == '')) {
              return false;
            }

            return true;
          }, '');

          $.validator.addMethod("greaterThan", function(value, element, params) {
            
            if(($(params).val() === '') || ($(element).val() === '')) {
              return true;
            }

            if(DateTime.dateToTimestamp($(params).val()) > DateTime.dateToTimestamp(value)) {
              return true;
            }

            return false;
          }, '');

	        $("#item_form").validate({  

            // ignore: '.ignore-field, :hidden, :disabled',
            ignore: ':disabled',

	          // Rules for form validation
            rules:
            {
              title:
              {
                required: true,
                maxlength: 255
              },
              description:
              {
                required: true
              },
              price:
              {
                required: true,
                regx: /^[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?$/
              },
              original_price:
              {
                regx: /^(\s?|[0-9]{1,3}(?:,?[0-9]{3})*(?:\.[0-9]{2})?)$/ // allow space
              },
              date_1:
              {
                checkDateRequired: '#date_input_1',
                date: true,
                greaterThan: '#date_input_2'
              },
              date_2:
              {
                checkDateRequired: '#date_input_2',
                // required: true,
                date: true
              },
              contact:
              {
                required: true
              },
              'ItemToCategory[category_id]':
              {
                required: true,
                number: true
              },
              'ItemToLocation[location_id]':
              {
                required: true,
                number: true
              },
              'Preview[filename]':
              {
                required: true,
              },
              // 'quantity':
              // {
              //   required: true,
              //   number: true
              // }
            },
                                
            // Messages for form validation
            messages:
            {
              title:
              {
                required: 'ยังไม่ได้ป้อนหัวข้อ / ชื่อสินค้า',
                maxlength: 'จำนวนตัวอักษรเกินกว่าที่กำหนด'
              },
              description:
              {
                required: 'ยังไม่ได้ป้อนรายละเอียด'
              },
              price:
              {
                required: 'ยังไม่ได้ป้อนราคาขาย',
                regx: 'รูปแบบราคาไม่ถูกต้อง'
              },
              original_price:
              {
                regx: 'รูปแบบราคาไม่ถูกต้อง'
              },
              date_1:
              {
                checkDateRequired: 'ยังไม่ได้ป้อนวันที่ใช้งาน',
                date: 'วันที่ไม่ถูกต้อง',
                greaterThan: 'ไม่อนุญาตให้กรอกวันที่เริ่มต้นมากกว่าหรือเท่ากับวันที่สิ้นสุด'
              },
              date_2:
              {
                // required: 'ยังไม่ได้ป้อนวันที่ใช้งาน',
                checkDateRequired: 'ยังไม่ได้ป้อนวันที่',
                date: 'วันที่ไม่ถูกต้อง'
              },
              contact:
              {
                required: 'ยังไม่ได้ป้อนช่องทางการติดต่อ'
              },
              'ItemToCategory[category_id]':
              {
                required: 'ยังไม่ได้เลือกหมวดหมู่ของสินค้า',
                number: 'ข้อมูลไม่ถูกต้อง'
              },
              'ItemToLocation[location_id]':
              {
                required: 'ยังไม่ได้ระบุตำแหน่งสินค้า',
                number: 'ข้อมูลไม่ถูกต้อง'
              },
              'Preview[filename]':
              {
                required: 'ยังไม่ได้เพิ่มรูปภาพ Preview',
              },
              // 'quantity':
              // {
              //   required: 'ยังไม่ได้ระบุจำนวนสินค้า',
              //   number: 'จำนวนสินค้าไม่ถูกต้อง'
              // },
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