console.log('=========###########################################');
console.log('>> Start ###########################################');
console.log('=========###########################################');
// 
const ENV = require('./env');

//
const db = require('./db');
const fs = require('fs');
const app = require('express')();

// 
const stringHelper = require('./lib/string_helper');
const dateTime = require('./lib/date_time');
const token = require('./lib/token');
// const striptags = require('striptags');

// Server
if(ENV.SSL) {
  var server = require('https').Server({key: fs.readFileSync(ENV.SSL_KEY),cert: fs.readFileSync(ENV.SSL_CERT)},app);
  console.log('SSL -> true');
}else{
  var server = require('http').Server(app);
  console.log('SSL -> false');
}
// Socket.io
const io = require('socket.io')(server);
// Redis
const redis = require('redis');
// const redis = new Redis({
//   db: ENV.REDIS_DB || 0
// });
const redisClient = redis.createClient(6379, 'redis');
// Promise
const Promise = require('bluebird');
Promise.promisifyAll(redis);

const chatHandler = new (require('./lib/chat'))(db,io,redisClient);
const eventNotificationHandler = new (require('./lib/event_notification'))(db,io,redisClient);
const itemHandler = new (require('./lib/item'))(db,io);
const categoryHandler = new (require('./lib/category'))(db,io,redisClient);
const locationHandler = new (require('./lib/location'))(db,io);
const blockingHandler = new (require('./lib/blocking'))(db,io);

// Var
var userHandle = [];
var chatRoom = [];

function addUserOnline(userId) {
  redisClient.set('online-user:'+userId, 1);
  // console.log('add user online');
  // expire at 1 hrs = 3600 secs
  // redisClient.expireat('online-user:'+userId, 3600);
}

function clearUserOnline(userId) {
  redisClient.del('online-user:'+userId);
}

function arrayIndexOf(haystack, needle){
  for(var i = 0; i < haystack.length; ++i){
    if(haystack[i] == needle) {
      return i;
    }
  }
  return -1;
}

