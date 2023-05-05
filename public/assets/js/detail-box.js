class DetailBox {
  constructor(token) {
    this.token = token;
  }

  init() {
    this.bind();
  }

  bind() {

    let _this = this;

    $('body').on('click','[data-detail-box="1"]',function(e){
      e.preventDefault();

      Loading.show();
      _this.show();

      _this.load($(this).data('detail-id'));
    });

    $('#model_detail_box > .close').on('click',function(){
      _this.clear();
    });

    $('#model_detail_box .modal-close').on('click',function(){
      _this.clear();
    });

  }

  clear() {
    $('#detail_box_panel').text('');
  }

  load(id) {

    let _this = this;

    let request = $.ajax({
      url: "/ticket-detail",
      type: "POST",
      headers: {
        'x-csrf-token': this.token
      },
      data: this.createFormData(id),
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false,
      // beforeSend: function( xhr ) {},
      mimeType:"multipart/form-data"
    });

    request.done(function (response, textStatus, jqXHR){
      
      if(response.success) {

        $('#detail_box_panel').html(response.html);

        setTimeout(function(){
          Loading.hide();
        },200);

      }else {

        const snackbar = new Snackbar();
        snackbar.setTitle(response.errorMessage);
        snackbar.display();
        
        setTimeout(function(){
          Loading.hide();
          _this.close()
        },1000);

      }

    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  createFormData(id) {
    let formData = new FormData(); 
    formData.append('id', id);
    return formData;
  }

  show() {
    let modal = new ModalDialog();
    modal.show('#model_detail_box');
  }

  close() {
    let modal = new ModalDialog();
    modal.close();
  }

}