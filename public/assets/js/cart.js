class Cart {
  
  constructor(token) { 
    this.token = token;
    this.updating = false; 
  }

  init() {
    this.bind();
  	this.renderItemInCart();
    this.cartSummaryInfo();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-cart="add"]',function(e){
      e.preventDefault();
      _this.addItemToCart($(this).data('cart-item-id'),1);
    }); 

    // $('body').on('click','[data-cart="update"]',function(e){
    //   e.preventDefault();
    // });

    $('body').on('keypress','[data-cart="update"]',function(e){

      _this.updating = true;

      if (e.keyCode == 13) {
        e.preventDefault();

        if(_this.checkQty($(this).val())) {

          if(_this.updating) {
            _this.updateItemInCart($(this).data('cart-item-id'),$(this).val());
            _this.updating = false;  
          }
          
        }else {
          snackbar.setTitle('จำนวนสินค้าไม่ถูกต้อง');
          snackbar.display();          
        }

        return false;
      }
    });

    // $('body').on('focus','[data-cart="update"]',function(e){
    //   e.preventDefault();
    // });

    $(document).on('blur','[data-cart="update"]',function(e){
      e.preventDefault();

      if(_this.checkQty($(this).val())) {
        
        if(_this.updating) {
          _this.updateItemInCart($(this).data('cart-item-id'),$(this).val());
          _this.updating = false;  
        }

      }else {
        snackbar.setTitle('จำนวนสินค้าไม่ถูกต้อง');
        snackbar.display();          
      }
    });  

    // $('body').on('focusout','[data-cart="update"]',function(e){
    //   e.preventDefault();

    //   if(_this.checkQty($(this).val())) {
        
    //     if(_this.updating) {
    //       _this.updateItemInCart($(this).data('cart-item-id'),$(this).val());
    //       _this.updating = false;  
    //     }

    //   }else {
    //     snackbar.setTitle('จำนวนสินค้าไม่ถูกต้อง');
    //     snackbar.display();          
    //   }
    // }); 

    $('body').on('click','[data-cart="delete"]',function(e){
      e.preventDefault();
      _this.deleteItemInCart($(this).data('cart-token'));
    });

    $('#cart_check_out_btn').on('click',function(e){
      e.preventDefault();
      _this.checkout();
    });

  }

  checkQty(qty) {
    qty = Math.floor(qty);

    return !isNaN(qty) && ((qty > 0) && (qty <= 1000000));
  }

  renderItemInCart() {

    let _this = this;

  	let request = $.ajax({
  	  url: "/cart",
  	  type: "GET",
  	  dataType: 'json',
  	  // contentType: false,
  	  // cache: false,
  	  // processData:false,
  	});

  	request.done(function (response, textStatus, jqXHR){
      $('#cart_list').html(response.html);
  	});

  	request.fail(function (jqXHR, textStatus, errorThrown){
  	  console.error(
  	      "The following error occurred: "+
  	      textStatus, errorThrown
  	  );
  	});
  }

  // render(data) {
  //   return `<div class="cart-card-item cart-card-item-error clearfix" data-cart-id="${data.token}" >
  //       <div class="alert alert-danger" role="alert">
  //         <strong>Oh snap!</strong> <a href="#" class="alert-link">Change a few things up</a> and try submitting again.
  //       </div>
  //       <div class="cart-card-item-left fl">
  //         <a href="javascript:void(0);" data-cart="delete" data-cart-token="${data.token}">
  //           <i class="fas fa-minus"></i>
  //         </a>
  //       </div>
  //       <div class="cart-card-item-right fl">
  //         <div class="cart-card-item-inner clearfix">
  //           <div class="item-image-frame fl">
  //             <div class="item-image" style="background-image: url('${data.image._preview_url}')"></div>
  //           </div>
  //           <div class="item-content fl">
  //             <div class="item-primary-text">${data.name}</div>
  //             <div class="item-secondary-text">${data.price.price}</div>
  //             <div class="item-quantity">
  //               <input type="text" data-cart="update" data-cart-item-id="${data.itemId}" value="${data.quantity}">
  //               <i class="fas fa-caret-down"></i>
  //             </div>
  //           </div>
  //         </div>
  //       </div>
  //     </div>`;
  // }

  addItemToCart(itemId,qty) {

    let _this = this;

    let request = $.ajax({
      url: "/cart/add/"+itemId,
      type: "POST",
      headers: {
        'x-csrf-token': this.token
      },
      data: {
        'qty': qty
      },
      dataType: 'json',
      // contentType: false,
      // cache: false,
      // processData:false
      beforeSend: function( xhr ) {
        Loading.show();
      }
    });

    request.done(function (response, textStatus, jqXHR){

      if(!response.error) {
        snackbar.setTitle('<i class="fas fa-cart-plus"></i> สินค้าถูกเพิ่มลงในตระกร้าแล้ว');
        snackbar.display();

        _modal.close()
      }else {
        let messages = '';
        for (var i = 0; i < response.messages.length; i++) {
          messages += response.messages[i].message
        }

        _modal.close()

        _modal.show(_modal.create(messages,'ไม่สามารถเพิ่มสินค้าลงในตระกร้าได้','popup-error'));
      }

      _this.renderItemInCart();
      _this.cartSummaryInfo();

      setTimeout(function(){
        Loading.hide();  
      },1000);
      
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  updateItemInCart(itemId,qty) {

    let _this = this;

    let request = $.ajax({
      url: "/cart/update/"+itemId,
      type: "POST",
      headers: {
        'x-csrf-token': this.token
      },
      dataType: 'json',
      data: {
        'qty': qty
      },
      // contentType: false,
      // cache: false,
      // processData:false,
      beforeSend: function( xhr ) {
        Loading.show();
      }
    });

    request.done(function (response, textStatus, jqXHR){

      if(!response.error) {
        snackbar.setTitle('<i class="fas fa-cart-plus"></i> ปรับปรุงจำนวนสินค้าแล้ว');
        snackbar.display();
      }else {
        let messages = '';
        for (var i = 0; i < response.messages.length; i++) {
          messages += response.messages[i].message
        }

        _modal.show(_modal.create(messages,'ไม่สามารถเพิ่มสินค้าลงในตระกร้าได้','popup-error'));
      }

      _this.renderItemInCart();
      _this.cartSummaryInfo();

      Loading.hide();
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  deleteItemInCart(token) {

    let _this = this;

    let request = $.ajax({
      url: "/cart/delete",
      type: "POST",
      headers: {
        'x-csrf-token': this.token
      },
      dataType: 'json',
      data: {
        token: token
      },
      beforeSend: function( xhr ) {
        Loading.show();
      }
      // contentType: false,
      // cache: false,
      // processData:false,
    });

    request.done(function (response, textStatus, jqXHR){

      $('[data-cart-id="'+token+'"]').remove();

      if(response.empty) {
        $('#cart_list').html('<div class="cart-empty-text mt5 tc f4">ไม่มีสินค้า</div>');
      }

      _this.cartSummaryInfo();

      setTimeout(function(){
        Loading.hide();
      },1000);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  cartSummaryInfo() {

    let _this = this;

    let request = $.ajax({
      url: "/cart/info",
      type: "GET",
      dataType: 'json',
      // contentType: false,
      // cache: false,
      // processData:false,
    });

    request.done(function (response, textStatus, jqXHR){

      $('#cart_badge').text(response.amount);
      $('#modal_cart_item_amount').text(response.amount);

      if(response.quantity > 0) {
        $('#cart_summary').html(_this.renderItemQuantity(response.quantity));
      }else {
        $('#cart_summary').text('');
      }

      for (var i = 0; i < response.summary.length; i++) {
        $('#cart_summary').append(_this.renderSummary(response.summary[i]));
      }
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  renderItemQuantity(quantity) {

    return `<div class="cart-summary-row">
                <div class="cart-summary-item">
                  <div class="row">
                    <div class="cart-summart-value-title w-50">
                      จำนวนสินค้าทั้งหมด
                    </div>
                    <div class="w-50">
                      <div class="cart-summart-value tr">${quantity}</div>
                    </div>
                  </div>
                </div>
              </div><hr>`;

  }

  renderSummary(summary) {

    return `<div class="cart-summary-row">
      <div class="cart-summary-item ${summary.class}">
        <div class="row">
          <div class="cart-summart-value-title w-50">
            ${summary.title}
          </div>
          <div class="w-50">
            <div class="cart-summart-value tr">${summary.value}</div>
          </div>
        </div>
      </div>`;
  }

  checkout() {

    let _this = this;

    let request = $.ajax({
      url: "/cart/checkout",
      type: "GET",
      headers: {
        'x-csrf-token': this.token
      },
      dataType: 'json',
      beforeSend: function( xhr ) {
        Loading.show();
      }
      // contentType: false,
      // cache: false,
      // processData:false,
    });

    request.done(function (response, textStatus, jqXHR){

      $('body').append(response.html);

      _modal.show('#modal_cart_checkout');

      setTimeout(function(){
        Loading.hide();
      },1000);
    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  // renderCheckout(data) {

  //   let html = `<div class="cart-card-item clearfix" data-cart-id="${data.token}" >
  //       <div class="cart-card-item-left fl">
  //         <a href="javascript:void(0);" data-cart="delete" data-cart-token="${data.token}">
  //           <i class="fas fa-minus"></i>
  //         </a>
  //       </div>
  //       <div class="cart-card-item-right fl">
  //         <div class="cart-card-item-inner clearfix">
  //           <div class="item-image-frame fl">
  //             <div class="item-image" style="background-image: url('${data.image._preview_url}')"></div>
  //           </div>
  //           <div class="item-content fl">
  //             <div class="item-primary-text">${data.name}</div>
  //             <div class="item-secondary-text">${data.price.price}</div>
  //             <div class="item-quantity">
  //               <div>${data.quantity} ชิ้น</div>
  //             </div>
  //           </div>
  //         </div>
  //       </div>
  //     </div>`;

  //   return html;
  // }

}