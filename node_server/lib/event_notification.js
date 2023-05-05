const dateTime = require('./date_time');

module.exports = class EventNotification {

	constructor(db,io,redisClient) {
		this.db = db;
		this.io = io;
		this.redisClient = redisClient;
	}

	notifyNotification(userId,key,message,url,actionLink = null) {

		let _this = this;

	  // notify if user online
	  _this.redisClient.getAsync('online-user:'+userId).then(function(res){
	    
	    if(res === null) {
	      return false;
	    }

	    _this.countNotication(userId);
	    _this.notificationList(userId,key);
	    _this.snackbarNotication(userId,message,url,actionLink);

	  });

	}

	updateUserSeenNotification(key,userId) {
	  this.db.query("UPDATE `notifications` SET `seen` = 1 WHERE `identity_key` = '"+key+"', `receiver_id` = "+userId);
	}

	setAllNotificationSeen(userId) {
	  // update seen = 1
	  this.db.query("UPDATE `notifications` SET `seen` = 1 WHERE `receiver_id` = "+userId);
	}

	countNotication(userId) {

		let _this = this;

	  this.db.query("SELECT COUNT(identity_key) AS total FROM `notifications` WHERE `receiver_id` = "+userId+" AND `seen` = 0", function(err, row){
	    _this.io.in('u_'+userId).emit('count-notification', {
	      count: row[0]['total']
	    });
	  });
	}

	snackbarNotication(userId,message,url,actionLink) {
	  this.io.in('u_'+userId).emit('snackbar-notification', {
	    message: message,
	    user: userId,
	    url: url,
	    actionLink: actionLink
	  });
	}

	notificationList(userId,key) {

		let _this = this;

	  this.db.query("SELECT n.message, n.url, n.seen, n.created_at FROM `notifications` n WHERE `identity_key` = '"+key+"'", function(err, row){

	    if(row.length === 1) {

	      _this.io.in('u_'+userId).emit('notification-list-item', {
	        key: key,
	        message: row[0]['message'],
	        url: row[0]['url'],
	        seen: row[0]['seen'],
	        date: dateTime.passingDate(row[0].created_at,dateTime.now())
	      });

	    }

	  }); 

	}

	notificationLists(userId,token) {

		let _this = this;

	  // this.db.query("SELECT n.identity_key, n.message, n.url, n.seen, n.created_at FROM `notifications` n WHERE `receiver_id` = "+userId+" ORDER BY created_at DESC LIMIT 15", function(err, rows){
	  	this.db.query("SELECT n.identity_key, n.message, n.url, n.seen, n.created_at FROM `notifications` n WHERE `receiver_id` = "+userId+" AND `queued` = '0' ORDER BY active_date DESC LIMIT 15", function(err, rows){

	  	// WHERE `queued` = '1' AND `receiver_id` =
	  	// ORDER BY active_date DESC LIMIT 15

	  	// "SELECT n.identity_key, n.message, n.url, n.seen, n.created_at 
	  	// FROM `notifications` n 
	  	// WHERE `receiver_id` = "+userId+" AND `queued` = '1' 
	  	// ORDER BY active_date 
	  	// DESC LIMIT 15"

	    let data = [];
	    let count = 0;
	    let _now = dateTime.now();

	    for (var i = 0; i < rows.length; i++) {

	      data.push({
	        key: rows[i]['identity_key'],
	        message: rows[i]['message'],
	        url: rows[i]['url'],
	        seen: rows[i]['seen'],
	        date: dateTime.passingDate(rows[i].created_at,_now)
	      });

	      if((++count === rows.length) && (data.length > 0)) {
	        _this.io.in(userId+'.'+token).emit('notification-lists', data);
	      }

	    }

	  });
	}

	// notifyNotification(userId,key,message,url,actionLink = null) {

	// 	let _this = this;

	//   // notify if user online
	//   this.redisClient.getAsync('online-user:'+userId).then(function(res){
	    
	//     if(res === null) {
	//       return false;
	//     }

	//     _this.countNotication(userId);
	//     _this.notificationList(userId,key);
	//     _this.snackbarNotication(userId,message,url,actionLink);

	//   });

	// }

}