io.on('connection', function(socket){

  socket.on('join', function(chanel,type){
    socket.join(chanel); 
    // console.log('chanel joined: '+chanel);
  });

  socket.on('leave', function(chanel,type){
    socket.leave(chanel); 
  });

  socket.on('online', function(data){

    if((data.userId != null) && (typeof userHandle[data.userId] !== 'undefined')) {
      clearTimeout(userHandle[data.userId]);
    }

    db.query("SELECT COUNT(*) AS exist FROM `users` WHERE `id` = "+data.userId+" AND `user_key` = '"+data.key+"'", function(err, user){

      if(user[0].exist) {
        //
        socket.userId = data.userId;
        socket.userKey = data.key;
        socket.identToken = data.token;
     
        // join
        socket.join('u_'+data.userId);
        socket.join(data.userId+'.'+data.token);

        // Chat Message Notification
        chatHandler.countMessageNotication(data.userId);
        chatHandler.messageNoticationLists(data.userId,data.token);
        // Event Notification
        eventNotificationHandler.countNotication(data.userId);
        eventNotificationHandler.notificationLists(data.userId,data.token);
        // --------------------------------------------------

        io.in(data.userId+'.'+data.token).emit('after-online');

        redisClient.getAsync('online-user:'+data.userId).then(function(res){

          if(res !== null) {
            return false;
          }

          // Set Last active
          db.query("UPDATE `users` SET `last_active` = '"+dateTime.now(true)+"' WHERE `id` = "+data.userId);

          // console.log('user online # '+data.userId);
          addUserOnline(data.userId);
          
          io.in('check-online').emit('check-user-online', {
            user: data.userId,
            online: true
          });

        });

      }else {

        io.in(data.userId+'.'+data.token).emit('user-error',{
          title: 'ผู้ใช้ผิดพลาด',
          message: 'เราตรวจพบการเข้าถึงของผู้ใช้อย่างไม่ถูกต้อง ระบบจะกลับไปยังหน้าแรกใน 5 วินาที'
        });

      }

    });

  });

  socket.on('disconnect', function() {

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    userHandle[socket.userId] = setTimeout(function(){

      // Set Last active
      db.query("UPDATE `users` SET `last_active` = '"+dateTime.now(true)+"' WHERE `id` = "+socket.userId);

      // Clear
      clearUserOnline(socket.userId);
      // chanel leave
      socket.leave('u_'+socket.user);
      socket.leave(socket.user+'.'+socket.identToken);

      // 
      io.in('u_'+socket.userId).emit('offline');

      io.in('check-online').emit('check-user-online', {
        user: socket.userId,
        online: false
      });

      // redisClient.getAsync('online-user:'+socket.userId).then(function(res){

      //   if(res === null) {
      //     return false;
      //   }

      //   // Set Last active
      //   db.query("UPDATE `users` SET `last_active` = '"+dateTime.now(true)+"' WHERE `id` = "+socket.userId);

      //   // Clear
      //   clearUserOnline(socket.userId);
      //   // chanel leave
      //   socket.leave('u_'+socket.user);
      //   socket.leave(socket.user+'.'+socket.identToken);

      //   // 
      //   io.in('u_'+socket.userId).emit('offline');

      //   io.in('check-online').emit('check-user-online', {
      //     user: socket.userId,
      //     online: false
      //   });

      // });

    },4000);

  });

  socket.on('check-user-online', function(data) {

    redisClient.getAsync('online-user:'+data.userId).then(function(res){

      if(res === null) {
        io.in('check-online').emit('check-user-online', {
          user: data.userId,
          online: false
        });
      }else {
        io.in('check-online').emit('check-user-online', {
          user: data.userId,
          online: true
        });
      }

    });

  });






  // ||||||||||||||||||||||| CHAT |||||||||||||||||||||||

  socket.on('chat-join', function(data){

    // if(typeof socket.userId === 'undefined') {
    //   return false;
    // }

    if(typeof chatRoom[data.room] === 'undefined') {
      chatRoom[data.room] = [];
    }

    redisClient.getAsync('chat-room:'+data.room+':'+data.key+':'+socket.userId).then(function(res){

      if(res === null) {
        io.in(data.chanel).emit('chat-error', {
          error: true,
          message: 'มีบางอย่างผิดพลาด ไม่สามารถแชทได้'
        });
        return false;
      }

      if(typeof chatRoom[data.room][socket.userId] !== 'undefined') {
        clearTimeout(chatRoom[data.room][socket.userId]);
      }

      socket.join('cr_'+data.room+'.'+data.key);
      socket.join('ca_'+socket.userId+'.'+data.key+'.'+socket.identToken);

      io.in('ca_'+socket.userId+'.'+data.key+'.'+socket.identToken).emit('after-chat-joined');

    });

  });

  socket.on('chat-leave', function(data){

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    // if(typeof chatRoom[data.room] === 'undefined') {
    //   chatRoom[data.room] = [];
    // }

    chatRoom[data.room][socket.userId] = setTimeout(function(){

      io.in('ca_'+socket.userId+'.'+data.key+'.'+socket.identToken).emit('chat-cleared');

      socket.leave('cr_'+data.room+'.'+data.key);
      socket.leave('ca_'+socket.userId+'.'+data.key+'.'+socket.identToken);

      // clear redis
      redisClient.del('chat-room:'+data.room+':'+data.key+':'+socket.userId);
    },4000);

    io.in(socket.userId+'.'+socket.identToken).emit('after-chat-leave',{
      token: socket.identToken
    });

  });

  socket.on('chat-room-check-user-exist', function(data){
    if(typeof socket.userId === 'undefined') {
      return false;
    }

    if(typeof chatRoom[data.room][socket.userId] !== 'undefined') {
      clearTimeout(chatRoom[data.room][socket.userId]);
    }
  });

  socket.on('typing', function(data){
    io.in('cr_'+data.room+'.'+data.key).emit('typing', {
      user: data.user
    });
  });

  socket.on('send-message', function(data){
    if(!data.room || !socket.userId || !data.key) {
      io.in(data.chanel).emit('chat-error', {
        error: true,
        message: 'มีบางอย่างผิดพลาด ไม่สามารถแชทได้'
      });
      return false;
    }

    // push user in chat room (Redis)
    // user-chat-{room_id}-{room_key}-{user_id}

    // redisClient.getAsync('online-user:'+socket.userId).then(function(res){
    redisClient.getAsync('chat-room:'+data.room+':'+data.key+':'+socket.userId).then(function(res){

      if(res === null) {
        io.in(data.chanel).emit('chat-error', {
          error: true,
          message: 'มีบางอย่างผิดพลาด ไม่สามารถแชทได้'
        });
        return false;
      }

      chatHandler.send(data,socket.userId);

    });

  });

  socket.on('chat-load-more', function(data){

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    chatHandler.more(data,socket.userId,socket.identToken);
  });

  // socket.on('item-chat-room-message-send', function(data){

  //   db.query('SELECT `id`,`title`,`created_by` FROM `items` WHERE `id` = "'+data.item+'" AND `cancel_option` = 0 LIMIT 1', function(err, rows){

  //     if((rows.length === 1) && (data.user != rows[0].created_at)) {
        
  //       db.query('SELECT item_chat_rooms.chat_room_id FROM `item_chat_rooms` LEFT JOIN user_in_chat_room ON user_in_chat_room.chat_room_id = item_chat_rooms.chat_room_id WHERE item_chat_rooms.item_id = '+data.item+' AND user_in_chat_room.user_id = '+data.user+' AND user_in_chat_room.role = "b" LIMIT 1',function(err, rooms){

  //         if(rooms.length == 1) {
  //           // send
  //           chatHandler.chatRoomSend({
  //             message: data.message,
  //             room: rooms[0].chat_room_id,
  //             user: data.user,
  //             chanel: data.chanel
  //           });

  //         }else {
  //           // create room and send
  //           chatHandler.createRoom(rows[0].created_by,data);
  //         }

  //       });

  //     }else {
  //       io.in(data.chanel).emit('item-chat-room-after-sending', {
  //         error: true,
  //         errorMessage: 'ไม่พบรายการนี้'
  //       });
  //     }

  //   })

  // })

  socket.on('message-read', function(data){
    chatHandler.updateUserReadMessage(data.room,data.user);
  })

  socket.on('count-message-notification', function(){
    chatHandler.countMessageNotication(socket.userId);
  })

  socket.on('message-notification-lists', function(){
    chatHandler.messageNoticationLists(socket.userId,socket.identToken);
  })

  socket.on('set-all-message-read', function(){
    chatHandler.setAllMessageRead(socket.userId);
  })


  // ||||||||||||||||||||||| NOTIFICATION |||||||||||||||||||||||

  socket.on('notification-seen', function(data){
    eventNotificationHandler.updateUserSeenNotification(data.key,data.user);
  })

  socket.on('count-notification', function(){
    eventNotificationHandler.countNotication(socket.userId);
  })

  socket.on('notification-lists', function(){
    eventNotificationHandler.notificationLists(socket.userId,socket.identToken);
  })

  socket.on('set-all-notification-seen', function(){
    eventNotificationHandler.setAllNotificationSeen(socket.userId);
  })



  // ||||||||||||||||||||||| LOCATION |||||||||||||||||||||||

  socket.on('get-location', function(data){
    locationHandler.getLocation(data);
  })




  // ||||||||||||||||||||||| CATEGORY |||||||||||||||||||||||

  socket.on('get-category', function(data){
    categoryHandler.getCategory(data);
  })

  socket.on('get-category-list', function(data){
    categoryHandler.getCategoryWithFilter(data,socket.userId);
  })

  // ||||||||||||||||||||||| Item |||||||||||||||||||||||

  socket.on('get-item-detail-contact', function(data){
    itemHandler.getContact(data,socket.identToken);
  });



  // ||||||||||||||||||||||| Blocking User |||||||||||||||||||||||

  socket.on('blocking', function(data){

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    blockingHandler.block(data.blockedType,data.blockedId,socket.userId,socket.identToken);

  });

  socket.on('remove-blocking', function(data){

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    blockingHandler.removeBlocking(data.blockedType,data.blockedId,socket.userId,socket.identToken);
  });



  // ||||||||||||||||||||||| Shop |||||||||||||||||||||||

  socket.on('shop-remove', function(data){

    if(typeof socket.userId === 'undefined') {
      return false;
    }

    db.query("SELECT `id` FROM `shops` WHERE `created_by` = "+socket.userId+" AND `deleted` = 0", function(err, shop){

      if(shop.length == 1) {

        db.query("UPDATE `users` SET `upgraded` = '0' WHERE `id` = "+socket.userId);
        db.query("UPDATE `shops` SET `deleted` = '1' WHERE `id` = "+shop[0].id);

        // Shop notification
        // db.query("DELETE FROM `notifications` WHERE `model` = 'Shop' AND `model_id` = "+shop[0].id);

        // Shop chat room
        db.query("UPDATE `chat_rooms` SET `active` = '0' WHERE `model` = 'Shop' AND `model_id` = "+shop[0].id);

        // remove blocked shop
        // db.query("DELETE FROM `user_blocking` WHERE `model` = 'Shop' AND `model_id` = "+shop[0].id);

        db.query("SELECT `id` FROM `items` WHERE `deleted` = 0 AND `shop_id` = "+shop[0].id+" AND `created_by` = "+socket.userId, function(err, items){

          if(items.length > 0) {
            let ids = [];
            for (var i = 0; i < items.length; i++) {
              ids.push(items[i].id);
            }

            let _ids = ids.join();

            db.query("UPDATE `items` SET `deleted` = '1' WHERE `id` IN ("+_ids+")");
          
            // remove notification (item)
            // db.query("DELETE FROM `notifications` WHERE `model` = 'Item' AND `model_id` IN ("+_ids+") AND `receiver_id` = "+socket.userId);
            db.query("DELETE FROM `notifications` WHERE `model` = 'Item' AND `model_id` IN ("+_ids+")");
          
            // set related chat room active to 0
            db.query("UPDATE `chat_rooms` SET `active` = '0' WHERE `model` = 'Item' AND `model_id` IN ("+_ids+")");

            // remove blocked items
            db.query("DELETE FROM `user_blocking` WHERE `model` = 'Item' AND `model_id` IN ("+_ids+")");

          }

        });

        setTimeout(function(){
          io.in(socket.userId+'.'+socket.identToken).emit('shop-removed');
        },1800);

      }

    });

  })

});



