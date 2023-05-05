const service = require('./service');

module.exports = class Category {

	constructor(db,io,redisClient) {
		this.db = db;
		this.io = io;
		this.redisClient = redisClient;
	}

	getCategory(data) {

		let _this = this;

		let sql = '';
		if(data['parentId'] === null) {
		  sql = 'SELECT `id`,`name`, `slug`, `image` FROM `categories` WHERE `parent_id` IS NULL';
		}else if(data['parentId']) {
		  sql = 'SELECT `id`,`name`, `slug`, `image` FROM `categories` WHERE `parent_id` = '+data['parentId'];
		}else {
		  return false;    
		}

		let count = 0;
		let categories = [];

		this.db.query(sql, function(err, rows){

		  for (var i = 0; i < rows.length; i++) {

		    let _rows = rows[i];
		  
		    _this.db.query('SELECT COUNT(id) AS total FROM `categories` WHERE `parent_id` = '+rows[i]['id'], function(err, row){
		      
		      let next = true;
		      if(row[0]['total'] === 0) {
		        next = false;
		      }

		      categories.push({
		        id: _rows['id'],
		        name: _rows['name'],
		        hasChild: next,
		      });

		      if(++count === rows.length) {
		        _this.io.in(data.chanel).emit('get-category', {
		          data: categories
		        });
		      }

		    })

		  }
		  
		})
	}

	getCategoryWithFilter(data,userId) {

		let _this = this;

		let sql = '';
		if(data['parentId'] === null) {
		  sql = 'SELECT `id`,`name`, `slug`, `image` FROM `categories` WHERE `parent_id` IS NULL';
		}else if(data['parentId']) {
		  sql = 'SELECT `id`,`name`, `slug`, `image` FROM `categories` WHERE `parent_id` = '+data['parentId'];
		}else {
		  return false;    
		}

		let count = 0;
		let categories = [];

		this.db.query(sql, function(err, rows){

		  let sql = service.buildSqlForCountItemRelatedWithCategory(JSON.parse(data.queryString));

		  _this.redisClient.getAsync('online-user:'+userId).then(function(res){

		    if(res === null) {
		      _this.catagoryCountItem(rows,sql,data);
		    }else {
		      _this.db.query('SELECT `model`, `model_id` FROM `user_blocking` WHERE `user_id` = '+userId, function(err, blockedData){

		        let blockedUser = [];
		        let blockedItem = [];
		        // let blockedShop = [];

		        for (var i = 0; i < blockedData.length; i++) {

		        	switch(blockedData[i].model) {
		        	  case "User":
		        	  	blockedUser.push(blockedData[i].model_id);
		        	  break;

		        	  case "Item":
		        	  	blockedItem.push(blockedData[i].model_id);
		        	  break;

		        	  // case "Shop":
		        	  // 	blockedShop.push(blockedData[i].model_id);
		        	  // break;
		        	}

		        }

		        let block = [];
		        if(blockedUser.length > 0) {
		          // _sql += ' AND i.created_by NOT IN ('+blockedUser.join()+')';
		          block.push('i.created_by NOT IN ('+blockedUser.join()+')');
		        }

		        if(blockedItem.length > 0) {
		          // _sql += ' AND i.id NOT IN ('+blockedItem.join()+')';
		          block.push('i.id NOT IN ('+blockedItem.join()+')');
		        }

		        if(block.length > 0) {
		        	sql += 'AND('+block.join(' AND ')+')';
		        }

		        _this.catagoryCountItem(rows,sql,data);
		      });
		    }

		  });
		  
		})

	}

	catagoryCountItem(rows,sql,data) {

		let _this = this;

		let count = 0;
		let categories = [];

		for (var i = 0; i < rows.length; i++) {

		  let _rows = rows[i];

		  _this.db.query('SELECT COUNT(id) AS total FROM `categories` WHERE `parent_id` = '+rows[i]['id'], function(err, row){
		    
		    let next = true;
		    if(row[0]['total'] === 0) {
		      next = false;
		    }

		    _this.db.query("SELECT `category_id` FROM `category_paths` WHERE `path_id` = "+_rows['id'], function(err, paths){
		      
		      let ids = [];
		      for (var j = 0; j < paths.length; j++) {
		        ids.push(paths[j].category_id);
		      };

		      let _sql = sql+' AND ic.category_id IN ('+ids.join()+')';

		      _this.db.query(_sql, function(err, _data){

		        categories.push({
		          id: _rows['id'],
		          name: _rows['name'],
		          slug: _rows['slug'],
		          image: _rows['image'],
		          hasChild: next,
		          total: _data[0].total
		        });

		        if(++count === rows.length) {
		          _this.io.in(data.chanel).emit('get-category-list', {
		            data: categories
		          });
		        }

		      })

		    })

		  })

		}

	}

}