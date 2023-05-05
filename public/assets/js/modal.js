class ModalDialog {
  
  constructor() {
    if(!ModalDialog.instance){
      this.obj = [];
      this.stopClick = false;
      this.zIndex = 65;
      ModalDialog.instance = this;
    }

    return ModalDialog.instance;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-toggle="modal"]',function(e){
      e.preventDefault();

      _this.show($(this).data('c-modal-target'));
    });

    $('body').on('click','[data-close="modal"]',function(e){
      if(_this.stopClick) {
        return false;
      }

      _this.close();

      _this.stopClick = true;
      setTimeout(function(){
        _this.stopClick = false;
      },250);
    });

    $('body').on('click','[data-close="all-modal"]',function(e){
      if(_this.stopClick) {
        return false;
      }

      _this.closeAll();

      _this.stopClick = true;
      setTimeout(function(){
        _this.stopClick = false;
      },250);
    });

    $('body').on('click','.c-modal > .close',function(){

      if(_this.stopClick) {
        return false;
      }

      _this.close();

      _this.stopClick = true;
      setTimeout(function(){
        _this.stopClick = false;
      },250);
    });

    $('body').on('click','.c-modal .modal-close',function(){

      if(_this.stopClick) {
        return false;
      }
      
      _this.close();

      _this.stopClick = true;
      setTimeout(function(){
        _this.stopClick = false;
      },250);
    });

  }

  show(target) {
    this.obj.push(target);

    if(this.obj.length === 1) {
      $('body').addClass('overflow-y-hidden');
    }
    // else if(this.obj.length > 1) {
    //   $(this.obj[this.obj.length-2]).find('.c-modal-inner').addClass('overflow-y-hidden');
    // }

    $(target).addClass('show');
    $(target).css('z-index',++this.zIndex);
  }

  close() {

    if(this.obj.length > 0) {
      let elem = this.obj.pop();

      if(this.obj.length === 0) {
        $('body').removeClass('overflow-y-hidden');
      }

      $(elem).removeClass('show');
      $(elem).css('z-index',--this.zIndex);
      
      this.removeElem(elem);
    }
    
  }

  closeAll() {

    for (var i = 0; i < this.obj.length; i++) {
      this.removeElem(this.obj[i]);
    }

    this.obj = [];
    this.zIndex = 65;

    $('.c-modal').removeClass('show');
    $('body').removeClass('overflow-y-hidden');
  }

  removeElem(elem) {
    if($(elem).data('remove-modal-elem') == 1) {
      setTimeout(function(){
        $(elem).remove();
      },500);
    }
  }

  create(title,message,type = 'popup') {

    let id = 'modal_'+Token.generateToken(7);

    let style = '';
    switch(type) {
      case 'full':
        style = 'w-100 h-100';
      break;

      case 'paper':
        style = 'w-100 w-70-ns h-100';
      break;

      // case 'popup-error':
      //   style = 'modal-error';
      // break;

    }

    let html = '';

    switch(type) {

      case 'popup-success':
        html = `
        <div id="${id}" class="c-modal" data-remove-modal-elem="1">
          <a class="close"></a>
          <div class="c-modal-inner tc">

            <a class="modal-close">
              <span aria-hidden="true">&times;</span>
            </a>

            <i class="fas fa-check green f-6 mb4"></i>

            <h3 class="dark-green mb3 mb4-ns">${title}</h3>
            <h4 class="item-title f4">${message}</h4>

          </div>
        </div>
        `;
        break;

      case 'popup-error':
        html = `
        <div id="${id}" class="c-modal" data-remove-modal-elem="1">
          <a class="close"></a>
          <div class="c-modal-inner tc">

            <a class="modal-close">
              <span aria-hidden="true">&times;</span>
            </a>

            <i class="fas fa-exclamation red f-6 mb4"></i>

            <h3 class="dark-red mb3 mb4-ns">${title}</h3>
            <h4 class="item-title f4">${message}</h4>

          </div>
        </div>
        `;
        break;

      default:
        html = `
        <div id="${id}" class="c-modal" data-remove-modal-elem="1">
          <a class="close"></a>
          <div class="c-modal-inner ${style}">

            <a class="modal-close">
              <span aria-hidden="true">&times;</span>
            </a>

            <h4 class="item-title f4 f3-ns mb3 mb4-ns">${title}</h4>

            ${message}

          </div>
        </div>
        `;
        break;

    }

    // if(type == 'popup-error') {

    //   html = `
    //   <div id="${id}" class="c-modal" data-remove-modal-elem="1">
    //     <a class="close"></a>
    //     <div class="c-modal-inner tc">

    //       <a class="modal-close">
    //         <span aria-hidden="true">&times;</span>
    //       </a>

    //       <i class="fas fa-exclamation red f-6 mb4"></i>

    //       <h3 class="dark-red mb3 mb4-ns">${title}</h3>
    //       <h4 class="item-title f4">${message}</h4>

    //     </div>
    //   </div>
    //   `;

    // }else {
    //   html = `
    //   <div id="${id}" class="c-modal" data-remove-modal-elem="1">
    //     <a class="close"></a>
    //     <div class="c-modal-inner ${style}">

    //       <a class="modal-close">
    //         <span aria-hidden="true">&times;</span>
    //       </a>

    //       <h4 class="item-title f4 f3-ns mb3 mb4-ns">${title}</h4>

    //       ${message}

    //     </div>
    //   </div>
    //   `;
    // }

    $('body').append(html);

    return '#'+id;
  }

}