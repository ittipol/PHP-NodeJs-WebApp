class UserBlocking {

  constructor() {
    this.io;
  }

  init() {

    this.io = new IO();

    this.bind();
    this.socketEvent();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-blocking="1"]',function(e){
      e.preventDefault();

      Loading.show();

      _this.io.socket.emit('blocking', {
        blockedType: $(this).data('blocked-type'),
        blockedId: $(this).data('blocked-id'),
      });
    });

    $('body').on('click','[data-remove-blocking="1"]',function(e){
      e.preventDefault();

      Loading.show();

      _this.io.socket.emit('remove-blocking', {
        blockedType: $(this).data('blocked-type'),
        blockedId: $(this).data('blocked-id'),
      });
    });

  }

  socketEvent() {

    let _this = this;

    this.io.socket.on('blocked', function(res){

      setTimeout(function(){  

        let elem = $('[data-blocking-ident="'+res.type+'_'+res.id+'"]');
        
        if(elem.length == 0) {
          return false;
        }

        if(res.blocked) {

          $(elem).find('.user-blocking-icon').html('<i class="fas fa-stop"></i>');
          $(elem).find('.user-blocking-label').text(_this.getLabel(res.blocked,res.type));
        
          const snackbar = new Snackbar();
          snackbar.setTitle('ไม่สนใจรายการขายแล้ว');
          snackbar.display();

          $('#blocking_alert').find('.alert-heading').text(_this.getAlertLabel(res.blocked,res.type));
          $('#blocking_alert').removeClass('dn').addClass('db');

        }else {

          $(elem).find('.user-blocking-icon').html('<i class="fas fa-ban"></i>');
          $(elem).find('.user-blocking-label').text(_this.getLabel(res.blocked,res.type));
        
          const snackbar = new Snackbar();
          snackbar.setTitle('ยกเลิกไม่สนใจรายการขายแล้ว');
          snackbar.display();

          $('#blocking_alert').removeClass('db').addClass('dn');
          $('#blocking_alert').find('.alert-heading').text(_this.getAlertLabel(res.blocked,res.type));
          
        }

        Loading.hide();
      },1200);

    });

    this.io.socket.on('blocking-removed', function(res){

      let elem = $('[data-blocking-ident="'+res.type+'_'+res.id+'"]');
      
      if(elem.length == 0) {
        return false;
      }

      elem.remove();

      if($('.blocked-item').length == 0) {
        $('#blocking_data_list').html('<div class="c-form-container mv7"><div class="message-panel tc"><div class="center w-90 w-100-ns"><h5>ยังไม่มีรายการที่ไม่สนใจ</h5></div></div></div>');
      }

      const snackbar = new Snackbar();
      snackbar.setTitle('ยกเลิกไม่สนใจรายการขายแล้ว');
      snackbar.display();

      setTimeout(function(){  
        Loading.hide();
      },1000);
    });

  }

  getLabel(blocked,type) {
    switch(type) {
      case "User":

        if(blocked) {
          return 'ยกเลิกไม่สนใจรายการขายผู้ใช้รายนี้';
        }

        return 'ไม่สนใจรายการขายผู้ใช้รายนี้';
        
      break;

      case "Item":
        
        if(blocked) {
          return 'ยกเลิกไม่สนใจรายการขายนี้';
        }

        return 'ไม่สนใจรายการขายนี้';

      break;

      case "Shop":
        
        if(blocked) {
          return 'ยกเลิกไม่สนใจรายการขายจากร้านนี้';
        }

        return 'ไม่สนใจรายการขายจากร้านนี้';

      break;
    }
  }

  getAlertLabel(blocked,type) {
    switch(type) {
      case "User":

        if(blocked) {
          return 'คุณไม่สนใจรายการขายผู้ใช้รายนี้';
        }

        return '';
        
      break;

      case "Item":
        
        if(blocked) {
          return 'คุณไม่สนใจรายการขายนี้';
        }

        return '';

      break;

      case "Shop":
        
        if(blocked) {
          return 'คุณไม่สนใจรายการขายจากร้านนี้';
        }

        return '';

      break;
    }
  }

}