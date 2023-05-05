const service = require('./service');

module.exports = class Location {

	constructor(db,io) {
		this.db = db;
		this.io = io;
	}

	getLocation(data) {

		let _this = this;

		let sql = '';
		if(data['parentId'] === null) {
		  sql = 'SELECT * FROM `locations` WHERE `parent_id` IS NULL';
		}else if(data['parentId']) {
		  sql = 'SELECT * FROM `locations` WHERE `parent_id` = '+data['parentId'];
		}else {
		  return false;    
		}

		let count = 0;
		let locations = [];

		this.db.query(sql, function(err, rows){

		  for (var i = 0; i < rows.length; i++) {

		    let _rows = rows[i];
		  
		    _this.db.query('SELECT COUNT(id) AS total FROM `locations` WHERE `parent_id` = '+rows[i]['id'], function(err, row){
		      
		      let next = true;
		      if(row[0]['total'] === 0) {
		        next = false;
		      }

		      locations.push({
		        id: _rows['id'],
		        name: _rows['name'],
		        hasChild: next,
		      });

		      if(++count === rows.length) {
		        _this.io.in(data.chanel).emit('get-location', {
		          data: locations
		        });
		      }

		    })

		  }
		  
		})

	}

}