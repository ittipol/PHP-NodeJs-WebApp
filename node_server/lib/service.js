const dateTime = require('./date_time');

module.exports = class Service {

	constructor() {}

	static buildSqlForCountItemRelatedWithCategory(condition) {

		// dateTime.dt() = now
	  let sql = 'SELECT COUNT(i.id) AS total FROM `items` AS i LEFT JOIN `item_to_categories` AS ic ON ic.item_id = i.id LEFT JOIN `item_to_locations` AS il ON il.item_id = i.id WHERE (cancel_option = 0 AND expiration_date > "'+dateTime.dt()+'" AND approved = 1 AND deleted = 0)';

	  if(typeof condition.q_title !== 'undefined') {
	    for (var i = 0; i < condition.q_title.length; i++) {
	      sql += ' AND i.title LIKE "%'+condition.q_title[i]+'%"';
	    };

	    // Object.keys(images)
	  }

	  if(typeof condition.q_description !== 'undefined') {
	    for (var i = 0; i < condition.q_description.length; i++) {
	      sql += ' AND i.title LIKE "%'+condition.q_description[i]+'%"';
	    };
	  }

	  if(typeof condition.location !== 'undefined') {
	    sql += ' AND il.location_id = '+condition.location;
	  }

	  if((typeof condition.price_start !== 'undefined') && (typeof condition.price_end !== 'undefined')) {
	    sql += ' AND (i.price >= '+condition.price_start+' AND i.price <= '+condition.price_end+')';
	  }else if(typeof condition.price_start !== 'undefined') {
	    sql += ' AND i.price >= '+condition.price_start;
	  }else if(typeof condition.price_end !== 'undefined') {
	    sql += ' AND i.price <= '+condition.price_end;
	  }

	  if(typeof condition.from !== 'undefined') {
	    if(condition.from == 1) {
	      sql += ' AND i.`shop_id` is null'
	    }else if(condition.from == 2) {
	      sql += ' AND i.`shop_id` is not null'
	    }
	  }

	  if(typeof condition.publishing !== 'undefined') {
	    if(condition.publishing == 1) {
	      sql += ' AND i.`publishing_type` = 1'
	    }else if(condition.publishing == 2) {
	      sql += ' AND i.`publishing_type` = 2'
	    }
	  }

	  if(typeof condition.item !== 'undefined') {
	  	let _sqlArr = [];
	  	for (var i = 0; i < condition.item.length; i++) {
	  		_sqlArr.push('i.`grading` = ' + condition.item);
	  	}

	  	if(_sqlArr.length > 0) {
	  		sql += 'AND('+_sqlArr.join(' OR ')+')';
	  	}
	    // if(condition.item == 1) {
	    //   sql += ' AND i.`grading` = 1'
	    // }else if(condition.item == 2) {
	    //   sql += ' AND i.`grading` = 2'
	    // }
	  }

	  // if(typeof condition.b !== 'undefined' && condition.b.length > 0) { // blocked user
	  //   sql += ' AND i.created_by NOT IN ('+condition.b.join()+')';
	  // }

	  return sql;

	}

}