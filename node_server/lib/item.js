const stringHelper = require('./string_helper');

module.exports = class Item {

	constructor(db,io) {
		this.db = db;
		this.io = io;
	}

	getContact(data,token) {

		let _this = this;

		this.db.query("SELECT `contact`, `use_specific_contact`, `created_by` FROM `items` WHERE `id` = "+data.id+" AND `cancel_option` = 0 AND `deleted` = 0", function(err, item){

		  let contact = null;

		  if(item.length == 1) {

		    if(item[0].use_specific_contact) {

		      _this.db.query("SELECT `contact`, `upgraded` FROM `users` WHERE `id` = "+item[0].created_by+" AND `upgraded` = 1", function(err, user){

		        if(user.length == 1) {

		          if(user[0].upgraded) {

		            // shop contact
		            _this.db.query("SELECT `contact` FROM `shops` WHERE `created_by` = "+item[0].created_by+" AND `deleted` = 0", function(err, shop){

		              if(shop.length == 1) {

		                _this.io.in('item.'+data.id+'.'+token).emit('get-item-detail-contact', {
		                  contact: stringHelper.getUrlFromString(stringHelper.getHashtagFromString(shop[0].contact.replace(/(?:\r\n|\r|\n)/g, '<br/>')))
		                });

		              }

		            });

		          }else {

		            _this.io.in('item.'+data.id+'.'+token).emit('get-item-detail-contact', {
		              contact: stringHelper.getUrlFromString(stringHelper.getHashtagFromString(user[0].contact.replace(/(?:\r\n|\r|\n)/g, '<br/>')))
		            });

		          }

		        }

		      });

		    }else {

		      _this.io.in('item.'+data.id+'.'+token).emit('get-item-detail-contact', {
		        contact: stringHelper.getUrlFromString(stringHelper.getHashtagFromString(item[0].contact.replace(/(?:\r\n|\r|\n)/g, '<br/>')))
		      });

		    }

		  }

		});
	}

}