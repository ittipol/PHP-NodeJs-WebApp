class LeftSideBar {
  
  constructor() {
    if(!LeftSideBar.instance){
      this.currentSideBar = null;
      this.opening = false;
      LeftSideBar.instance = this;
    }

    return LeftSideBar.instance;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-toggle="left-sidebar"]',function(e){

      if(_this.opening) {
        return false;
      }

      _this.opening = true;

      setTimeout(function(){
        _this.opening = false;
      },1500);

      _this.currentSideBar = $(this).data('left-sidebar-target'); 

      // $(this).attr('disabled',true);
      $('body').css('overflow-y','hidden');
      $(_this.currentSideBar).addClass('show');  
    });

    $('body').on('click','.left-sidenav > button.close',function(e){

      if(_this.opening) {
        return false;
      }
      
      // $('#fiter_panel_toggle').attr('disabled',false);
      $('body').css('overflow-y','auto');
      $(_this.currentSideBar).removeClass('show'); 
    });

  }

}