// ==================================================


// check user and clean value if They are online, check every 5 mins
setInterval(function(){
  db.query("SELECT `id` FROM `users` WHERE (`last_active` >= '"+dateTime.now(true,2760)+"' AND `last_active` <= '"+dateTime.now(true,1800)+"') ORDER BY last_active ASC LIMIT 100", function(err, rows){
    for (var i = 0; i < rows.length; i++) {
      // temp
      let _userid = rows[i].id;

      redisClient.getAsync('online-user:'+_userid).then(function(_res){

        if(_res === null) {
          return false;
        }

        // Clear
        clearUserOnline(_userid);
        // Emit
        // io.in('u_'+_userid).emit('offline', {});

        io.in('check-online').emit('check-user-online', {
          user: _userid,
          online: false
        });

      });
    }
  });
},300000);

// System recurring

// Check approved data
// setInterval(function(){

//   let now = dateTime.now(true);

//   // Save to recurring date
//   db.query("UPDATE `system_recurring_date` SET `date` = '"+now+"' WHERE `system_recurring_date`.`id` = 1");

//   db.query('SELECT `model`, `model_id` FROM `approve_queues` WHERE `checking_date` <= "'+now+'"', function(err, rows){

//     for (var i = 0; i < rows.length; i++) {
      
//       let _row = rows[i];

