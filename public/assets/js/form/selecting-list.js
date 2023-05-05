class SelectingList {

  constructor(loadedData,topElem,selectedLabel) {
    this.loadedData = loadedData;
    this.topElem = topElem;
    this.selectedLabel = selectedLabel;
    this.selectedElem;
    this.selectedData = null;
    this.catPathName = [];
    this.prevId = [];
    this.currId;
    this.currListGroup;
    this.level = 0;
    this.target = null;
    this.pipe = null;
    this.chanel = null;
    this.allowedClick = true;
  }

  init() {

    this.io = new IO();

    this.chanel = 'selcting-list.'+Token.generateToken(32)+this.io.token;
    this.io.join(this.chanel);

    this.setPipe(this.loadedData);

    this.bind();
    this.socketEvent();
  }

  bind() {

    let _this = this;

    $(this.topElem).on('click','.list-next-btn',function(){

      if(!_this.allowedClick) {
        return false;
      }

      Loading.show();

      setTimeout(function(){
        _this.allowedClick = true;
      },400);

      // _this.selectedData = $(this).data('id');

      if(_this.level > 0) {
        _this.prevId.push(_this.currId);
      }

      _this.addCatPath($(this).data('name'));

      _this.currId = $(this).data('id');

      _this.level++;
      _this.getData($(this).data('id'));

    });

    $(this.topElem).on('click','.list-back-btn',function(){

      if(!_this.allowedClick) {
        return false;
      }

      Loading.show();

      setTimeout(function(){
        _this.allowedClick = true;
      },400);

      if(--_this.level > 0) {
        _this.currId = _this.prevId.pop();
        _this.getData(_this.currId);
      }else{
        _this.level = 0;
        _this.getData();
      }

      _this.removeCatPath();
    });

    $(this.topElem).on('click','.list-value',function(){

      if(!_this.allowedClick) {
        return false;
      }

      setTimeout(function(){
        _this.allowedClick = true;
      },400);
      
      // remove prev before
      $(_this.topElem).find('.selected').removeClass('selected');
      // Set Selected Icon
      $(this).parent().parent().addClass('selected');

      // if(_this.level > 0) {
      //   _this.prevId.push(_this.currId);
      // }

      _this.selectedData = $(this).data('id');

      _this.addCatPath($(this).data('name'));
      // _this.currId = $(this).data('id');

      _this.setSelectedLabel();
      
       $(_this.topElem).find('input[type="hidden"]').val($(this).data('id'));

      _this.close();

    });

    $('.selecting-lable-box').on('click','.selected-value-delete',function(){

      if($(_this.selectedLabel).data('selecting-empty-label') == 'undefined') {
        $(_this.selectedLabel).text('เลือก...');
      }else {
        $(_this.selectedLabel).text($(_this.selectedLabel).data('selecting-empty-label'));
      }

      _this.selectedData = null;

      $(_this.topElem).find('.selected').removeClass('selected');

      $(_this.topElem).find('input[type="hidden"]').val('');
    });

  }

  socketEvent() {

    let _this = this;

    this.io.socket.on(this.pipe, function(res){
      _this.createListGroup(res.data);

      Loading.hide();

      setTimeout(function(){
        _this.allowedClick = true;
      },400);
    });

  }

  setDataId(dataId = null) {
    if(dataId != null) {
      this.selectedData = dataId;
    }
  }

  setDataPath(dataPaths = []) {

    if(dataPaths == null) {
      this.getData();
    }else {
      for (var i = 0; i < dataPaths.length; i++) {
        if(this.level > 0) {
          this.prevId.push(this.currId);
        }

        this.addCatPath(dataPaths[i]['name']);
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

  addCatPath(name) {

    if(this.catPathName.length > this.level) {
      this.catPathName.splice(this.level,this.catPathName.length - this.level);
    }

    this.catPathName.push(name);

    // Display current path
    $(this.topElem).find('.selecting-lable').html(this.buildPath());
  }

  removeCatPath() {

    if(this.level == 0) {
      this.catPathName = [];
      $(this.topElem).find('.selecting-lable').html('...');
    }else {
      this.catPathName.pop();
      $(this.topElem).find('.selecting-lable').html(this.buildPath());
    }

  }

  buildPath() {
    let path = '';
    for (var i = 0; i < this.catPathName.length; i++) {

      if(i > 0) {
        path += ' » ';
      }

      if(i == (this.catPathName.length-1)) {
        path += '<span>'+this.catPathName[i]+'</span>';
      }else{
        path += this.catPathName[i];
      }
      
    }

    return path;
  }

  getData(parentId = null){

    if(typeof this.currListGroup !== 'undefined') {

      let currListGroup = $(this.currListGroup);
      currListGroup.delay(400).fadeOut(200);

      setTimeout(function(){
        currListGroup.remove();
      },100);

    }

    this.io.socket.emit(this.pipe, {
      // chanel: this.user.user+'.'+this.io.token,
      chanel: this.chanel,
      parentId: parentId
    });

  }

  createListGroup(data) {

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
      listGroup.append(this.createList(data[i]['id'],data[i]['name'],data[i]['hasChild']));
    };

    $(this.topElem).find('.list-item-panel').html(listGroup);
    $(this.topElem).find('.list-item-panel').addClass('show');
    // $(listGroup).fadeIn(250);

  }

  createList(id,name,next) {

    // let list = document.createElement('div');

    // let cssClass = 'selecting-list-item'; 

    // if(this.selectedData == id) {
    //   cssClass += ' selected';
    //   this.selectedElem = list;
    // }

    // if(next) {
    //   cssClass += ' has-next';
    // }else{
    //   cssClass += ' has-end';
    // }

    // list.setAttribute('class',cssClass);
    // list.setAttribute('data-id',id);
    // list.setAttribute('data-name',name);

    // list.innerHTML = '<h4>'+name+'</h4>';

    // return list;

    let list = document.createElement('div');

    let cssClass = 'list-item edge'; 

    if(this.selectedData == id) {
      cssClass += ' selected';
      this.selectedElem = list;
    }

    let html = '<div class="list-item-label"><a href="javascript:void(0);" data-id="'+id+'" data-name="'+name+'" class="list-value">'+name+'</a>';

    if(next) {
      html += '<div class="list-next-btn" data-id="'+id+'" data-name="'+name+'"><i class="fas fa-chevron-right"></i></div>';
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

  setPipe(data) {

    switch(data) {
      case 'category':
        this.pipe = 'get-category';
      break;

      case 'location':
        this.pipe = 'get-location';
      break;
    }

  }

  setSelectedLabel() {
    let path = this.buildPath();

    if(path == '') {
      path = $(this.selectedLabel).data('selecting-empty-label');
    }

    $(this.selectedLabel).html(path)
  }

  close() {
    const _modal = new ModalDialog();
    _modal.close();
  }

}