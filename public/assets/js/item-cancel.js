class ItemCancel {

  constructor() {
    this.item;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-t-cancel-modal="1"]',function(e){
      
      $('.item-title').text($(this).data('t-title'));

      _this.item = $(this).data('t-id');

      $('#cancel_option_1').trigger('click');

      const _modal = new ModalDialog();
      _modal.show('#cancel_item_modal');
    });

    $('#cancel_item_modal > .close').on('click',function(){
      _this.clear();
    });

    $('#cancel_item_modal .modal-close').on('click',function(){
      _this.clear();
    });

    $('.close-option').on('click',function(){

      switch($(this).val()) {

        case '3':
          $('#cancel_reason').addClass('show').focus();
        break;

        default:
          $('#cancel_reason').removeClass('show').removeClass('error');
        break;

      }

    });

    $('#cancel_item_form').on('submit',function(){

      switch($('.close-option:checked').val()) {
        case '3':

          if($('#cancel_reason').val().trim() === '') {
            $('#cancel_reason').addClass('error');
            return false;
          }

        break;
      }

      let hidden = document.createElement('input');
      hidden.setAttribute('type','hidden');
      hidden.setAttribute('name','itemId');
      hidden.setAttribute('value',_this.item);
      $(this).append(hidden);

    });

  }

  clear() {
    $('.item-title').text('');
    $('#cancel_reason').removeClass('show').removeClass('error').val('');
  }

}