//       // remove this queue
//       db.query("DELETE FROM `approve_queues` WHERE `model` = '"+_row.model+"' AND `model_id` = "+_row.model_id);

//       // Get Item
//       db.query("SELECT `title`, `created_by` FROM `items` WHERE `id` = "+_row.model_id+" AND `approved` = 0", function(err, data){

//         if(data.length === 1) {
//           // data
//           let message = 'รายการขาย "'+stringHelper.truncString(data[0]['title'],60)+'" ผ่านการตรวจสอบแล้ว';
//           let url = '/ticket/view/'+_row.model_id;
//           let key = dateTime.now()+token.generateToken(64);

//           // update approved = 1 in item table
//           db.query("UPDATE `items` SET `approved` = 1 WHERE `id` = "+_row.model_id);
//           // Create Notification record
//           db.query("INSERT INTO `notifications` (`model`, `model_id`, `identity_key`,`message`, `url`, `receiver_id`, `created_at`) VALUES ('"+_row.model+"', "+_row.model_id+", '"+key+"',"+db.escape(message)+", '"+url+"', "+data[0].created_by+", '"+now+"')");
//           // Notify to user
//           eventNotificationHandler.notifyNotification(data[0].created_by,key,message,url);

//         }

//       }); 
      
//     }

//   });

// },40000);

// notify when post can pull, every 5 mins
setInterval(function(){

  let now = dateTime.ts();
  let firstDate = dateTime.convertTimestampToDateTime(now + 258900); // 3 days - 15 mins (900) = 258300
  let lastDay = dateTime.convertTimestampToDateTime(now + 259200); // 3 days

  db.query("UPDATE `system_recurring_date` SET `date` = '"+lastDay+"' WHERE `system_recurring_date`.`id` = 2");

  db.query("SELECT `id`, `title`, `created_by` FROM `items` WHERE (`active_date` >= '"+firstDate+"' AND `active_date` < '"+lastDay+"') ORDER BY created_at ASC", function(err, data){

    for (var i = 0; i < data.length; i++) {

      let message = 'รายการขาย "'+stringHelper.truncString(data[i].title,60)+'" สามารถเลื่อนขึ้นสู่ตำแหน่งบนได้แล้ว';
      let url = '/ticket/view/'+data[i].id;
      let key = dateTime.now()+token.generateToken(64);
      let actionLink = {
        url: '/ticket/view/'+data[i].id,
        label: 'เลื่อนรายการขายขึ้นสู่ตำแหน่งบน'
      };

      db.query("INSERT INTO `notifications` (`model`, `model_id`, `identity_key`,`message`, `url`, `receiver_id`, `created_at`) VALUES ('Item', "+data[i].id+", '"+key+"',"+db.escape(message)+", '"+url+"', "+data[i].created_by+", '"+now+"')");

      eventNotificationHandler.notifyNotification(data[0].created_by,key,message,null,actionLink);

    }

  });

},300000);

