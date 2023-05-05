class PageViewingHistory {

  constructor(token,model,modelId,pageId) {
    this.token = token;
    this.model = model;
    this.modelId = modelId;
    this.pageId = pageId;
    this.timeout = 15000;
  }

  record() {

    let _this = this;

    setTimeout(function(){

      let request = $.ajax({
        url: "/page-record",
        type: "POST",
        headers: {
          'x-csrf-token': _this.token
        },
        data: {
          model: _this.model,
          modelId: _this.modelId,
          pageId: _this.pageId
        },
        dataType: 'json',
        // contentType: false,
        // cache: false,
        // processData:false,
      });

      request.done(function (response, textStatus, jqXHR){

      });

      request.fail(function (jqXHR, textStatus, errorThrown){
        console.error(
            "The following error occurred: "+
            textStatus, errorThrown
        );
      });

    },this.timeout);

  }

}