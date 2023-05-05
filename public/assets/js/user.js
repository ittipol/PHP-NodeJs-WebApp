class User {

	constructor(){
		if(!User.instance){
		  this.user = null;
		  this.key = null;
		  this.newMessage = false;
		  this.newNotification = false;
		  this.emptyMessage = true;
		  this.emptyNotification = true;
		  this.io;

		  User.instance = this;
		}

		return User.instance;
	}

	init() {

		// let _this = this;

		this.io = new IO();

		this.bind();
		this.socketEvent();

		this.online();
	}

	bind() {

		let _this = this;

		$('#message_notification_icon').on('click',function(){
			if(_this.newMessage) {
				_this.newMessage = false;

				$('#message_notification_badge').text(0);
				$('#message_notification_icon').removeClass('on');

				_this.setAllMessageRead();
			}
		});

		$('#event_notification_icon').on('click',function(){
			if(_this.newNotification) {
				_this.newNotification = false;
				
				$('#notification_badge').text(0);
				$('#event_notification_icon').removeClass('on');

				_this.setAllNotificationSeen();
			}
		});

	}

	socketEvent() {

		let _this = this;
		
		this.io.socket.on('offline', function(res){
			_this.online();
		});

		// ################### Chat Message
		this.io.socket.on('count-message-notification', function(res){
			
			$('#message_notification_badge').text(res.count);

			if(res.count > 0) {
				_this.newMessage = true;
				$('#message_notification_icon').addClass('on');
			}else {
				$('#message_notification_icon').removeClass('on');
			}

		});

		this.io.socket.on('snackbar-new-message', function(res){
			const snackbar = new Snackbar();
			snackbar.setTitle('<span class="avatar"><img src="/avatar/'+res.user+'?d=1"></span><span class="w-50 ml-2"><div><small>ข้อความใหม่</small></div><div>'+res.message+'</div></span><a href="#" data-chat-box="1" data-chat-data="r|'+res.room+'" class="action">แชท</a>');
			snackbar.display();
		});

		this.io.socket.on('message-notification-lists', function(res){

			for (var i = 0; i < res.length; i++) {
				if($('#message_'+res[i].room).length) {
					$('#message_'+res[i].room).remove();
				}

				if(_this.emptyMessage) {
					_this.emptyMessage = false;
					$('#message_notification_list').text('');
				}

				$('#message_notification_list').append(_this.messageNotificationListHtml(res[i]));

			}
		})

		this.io.socket.on('message-notification-list-item', function(res){

			if($('#message_notification_item_'+res.room).length) {
				$('#message_notification_item_'+res.room).remove();
			}

			if(_this.emptyMessage) {
				_this.emptyMessage = false;
				$('#message_notification_list').text('');
			}

			// create new and place to top of list
			$('#message_notification_list').prepend(_this.messageNotificationListHtml(res));

		})

		// ################### Notification

		this.io.socket.on('count-notification', function(res){

			$('#notification_badge').text(res.count);

			if(res.count > 0) {
				_this.newNotification = true;
				$('#event_notification_icon').addClass('on');
			}else {
				$('#event_notification_icon').removeClass('on');
			}

		});

		this.io.socket.on('snackbar-notification', function(res){

			let url = '';
			if(res.url) {
				url = 'href="'+res.url+'"';
			}

			let actionLink = '';
			if(res.actionLink) {
				actionLink = '<a href="'+res.actionLink.url+'" class="action">'+res.actionLink.label+'</a>';
			}

			const snackbar = new Snackbar();
			snackbar.setTitle('<a '+url+'>'+res.message+'</a>'+actionLink);
			snackbar.display();
		});

		this.io.socket.on('notification-lists', function(res){

			for (var i = 0; i < res.length; i++) {
				if($('#notification_list_'+res[i].key).length) {
					$('#notification_list_'+res[i].key).remove();
				}

				if(_this.emptyNotification) {
					_this.emptyNotification = false;
					$('#notification_list').text('');
				}

				$('#notification_list').append(_this.notificationListHtml(res[i]));
			}
		})

		this.io.socket.on('notification-list-item', function(res){

			if($('#notification_item_'+res.key).length) {
				$('#notification_item_'+res.key).remove();
			}

			if(_this.emptyNotification) {
				_this.emptyNotification = false;
				$('#notification_list').text('');
			}

			// create new and place to top of list
			$('#notification_list').prepend(_this.notificationListHtml(res));
		})

		this.io.socket.on('user-error', function(res){
			const modal = new ModalDialog();
			modal.show(modal.create(res.title,res.message,'full',false));

			setTimeout(function(){
				location.href = '/';
			},5000);
		});

	}

	// join() {
	// 	this.io.join('u_'+this.user);
	// 	this.io.join(this.user+'.'+this.io.token);
	// }

	online() {
		this.io.socket.emit('online', {
			userId: this.user,
			key: this.key,
			token: this.io.token
		});
	}

	// Chat message notification
	// countMessageNotification() {
	// 	this.io.socket.emit('count-message-notification');
	// }

	// getMessageNotificationLists() {
	// 	this.io.socket.emit('message-notification-lists');
	// }

	// // Notification
	// countNotification() {
	// 	this.io.socket.emit('count-notification');
	// }

	// getNotificationLists() {
	// 	this.io.socket.emit('notification-lists');
	// }

	messageNotificationListHtml(data) {

		if(data.sender) {
			var senderLable = 'คุณได้ส่งข้อความถึง '+data.name+' ('+data.date+')';
		}else{
			var senderLable = data.name+' ได้ส่งข้อความถึงคุณ'+' ('+data.date+')';
		}

		return `
			<a href="#" data-chat-box="1" data-chat-data="r|${data.room}" id="message_notification_item_${data.room}" class="notification-list-item">
			  <div class="notification-icon">
			    <img class="notification-avatar" src="/avatar/${data.user}?d=1">
			  </div>
			  <div class="notification-list-content">
			    <div><i class="fas fa-comments" aria-hidden="true"></i>&nbsp;${data.message}</div>
			    <hr>
			    <div class="pb1"><small>${senderLable}</small></div>
			  	<div><small>[ ${data['relatedTo']} ]</small></div>
			  </div>
			</a>
    `;

	}

	setAllMessageRead() {
		this.io.socket.emit('set-all-message-read');
	}

	notificationListHtml(data) {

		let url = '';
		if(data.url) {
			url = 'href="'+data.url+'"';
		}

		return `
			<a ${url} id="notification_item_${data.key}" class="notification-list-item">
			  <div class="notification-icon">
			    <i class="far fa-bell"></i>
			  </div>
			  <div class="notification-list-content">
			    <div>${data.message}</div>
			    <div><small>${data.date}</small></div>
			  </div>
			</a>
    `;

	}

	setAllNotificationSeen() {
		this.io.socket.emit('set-all-notification-seen');
	}

	setUser(user) {
		this.user = user;
	}

	check() {
		return this.user;
	}

	setKey(key) {
		this.key = key;
	}

}