// notify when post can extend expire date, every 15 mins
// setInterval(function(){

//   let now = dateTime.ts();
//   let firstDate = dateTime.convertTimestampToDateTime(now + 863100); // 10 days - 15 mins = 863100
//   let lastDay = dateTime.convertTimestampToDateTime(now + 864000); // 10 days

//   // update lastDay to table recurrings
//   db.query("UPDATE `system_recurring_date` SET `date` = '"+lastDay+"' WHERE `system_recurring_date`.`id` = 3");

//   db.query("SELECT `id`, `title`, `created_by` FROM `items` WHERE (`expiration_date` >= '"+firstDate+"' AND `expiration_date` < '"+lastDay+"') ORDER BY created_at ASC", function(err, data){
    
//     for (var i = 0; i < data.length; i++) {

//       let message = 'รายการขาย "'+stringHelper.truncString(data[i].title,60)+'" เหลือเวลารายการขายอีก 10 วัน สามารถต่ออายุรายการขายนี้ได้แล้ว';
//       let url = '/ticket/view/'+data[i].id;
//       let key = dateTime.now()+token.generateToken(64);
//       let actionLink = {
//         url: '/ticket/view/'+data[i].id,
//         label: 'ต่ออายุรายการขาย'
//       };

//       db.query("INSERT INTO `notifications` (`model`, `model_id`, `identity_key`,`message`, `url`, `receiver_id`, `created_at`) VALUES ('Item', "+data[i].id+", '"+key+"',"+db.escape(message)+", '"+url+"', "+data[i].created_by+", '"+now+"')");

//       eventNotificationHandler.notifyNotification(data[0].created_by,key,message,null,actionLink);

//     }

//   });

// },900000);

// check expire, every 60 secs
// setInterval(function(){

//   let now = dateTime.dt();
//   let firstDate = dateTime.convertTimestampToDateTime(dateTime.convertDateTimeToTimestamp(now) - 60); // 10 days

//   db.query("UPDATE `system_recurring_date` SET `date` = '"+now+"' WHERE `system_recurring_date`.`id` = 4");

//   db.query("SELECT `id`, `title`, `created_by` FROM `items` WHERE (`expiration_date` > '"+firstDate+"' AND `expiration_date` <= '"+now+"') ORDER BY created_at ASC", function(err, data){

//     for (var i = 0; i < data.length; i++) {

//       let message = 'รายการขาย "'+stringHelper.truncString(data[i].title,60)+'" สิ้นสุดการขายแล้ว';
//       let url = '/ticket/view/'+data[i].id;
//       let key = dateTime.now()+token.generateToken(64);

//       db.query("INSERT INTO `notifications` (`model`, `model_id`, `identity_key`,`message`, `url`, `receiver_id`, `created_at`) VALUES ('Item', "+data[i].id+", '"+key+"',"+db.escape(message)+", '"+url+"', "+data[i].created_by+", '"+now+"')");

//       eventNotificationHandler.notifyNotification(data[i].created_by,key,message,url);

//     }

//   });

// },30000);

// Order Notification, Every 15 secs
setInterval(function(){
  db.query("SELECT `receiver_id`, `message`, `url`, `identity_key` FROM `notifications` WHERE `queued` = 1 ORDER BY created_at ASC", function(err, data){

    for (var i = 0; i < data.length; i++) {
      db.query("UPDATE `notifications` SET `queued` = '0', `active_date` = '"+dateTime.now(true)+"' WHERE `identity_key` = '"+data[i].identity_key+"'");
      eventNotificationHandler.notifyNotification(data[i].receiver_id,data[i].identity_key,data[i].message,data[i].url);
    }

  });

},15000);  

server.listen(ENV.SOCKET_PORT, () => {
  console.log('App listening on port -> '+ENV.SOCKET_PORT)
});