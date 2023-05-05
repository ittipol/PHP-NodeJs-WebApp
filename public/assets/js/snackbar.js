class Snackbar {
  
  constructor() {
    if(!Snackbar.instance){
      this.title = '';
      // this.desc = '';
      // this.type = 'info';
      // this.layout = 'small';
      this.handle = null;
      this.delay = 4500;
      this.alwaysVisible = false;
      this.allowedClose = true;
      this.stopClick = false;
      Snackbar.instance = this;
    }

    return Snackbar.instance;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    // $('#snackbar_close').on('click', function(){
    //   $('#snackbar').stop().fadeOut(220)
    // });

    $('body').on('click','.snackbar > a.action', function(){

      // Stop Click
      if(_this.stopClick) {
        return false;
      }

      _this.stopClick = true;

      clearTimeout(_this.handle);

      $('#snackbar').css({opacity:0});

      setTimeout(function(){
        $('#snackbar').remove();
        _this.stopClick = false;
      },1000);
    });

  }

  createElement() {
    return `<div id="snackbar" class="snackbar">${this.title}</div>`;
  }

  display() {

    $('#snackbar').remove(); 
    $('body').append(this.createElement());

    setTimeout(function(){
      // $('#snackbar').css({bottom:50,opacity:1});
      $('#snackbar').addClass('show');
    },540);

    if(!this.alwaysVisible) {
      clearTimeout(this.handle);

      this.handle = setTimeout(function(){
        // $('#snackbar').css({bottom:-140,opacity:0});
        $('#snackbar').removeClass('show');
      },this.delay);
    }
    
  }

  hideNotificationBox(obj) {

    if($(obj).is(':checked')) {
      $('#snackbar').stop().css({
        bottom: 0,
        opacity: 0
      });
    }else{
      $('#snackbar').stop().css({
        bottom: 50,
        opacity: 1
      });
    }

  }

  setTitle(title) {
    this.title = title;     
  }

  // setDesc(desc) {
  //   this.desc = desc;     
  // }

  // setType(type) {
  //   this.type = type;     
  // }

  setDelay(delay) {
    this.delay = delay;
  }

  // setLayout(layout) {
  //   this.layout = layout;
  // }

  setVisible(visible) {
    this.alwaysVisible = visible;
  }

}