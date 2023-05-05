const dateTime = require('./date_time');
const token = require('./token');
const stringHelper = require('./string_helper');
const striptags = require('striptags');
const md5 = require('md5');

module.exports = class Chat {

	constructor(db,io,redisClient) {
		this.db = db;
		this.io = io;
		this.redisClient = redisClient;
		this.notifyMessageHandle = [];
		this.take = 20;
		this.userInChatRoom = [];
	}

	send(data,userId) {

		let _this = this;

		// if(this.notifyMessageHandle[data.room] !== undefined) {
		// 	clearTimeout(this.notifyMessageHandle[data.room]);
		// }

		clearTimeout(this.notifyMessageHandle[data.room]);

		// Check chat room active = 1
		// if 0 return as error

		// check user in chat room

		let message = striptags(data.message.trim());
		//
		this.db.query("INSERT INTO `chat_messages` (`chat_room_id`, `user_id`, `identity_key`, `message`, `created_at`) VALUES ('"+data.room+"', '"+userId+"', '"+md5(data.room)+token.generateToken(128)+"', "+this.db.escape(message)+", '"+dateTime.dt()+"')");

		_this.io.in('cr_'+data.room+'.'+data.key).emit('chat-message', {
		  user: userId,
		  message: message
		});

		_this.notifyMessageHandle[data.room] = setTimeout(function(){
		  _this.notifyMessage(data.room,userId);
		  _this.messageNoticationList(data.room, userId);
		},3500);

	}

	// Notify message to users
	notifyMessage(roomId,userId) {
	  
	  let _this = this;
	  // GET Last Message
	  this.db.query("SELECT cm.message, cm.created_at FROM `chat_messages` cm LEFT JOIN `chat_rooms` AS cr ON cm.chat_room_id = cr.id WHERE `chat_room_id` = "+roomId+" AND cr.active = 1 ORDER BY cm.created_at DESC LIMIT 1", function(err, messages){

	    if(messages.length === 1) {
	      // Get Users in room

	      	// _this.db.query("SELECT `user_id` FROM `user_in_chat_room` WHERE `chat_room_id` = "+roomId+" AND `notify` = 0 AND `message_read_date` <= '"+messages[0].created_at+"'", function(err, rows){
	        // check not self
	        _this.db.query("SELECT `user_id` FROM `user_in_chat_room` WHERE `user_id` != "+userId+" AND `chat_room_id` = "+roomId+" AND `notify` = 0 AND `message_read_date` <= '"+messages[0].created_at+"'", function(err, rows){
	        
	        for (var i = 0; i < rows.length; i++) {

	          // if(rows[i].user_id != userId) {

	            let _userid = rows[i].user_id;

	            // if(rows[i].notify == 0) {
		            // Update notify = 1
		            _this.db.query("UPDATE `user_in_chat_room` SET `notify` = 1, `message_read_date` = '"+messages[0].created_at+"' WHERE `chat_room_id`= "+roomId+" AND `user_id`= "+_userid);
		          // }

	            // Notify to online user
	            _this.redisClient.getAsync('online-user:'+_userid).then(function(res){
	              
	              if(res === null) {
	                return false;
	              }

	              _this.countMessageNotication(_userid);
	              _this.messageNoticationList(roomId, _userid);
	              _this.snackbarChatMessage(roomId, _userid, messages[0].message);

	            });

	          // }
	        }
	      });
	    }
	  });
	}

	// count message notification
	countMessageNotication(userId) {

		let _this = this;

	  this.db.query("SELECT COUNT(chat_room_id) AS total FROM `user_in_chat_room` ucr LEFT JOIN `chat_rooms` AS cr ON ucr.chat_room_id = cr.id WHERE `user_id` = "+userId+" AND `notify` = 1 AND cr.active = 1", function(err, row){
	    _this.io.in('u_'+userId).emit('count-message-notification', {
	      count: row[0]['total']
	    });
	  });
	}

	messageNoticationList(roomId,userId) {

		let _this = this;

	  	this.db.query("SELECT cm.message, cm.user_id, u.name, cr.model, cr.model_id, cm.created_at FROM `chat_messages` AS cm LEFT JOIN `users` as u ON cm.user_id = u.id LEFT JOIN `chat_rooms` AS cr ON cm.chat_room_id = cr.id WHERE cm.chat_room_id = "+roomId+" AND cr.active = 1 ORDER BY cm.created_at DESC LIMIT 1", function(err, message){

	    if(message.length === 1) {

	      let isSender = false;
	      if(message[0].user_id == userId) {
	        isSender = true;
	      }

	      let sql = '';
	      switch(message[0].model) {
	        case 'Shop':
	          sql = 'SELECT name AS title FROM `shops` WHERE `id` = '+message[0].model_id+' AND `deleted` = 0';
	        break;

	        case 'Item':
	          sql = 'SELECT title FROM `items` WHERE `id` = '+message[0].model_id+' AND `deleted` = 0';
	        break;
	      }

				_this.db.query(sql, function(err, _data){
					if(_data.length == 1) {
						_this.io.in('u_'+userId).emit('message-notification-list-item', {
						  room: roomId,
						  user: message[0].user_id,
						  message: message[0].message,
						  name: message[0].name,
						  // relatedTo: stringHelper.truncString(message[0].title,50),
						  relatedTo: stringHelper.truncString(_data[0].title,50),
						  cancel_option: message[0].cancel_option,
						  isSender: isSender,
						  date: dateTime.passingDate(message[0].created_at,dateTime.now())
						});
					}
				}); 
	      
	    }

	  });

	}

	// update notification message to panel
	messageNoticationLists(userId,token) {

		let _this = this;

	  let data = [];
	  let count = 0;
	  let _now = dateTime.now();

	  this.db.query("SELECT cr.id, cr.model, cr.model_id FROM `user_in_chat_room` ucr LEFT JOIN `chat_rooms` AS cr ON ucr.chat_room_id = cr.id LEFT JOIN `chat_messages` AS cm ON cr.id = cm.chat_room_id WHERE ucr.`user_id` = "+userId+" AND cr.active = 1 AND cm.message IS NOT NULL GROUP BY cr.id, cr.model, cr.model_id ORDER BY ucr.message_read_date DESC LIMIT 15", function(err, rooms) {

	    for (var i = 0; i < rooms.length; i++) {

	      let room = rooms[i];

	      _this.db.query("SELECT cm.message, cm.user_id, u.name, cm.created_at   FROM `chat_rooms` AS cr LEFT JOIN `chat_messages` AS cm ON cm.chat_room_id = cr.id LEFT JOIN `users` as u ON cm.user_id = u.id WHERE `model` = '"+rooms[i].model+"' AND `model_id` = "+rooms[i].model_id+" ORDER BY cm.created_at DESC LIMIT 1", function(err, message){

	        let sender = false;
	        if(message[0].user_id == userId) {
	          sender = true;
	        }

	        //
	        let sql = '';
	        switch(room.model) {
	          case 'Shop':
	            sql = 'SELECT name AS title FROM `shops` WHERE `id` = '+room.model_id+' AND `deleted` = 0';
	          break;

	          case 'Item':
	            sql = 'SELECT title FROM `items` WHERE `id` = '+room.model_id+' AND `deleted` = 0';
	          break;
	        }

	        _this.db.query(sql, function(err, _data){

	        	if(_data.length == 1) {
	        		data.push({
	        		  room: room.id,
	        		  user: message[0].user_id,
	        		  message: message[0].message,
	        		  name: message[0].name,
	        		  relatedTo: stringHelper.truncString(_data[0].title,50),
	        		  sender: sender,
	        		  date: dateTime.passingDate(message[0].created_at,_now)
	        		});
	        	}

	          if(++count === rooms.length) {
	            _this.io.in(userId+'.'+token).emit('message-notification-lists', data);
	          }

	        });  

	      });
	    }

	  });
	}

	// display notification with snackbar
	snackbarChatMessage(roomId,userId,message) {
	  this.io.in('u_'+userId).emit('snackbar-new-message', {
	    message: message,
	    room: roomId,
	    user: userId
	  });
	}

	// Update read all message
	setAllMessageRead(userId) {
	  // update notify = 1
	  this.db.query("UPDATE `user_in_chat_room` SET `notify` = 0 WHERE `user_id` = "+userId);
	}

	// Update specifically message
	updateUserReadMessage(roomId,userId) {

		let _this = this;

	  this.db.query("SELECT `chat_room_id` FROM `chat_messages` WHERE `chat_room_id` = "+roomId+" ORDER BY created_at DESC LIMIT 1", function(err, messages){
	    if(messages.length === 1) {
	      _this.db.query("UPDATE `user_in_chat_room` SET `notify` = 0, `message_read_date` = '"+dateTime.now(true)+"' WHERE `chat_room_id`= "+roomId+" AND `user_id`= "+userId); 
	    }
	  });
	}

	more(data,userId,token) {

		let _this = this;

		let skip = (this.take * data.page) - this.take;

		this.db.query("SELECT cm.message, cm.user_id, cm.created_at FROM `chat_messages` cm LEFT JOIN `chat_rooms` AS cr ON cm.chat_room_id = cr.id WHERE cm.chat_room_id = "+data.room+" AND cm.created_at < '"+data.time+"' AND cr.active = 1 ORDER BY cm.created_at DESC LIMIT "+skip+","+this.take, function(err, rows){
		  
		  let res = {
		    next: false
		  };

		  if(rows.length !== 0) {
		    res = {
		      data: rows,
		      page: data.page + 1,
		      next: true
		    };
		  }
		  
		  _this.io.in('ca_'+userId+'.'+data.key+'.'+token).emit('chat-load-more', res);
		});
	}

}