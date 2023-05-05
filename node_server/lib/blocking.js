const service = require('./service');

module.exports = class Blocking {

	constructor(db,io) {
		this.db = db;
		this.io = io;
	}

	block(model,modelId,userId,token) {

		switch(model) {
		  case "User":
		  	this.blockUser(model,modelId,userId,token);
		  break;

		  case "Item":
		  	this.blockItem(model,modelId,userId,token);
		  break;

		  case "Shop":
		  	this.blockShop(model,modelId,userId,token);
		  break;
		}

	}

	blockUser(model,modelId,userId,token) {
		// if(modelId != userId) {
		// 	this._block(model,modelId,userId,token);
		// }

		let _this = this;

		this.db.query("SELECT `id` FROM `users` WHERE `id` = "+modelId, function(err, user){
			if((user.length === 1) && (user[0].created_at != userId)) {
				_this._block(model,modelId,userId,token);
			}
		});
	}

	blockItem(model,modelId,userId,token) {

		let _this = this;

		this.db.query("SELECT `created_at` FROM `items` WHERE `id` = "+modelId, function(err, item){
			if((item.length === 1) && (item[0].created_at != userId)) {
				_this._block(model,modelId,userId,token);
			}
		});
	}

	blockShop(model,modelId,userId,token) {

		let _this = this;

		this.db.query("SELECT `created_by` FROM `shops` WHERE `id` = "+modelId, function(err, shop){
			if((shop.length === 1) && (shop[0].created_by != userId)) {

				// _this._block(model,modelId,userId,token);

				// blocking shop
				// mean blocking user instend
				_this.db.query("SELECT * FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = 'User' AND `model_id` = "+shop[0].created_by, function(err, blockedData){

				  if(blockedData.length == 1) {

				    // unblock
				    _this.db.query("DELETE FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = 'User' AND `model_id` = "+shop[0].created_by);

				    _this.io.in(userId+'.'+token).emit('blocked', {
				    	type: model,
				    	id: modelId,
				      blocked: 0
				    });
				    
				  }else if(blockedData.length == 0) {

				    // block
				    _this.db.query("INSERT INTO `user_blocking` (`user_id`, `model`, `model_id`) VALUES ('"+userId+"', 'User', '"+shop[0].created_by+"')");

				    _this.io.in(userId+'.'+token).emit('blocked', {
				    	type: model,
				    	id: modelId,
				      blocked: 1
				    });
				  
				  }

				});
			}
		});
	}

	_block(model,modelId,userId,token) {

		let _this = this;

	  // check aleary blocked
	  _this.db.query("SELECT * FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = '"+model+"' AND `model_id` = "+modelId, function(err, blockedData){

	    if(blockedData.length == 1) {

	      // unblock
	      _this.db.query("DELETE FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = '"+model+"' AND `model_id` = "+modelId);

	      _this.io.in(userId+'.'+token).emit('blocked', {
	      	type: model,
	      	id: modelId,
	        blocked: 0
	      });
	      
	    }else if(blockedData.length == 0) {

	      // block
	      _this.db.query("INSERT INTO `user_blocking` (`user_id`, `model`, `model_id`) VALUES ('"+userId+"', '"+model+"', '"+modelId+"')");

	      _this.io.in(userId+'.'+token).emit('blocked', {
	      	type: model,
	      	id: modelId,
	        blocked: 1
	      });
	    
	    }

	  });     

	}

	removeBlocking(model,modelId,userId,token) {
		this._unblock(model,modelId,userId,token);
	}

	_unblock(model,modelId,userId,token) {

		let _this = this;

	  // check aleary blocked
	  _this.db.query("SELECT * FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = '"+model+"' AND `model_id` = "+modelId, function(err, blockedData){

	    if(blockedData.length == 1) {

	      // unblock
	      _this.db.query("DELETE FROM `user_blocking` WHERE `user_id` = "+userId+" AND `model` = '"+model+"' AND `model_id` = "+modelId);

	      _this.io.in(userId+'.'+token).emit('blocking-removed', {
		    	type: model,
		    	id: modelId
		    });
	      
	    }

	  });     

	}

}