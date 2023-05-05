class CategoryList {

  constructor(topElem,searchData,queryString) {
    this.topElem = topElem;
    this.searchData = searchData;
    this.queryString = queryString;
    this.selectedElem;
    this.selectedData = null;
    this.dataPaths = [];
    this.prevId = [];
    this.currId;
    this.currListGroup;
    this.level = 0;
    this.chanel = null;
    this.allowedClick = true;
    this.render = false;
  }

  init() {

    this.io = new IO();

    this.chanel = 'category-list.'+Token.generateToken()+this.io.token;
    this.io.join(this.chanel);

    this.bind();
    this.socketEvent();

    let user = new User();
    if(user.check() == null) {
      this.startRender();
    }

  }

  bind() {

    let _this = this;

    $(this.topElem).on('click','.list-next-btn',function(){

      if(!_this.allowedClick) {
        return false;
      }

      Loading.show();

      _this.allowedClick = false;

      // _this.selectedData = $(this).data('id');

      if(_this.level > 0) {
        _this.prevId.push(_this.currId);
      }

      // _this.addCatPath($(this).data('name'));

      _this.currId = $(this).data('id');

      _this.level++;
      _this.getData($(this).data('id'));

    });

    $(this.topElem).on('click','.list-back-btn',function(){

      if(!_this.allowedClick) {
        return false;
      }

      Loading.show();

      _this.allowedClick = false;

      if(--_this.level > 0) {
        _this.currId = _this.prevId.pop();
        _this.getData(_this.currId);
      }else{
        _this.level = 0;
        _this.getData();
      }

    });

  }

  socketEvent() {

    let _this = this;
    
    this.io.socket.on('get-category-list', function(res){
      _this.createItemList(res.data);

      Loading.hide();

      setTimeout(function(){
        _this.allowedClick = true;
      },400);
    });

    this.io.socket.on('after-online', function(res){
      _this.startRender();
    });

  }

  createItemList(data) {

    $(this.topElem).find('.list-item-panel').removeClass('show');
    // $(this.topElem).find('.list-item-panel').text('');

    let listGroup = document.createElement('div');
    listGroup.setAttribute('class','list-item-row');
    // listGroup.style.display = 'none';

    this.currListGroup = listGroup;

    if(this.level > 0) {
      listGroup.append(this.createBackBtn());
    }

    for (var i = 0; i < data.length; i++) {
      listGroup.append(this.createList(data[i]['id'],data[i]['name'],data[i]['slug'],data[i]['hasChild'],data[i]['total'],data[i]['image']));
    };

    $(this.topElem).find('.list-item-panel').html(listGroup);
    $(this.topElem).find('.list-item-panel').addClass('show');
    // $(listGroup).delay(30).fadeIn(220);
  }

  createList(id,name,slug,next,total,image) {

    let list = document.createElement('div');

    let cssClass = 'list-item edge'; 

    if(this.selectedData == id) {
      cssClass += ' selected';
      this.selectedElem = list;
    }

    let html = '<div class="list-item-label"><a href="/category/'+slug+this.queryString+'"><img class="list-image" src="/assets/images/catagory/'+image+'">'+name+'<small class="f6 dark-green">'+total+'</small></a>';

    if(next) {
      html += '<div class="list-next-btn" data-id="'+id+'"><i class="fas fa-chevron-right"></i></div>';
    }

    html += '</div>';

    list.setAttribute('class',cssClass);

    list.innerHTML = html;

    return list;
  }

  createBackBtn() {

    let btn = document.createElement('div');
    btn.setAttribute('class','list-item edge list-back-btn');

    let html = '';
    html += '<div class="mb0"><i class="fa fa-chevron-left" aria-hidden="true"></i> กลับ</div>';

    btn.innerHTML = html;

    return btn;
  }

  startRender() {
    if(!this.render) {
      Loading.hide();
    }

    this.render = true;

    if(this.selectedData === null) {
      this.getData();
    }else {
      this.renderPath(this.dataPaths);
    }
  }

  setDataId(dataId = null) {
    if(dataId != null) {
      this.selectedData = dataId;
    }
  }

  setDataPath(dataPaths = null) {
    if(dataPaths != null) {
      this.dataPaths = dataPaths;
    }
  }

  renderPath(dataPaths = []) {

    if(!this.render) {
      Loading.show();
      return false;
    }

    if(dataPaths == null) {
      this.getData();
    }else {
      for (var i = 0; i < dataPaths.length; i++) {
        if(this.level > 0) {
          this.prevId.push(this.currId);
        }

        // this.addCatPath(dataPaths[i]['name']);
        this.currId = dataPaths[i]['id'];

        this.level++;

        if(i == (dataPaths.length-1)) {

          if(--this.level > 0){
            this.currId = this.prevId.pop();
            this.getData(this.currId);
          }else{
            this.level = 0;
            this.getData();
          }

          // if(dataPaths[i]['hasChild']) {
          //   this.getData(dataPaths[i]['id']);
          // }else if(--this.level > 0){
          //   this.currId = this.prevId.pop();
          //   this.getData(this.currId);
          // }else{
          //   this.level = 0;
          //   this.getData();
          // }
          
        }
        
      }
    }

  }

  getData(parentId = null){

    if(typeof this.currListGroup !== 'undefined') {

      let currListGroup = $(this.currListGroup);
      currListGroup.delay(400).fadeOut(200);

      setTimeout(function(){
        currListGroup.remove();
      },100);

    }

    this.io.socket.emit('get-category-list', {
      chanel: this.chanel,
      parentId: parentId,
      queryString: this.searchData
    });

  }

}