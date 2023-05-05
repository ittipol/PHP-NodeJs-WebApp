class ToastNotification {
  
  constructor() {
    if(!ToastNotification.instance){
      this.title = '';
      this.subTitle = '';
      this.image = '';
      this.button = {};
      this.handle = null;
      this.delay = 10000;
      this.alwaysVisible = false;
      this.allowedClose = true;
      this.stopClick = false;
      ToastNotification.instance = this;
    }

    return ToastNotification.instance;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    $('body').on('click','.toast-notification > a.toast-notification-close', function(){

      // Stop Click
      if(_this.stopClick) {
        return false;
      }

      _this.stopClick = true;

      clearTimeout(_this.handle);

      $('#toast_notification').css({opacity:0});

      setTimeout(function(){
        $('#toast_notification').remove();
        _this.stopClick = false;
      },1000);
    });

  }

  createElement() {

    let html = '<div id="toast_notification" class="toast-notification">';
    html += '<a class="toast-notification-close"><span aria-hidden="true">×</span></a>';
    html += '<div class="toast-notification-wrapper">';

    if(this.title != '') {
      html += '<div class="toast-notification-title">'+this.title+'</div>';
    }

    if(this.subTitle != '') {
      html += '<div class="toast-notification-sub-title">'+this.subTitle+'</div>';
    }

    if(this.image != '') {
      html += '<div class="toast-notification-image"><img src="'+this.image+'"></div>';
    }

    if(Object.keys(this.button).length > 0) {
      html += '<div class="toast-notification-button-group"><a href="'+this.button.url+'" target="'+this.button.target+'" class="btn c-btn c-btn-primary btn-block">'+this.button.label+'</a></div>';
    }

    html += '</div>';
    html += '</div>';

    return html;
  }

  show() {

    $('#toast_notification').remove(); 
    $('body').append(this.createElement());

    setTimeout(function(){
      $('#toast_notification').addClass('show');
    },4000);

    if(!this.alwaysVisible) {
      clearTimeout(this.handle);

      this.handle = setTimeout(function(){
        $('#toast_notification').removeClass('show');
      },this.delay);
    }
    
  }

  set(type,value) {

    switch(type) {

      case 'title':
        this.setTitle(value);
      break;

      case 'subTitle':
        this.setSubTitle(value);
      break;

      case 'image':
        this.setImage(value);
      break;

      case 'button':
        this.setButton(JSON.parse(value));
      break;

    }

  }

  setTitle(title) {
    this.title = title;     
  }

  setSubTitle(subTitle) {
    this.subTitle = subTitle;     
  }

  setImage(image) {
    this.image = image;     
  }

  setButton(button) {
    this.button = button;     
  }

}