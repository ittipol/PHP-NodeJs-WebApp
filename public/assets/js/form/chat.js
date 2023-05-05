class Chat {

	constructor(chat) {
		this.chat = chat;
		this.io = null;
		this.loading = false;
		this.loadingPostition = 0;
		this.messagePlaced = false;
		this.typingHandle = null;
		this.messageReceivedHandle = null;
	}

	init() {

		this.io = new IO();

		let _this = this;

	  this.bind(_this);
	  this.socketEvent(_this);
 
	  Loading.show();
 
	  this.layout();
	  this.calPosition(window.innerHeight);
	}

	join() {
		this.io.socket.emit('chat-join', {
			room: this.chat.room,
	    key: this.chat.key
	  });
	}

	bind(_this) {

		$('#message_input').on('keyup',function(e){
			// maybe use keydown
			// if(e.keyCode === 13) {
			// 	return false;
			// }

			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
        // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return false;
  		}

  		// if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
    //     e.preventDefault();
    //   }

			_this.typing();

		});

		$('#send_btn').on('click',function(){

	    if($('#message_input').val().trim() !== '') {
	      _this.sending($('#message_input').val().trim());
	      _this.toButtom();
	    }

	  });

	  $('#message_input').on('keypress',function(event){

	  	if((event.keyCode == 13) && ($('#message_input').val().trim() !== '')) {
	  	  _this.sending($('#message_input').val().trim());
	  	  _this.toButtom();
	  	}

	  });

	  $('.chat-section').on('scroll',function(){
	  	if($(this).scrollTop() < _this.loadingPostition) {
	  		_this.more();
	  	}
	  });

	  $(window).resize(function(){
	  	_this.layout();
	  	_this.calPosition(window.innerHeight);
	  });

	}

	socketEvent(_this) {

		this.io.socket.on('after-online', function(res){
			_this.join();
		});

		this.io.socket.on('typing', function(res){

			if(_this.chat.user == res.user) {
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

	  	if(_this.chat.user != res.user) {
	  		setTimeout(function(){
					_this.io.socket.emit('message-read', {
						room: _this.chat.room,
						user: _this.chat.user
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

	  	_this.chat.page = res.page;

	  	for (var i = 0; i < res.data.length; i++) {
	  		me = false;

	  		if(_this.chat.user == res.data[i].user_id) {
	  			me = true;
	  		}

	  		$('#message_display').prepend(_this.getHtml(res.data[i].user_id, _this.parseMessage(res.data[i].message), res.data[i].created_at, me));
	  	};

	  	setTimeout(function(){
	  		_this.loading = false;
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
    	_this.more();

    	setTimeout(function(){
    		Loading.hide();
    		_this.toButtom();
    	},600)
    });

    this.io.socket.on('after-chat-leave', function(res){
      if(res.token != _this.io.token) {
        _this.chatRoomCheckUserExist();
      }
    });

	}

	toButtom() {
		if($('.chat-thread').innerHeight() > $('.chat-section').innerHeight()) {
			$('.chat-section').scrollTop(($('.chat-thread').innerHeight() - $('.chat-section').innerHeight()))
		}
	}

	more() {

		if(this.loading) {;
			return false;
		}

		this.loading = true;

		this.io.socket.emit('chat-load-more', {
			// chanel: this.chat.user+'.'+this.io.token,
			room: this.chat.room,
			key: this.chat.key,
			page: this.chat.page,
			time: this.chat.time
		});
	}

	sending(message) {
		// console.log(message.codePointAt(0));

		// var PATTERN = /(?:[\u2700-\u27bf]|(?:\ud83c[\udde6-\uddff]){2}|[\ud800-\udbff][\udc00-\udfff]|[\u0023-\u0039]\ufe0f?\u20e3|\u3299|\u3297|\u303d|\u3030|\u24c2|\ud83c[\udd70-\udd71]|\ud83c[\udd7e-\udd7f]|\ud83c\udd8e|\ud83c[\udd91-\udd9a]|\ud83c[\udde6-\uddff]|\ud83c[\ude01-\ude02]|\ud83c\ude1a|\ud83c\ude2f|\ud83c[\ude32-\ude3a]|\ud83c[\ude50-\ude51]|\u203c|\u2049|[\u25aa-\u25ab]|\u25b6|\u25c0|[\u25fb-\u25fe]|\u00a9|\u00ae|\u2122|\u2139|\ud83c\udc04|[\u2600-\u26FF]|\u2b05|\u2b06|\u2b07|\u2b1b|\u2b1c|\u2b50|\u2b55|\u231a|\u231b|\u2328|\u23cf|[\u23e9-\u23f3]|[\u23f8-\u23fa]|\ud83c\udccf|\u2934|\u2935|[\u2190-\u21ff])/g;

	  // console.log(String.fromCodePoint(0x1F389));
	  // console.log(String.fromCodePoint(0x1F604));

		$('#message_input').val('');

		this.placeMessage({
			user: this.chat.user,
			message: message.trim().stripTags()
		});

		this.messagePlaced = true;

		this.send(message);
	}

	send(message) {
		this.io.socket.emit('send-message', {
		  message: message,
		  room: this.chat.room,
		  // user: this.chat.user,
		  key: this.chat.key
		})
	}

	chatRoomCheckUserExist() {
	  this.io.socket.emit('chat-room-check-user-exist', {
	    room: this.chat.room,
	    key: this.chat.key
	  }); 
	}

	typing() {
		this.io.socket.emit('typing', {
			room: this.chat.room,
			user: this.chat.user,
			key: this.chat.key
		});
	}

	getHtml(user,message,date,me = true) {

		let html = '';

		if(me) {
			html = `
			<div class="message-section message-me">
			  <div class="avatar">
			    <img src="/avatar?d=1">
			  </div>
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
		$('#message_display').append(this.getHtml(data.user,this.parseMessage(data.message), moment().tz("Asia/Bangkok").format('YYYY-MM-DD HH:mm:ss'), me));
	}

	calPosition(screenHeight) {
		this.loadingPostition = screenHeight * 0.15;
	}

	layout() {
		let wH = window.innerHeight;
		let wW = window.innerWidth;
		let navbarH = 60;
		let sidebarW = $('.chat-left-sidenav').innerWidth();

		$('.chat-left-sidenav').css({
			'height': (wH-navbarH)+'px',
			'top': navbarH+'px'
		});

		$('.chat-section').css({
			'height': (wH-navbarH-50)+'px',
			'width': (wW-sidebarW)+'px'
		});

		$('.chat-footer-section').css('width',(wW-sidebarW)+'px');
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

}