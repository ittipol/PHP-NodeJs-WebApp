class ChatBox {

  constructor(token){
    this.token = token;
    this.target = null;
    this.room = null;
    this.key = null;
    this.page = 1;
    this.time = null;
    this.user = null;
    this.clearing = false;
    this.stopLoadMore = false;
    this.io = null;
  }

  init() {
    this.io = new IO();

    this.bind(this);
    this.socketEvent(this);
  }

  bind(_this) {

    $('body').on('click','[data-chat-box="1"]',function(e){
      e.preventDefault();

      // remove previous and clear data
      if(_this.target != null) {
        _this.leaveAndClose();
      }else if((typeof $(this).data('chat-close') != 'undefined') && ($(this).data('chat-close') == 1)) {
        _this.close();
      }

      _this.stopLoadMore = false;

      Loading.show();

      // if((typeof $(this).data('chat-close') != 'undefined') && ($(this).data('chat-close') == 1)) {
      //   _this.close();
      // }
      
      // _this.target = _this.create($(this).data('chat-box-title'));
      // set target directly
      // _this.target = '#chat_box';

      // Create Chat Room Or Load Message
      _this.chatRoom($(this).data('chat-data'));
    });

//     $('#chat_box').find('.close').on('click',function(){
//       _this.leave();
//       _this.close();

//       _this.clearing = true;
//     });

//     $('#chat_box').find('.modal-close').on('click',function(){
//       _this.leave();
//       _this.close();

//       _this.clearing = true;
//     });

//     $('#chat_box').find('.chat-box-input').on('keyup',function(e){

//       if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
//         // Allow: Ctrl+A, Command+A
//       (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
//         // Allow: home, end, left, right, down, up
//       (e.keyCode >= 35 && e.keyCode <= 40)) {
//         // let it happen, don't do anything
//         return false;
//       }

//       _this.typing();

//     });

//     $('#chat_box').find('.chat-box-send-btn').on('click',function(){

//       if($(id).find('.chat-box-input').val().trim() !== '') {
//         _this.sending($(id).find('.chat-box-input').val().trim());
//         _this.toButtom();
//       }

//     });

//     $('#chat_box').find('.chat-box-input').on('keypress',function(e){

//       if((e.keyCode == 13) && ($(id).find('.chat-box-input').val().trim() !== '')) {
//         _this.sending($(id).find('.chat-box-input').val().trim());
//         _this.toButtom();
//       }

//     });

    // $('#chat_box').find('.chat-box-thread').on('scroll',function(){
    //   if($(this).scrollTop() < 20) {
    //     _this.more();
    //   }
    // });

  }

  socketEvent(_this) {

    this.io.socket.on('typing', function(res){

      if(_this.user == res.user) {
        return false;
      }

      $('.typing-indicator').css('display','block');
      
      clearTimeout(_this.typingHandle);
      _this.typingHandle = setTimeout(function(){
        $('.typing-indicator').css('display','none');
      },400);

    });

    this.io.socket.on('chat-message', function(res){

      clearTimeout(this.messageReceivedHandle);

      if(_this.messagePlaced) {
        _this.messagePlaced = false;
        return false;
      }

      if(_this.user != res.user) {
        setTimeout(function(){
          _this.io.socket.emit('message-read', {
            room: _this.room,
            user: _this.user
          });
        },2000);

        _this.placeMessage(res,false);
        _this.toButtom();
      }

    });

    this.io.socket.on('chat-load-more', function(res){

      if(!res.next) {
        return false;
      }

      let me;

      _this.page = res.page;

      for (var i = 0; i < res.data.length; i++) {
        me = false;

        if(_this.user == res.data[i].user_id) {
          me = true;
        }

        $(_this.target).find('.chat-box-section').prepend(_this.getHtml(res.data[i].user_id, _this.parseMessage(res.data[i].message), res.data[i].created_at, me));
      }

      setTimeout(function(){
        _this.stopLoadMore = false;
      },1000);

    });

    this.io.socket.on('chat-error', function(res){
      if(res.error) {
        const snackbar = new Snackbar();
        snackbar.setTitle(res.message);
        snackbar.display();
      }
    });

    this.io.socket.on('after-chat-joined', function(res){
      // Load message after joining room
      _this.more();
    });

    this.io.socket.on('after-chat-leave', function(res){
      if(res.token != _this.io.token) {
        _this.chatRoomCheckUserExist();
      }
    });

    this.io.socket.on('chat-cleared', function(res){

      if(_this.clearing) {
        // _this.clearing = false;
        _this.clear();
      }
      
    });

  }

  join() {
    this.io.socket.emit('chat-join', {
      room: this.room,
      key: this.key
    });
  }

  leave() {
    this.io.socket.emit('chat-leave', {
      room: this.room,
      key: this.key
    });
  }

  chatRoomCheckUserExist() {
    this.io.socket.emit('chat-room-check-user-exist', {
      room: this.room,
      key: this.key
    }); 
  }

  more() {

    if(this.stopLoadMore) {;
      return false;
    }

    this.stopLoadMore = true;

    this.io.socket.emit('chat-load-more', {
      room: this.room,
      key: this.key,
      page: this.page,
      time: this.time // now
    });
  }

  sending(message) {

    $(this.target).find('.chat-box-input').val('');

    this.placeMessage({
      user: this.user,
      message: message.trim().stripTags()
    });

    this.messagePlaced = true;

    this.send(message);
  }

  send(message) {
    this.io.socket.emit('send-message', {
      message: message,
      room: this.room,
      key: this.key
    })
  }

  typing() {
    this.io.socket.emit('typing', {
      room: this.room,
      user: this.user,
      key: this.key
    });
  }

  getHtml(user,message,date,me = true) {

    let html = '';

    if(me) {
      html = `
      <div class="message-section message-me">
        <div class="message-box">
          ${message}
        </div>
        <small class="message-time">${DateTime.covertDateTimeToSting(date)}</small>
      </div>
      `;
    }else{
      html = `
      <div class="message-section">
        <div class="avatar">
          <img src="/avatar/${user}?d=1">
        </div>
        <div class="message-box">
          ${message}
        </div>
        <small class="message-time">${DateTime.covertDateTimeToSting(date)}</small>
      </div>
      `;
    }

    return html;

  }

  placeMessage(data,me = true) {
    $(this.target).find('.chat-box-section').append(this.getHtml(data.user,this.parseMessage(data.message), moment().tz("Asia/Bangkok").format('YYYY-MM-DD HH:mm:ss'), me));
  }

  chatRoom(data) {

    let _this = this;

    let request = $.ajax({
      url: "/chat",
      type: "POST",
      headers: {
        'x-csrf-token': this.token
      },
      data: this.createFormData(data),
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false,
      beforeSend: function( xhr ) {
        _this.clear();
        // _this.clearing = false;
      },
      mimeType:"multipart/form-data"
    });

    request.done(function (response, textStatus, jqXHR){
      
      if(response.success) {

        // loading data
        _this.room = response.room;
        _this.key = response.key;
        _this.time = response.time;
        _this.user = response.user;
        _this.page = 1;

        // joining room
        _this.join();

        //
        let _count = $('#message_notification_badge').text();
        if(_count > 1) {
          $('#message_notification_badge').text(_count-1);
        }else{
          $('#message_notification_icon').removeClass('on');
        }

        _this.target = _this.create(response.title);

        _this.show();

        // $(_this.target).find('.chat-box-input').focus();

        setTimeout(function(){
          _this.toButtom();
          Loading.hide();
        },300);

      }else {

        const snackbar = new Snackbar();
        snackbar.setTitle(response.errorMessage);
        snackbar.display();
        
        setTimeout(function(){
          // Close All Modal
          _this.close();
        },1000);

        setTimeout(function(){
          Loading.hide();
        },1500);

      }

    });

    request.fail(function (jqXHR, textStatus, errorThrown){
      console.error(
          "The following error occurred: "+
          textStatus, errorThrown
      );
    });

  }

  parseMessage(str) {
    str = this.parseUrlFromString(str);
    str = this.parseHashtagFromString(str);
    return str;
  }

  parseHashtagFromString(str) {
    const regex = /(?:#[^=#,:;()*\-^&!%<>|$\'\"\\\\\/\[\]\s]+)/g;

    let _str = str;

    let m;
    while ((m = regex.exec(str)) !== null) {

        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }
        
        m.forEach((match, groupIndex) => {
          _str = _str.replace(match,'<a href="/hashtag/'+match.substr(1)+'">'+match+'</a>');
        });
    }

    return _str;
  }

  parseUrlFromString(str) {
    const regex = /(?:http|ftp|https):\/\/(?:[\w_-]+(?:(?:\.[\w_-]+)+))(?:[\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?/g;

    let _str = str;

    let m;
    while ((m = regex.exec(str)) !== null) {

        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }
        
        m.forEach((match, groupIndex) => {
          _str = _str.replace(match,'<a href="'+match+'">'+match+'</a>');
        });
    }

    return _str;
  }

  layout(target) {
    let wH = $(target).find('.c-modal-sidebar-inner').height();

    $(target).find('.chat-box-thread').css({
      'height': (wH-100)+'px',
    });
  }

  toButtom() {
    if($(this.target).find('.chat-box-section').innerHeight() > $(this.target).find('.chat-box-thread').innerHeight()) {
      $(this.target).find('.chat-box-thread').scrollTop($(this.target).find('.chat-box-section').innerHeight() - $(this.target).find('.chat-box-thread').innerHeight() + 20);
    }
  }

  create(title) {

    let _this = this;

    let id = 'chat_box_'+Token.generateToken(64);

    let html = `
    <div id="${id}" class="c-modal modal-chat-box">
      <a class="close"></a>
      <div class="c-modal-sidebar-inner">

        <a class="modal-close white">
          <span aria-hidden="true">&times;</span>
        </a>

        <div class="chat-box">
          <h5 class="chat-box-title text-overflow-ellipsis">${title}</h5>
          <div class="typing-indicator">
            <span></span>
            <span></span>
            <span></span>
          </div>
          <div class="chat-box-thread">
            <div class="chat-box-section clearfix"></div>
          </div>
          <div class="chat-box-footer-section">
            <input type="text" class="chat-box-input">
            <button class="chat-box-send-btn">
              <i class="fa fa-location-arrow"></i>
            </button>
          </div>
        </div>

      </div>
    </div>
    `;

    $('#chat_box_panel').append(html);

    id = '#'+id;

    _this.layout(id);

    $(id).on('click','.close',function(){
      _this.leaveAndClose();
    });

    $(id).on('click','.modal-close',function(){
      _this.leaveAndClose();
    });

    $(id).on('keyup','.chat-box-input',function(e){

      if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
        // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return false;
      }

      _this.typing();

    });

    $(id).on('click','.chat-box-send-btn',function(){

      if($(id).find('.chat-box-input').val().trim() !== '') {
        _this.sending($(id).find('.chat-box-input').val().trim());
        _this.toButtom();
      }

    });

    $(id).on('keypress','.chat-box-input',function(e){

      if((e.keyCode == 13) && ($(id).find('.chat-box-input').val().trim() !== '')) {
        _this.sending($(id).find('.chat-box-input').val().trim());
        _this.toButtom();
      }

    });

    $(id).find('.chat-box-thread').on('scroll',function(){
      if($(this).scrollTop() < 20) {
        _this.more();
      }
    });

    return id;
  }

  createFormData(data) {
    let formData = new FormData(); 
    formData.append('data', data);
    return formData;
  }

  show() {
    let modal = new ModalDialog();
    modal.show(this.target);

    $(this.target).find('.chat-box-input').focus();
  }

  close() {
    let modal = new ModalDialog();
    modal.close();

    if(this.target != null) {
      $(this.target).remove();
      this.target = null;
    }
  }

  clear() {
    // this.target = null;
    this.room = null;
    this.key = null;
    this.page = 1;
    this.time = null;
    this.user = null;

    this.clearing = false;
  }

  leaveAndClose() {
    this.clearing = true;
    this.close();
    this.leave();
  }

}