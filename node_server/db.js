const env = require('./env');
const mysql = require('mysql');
const connection = mysql.createConnection({
    host     : env.DB_HOST,
    user     : env.DB_USERNAME,
    password : env.DB_PASSWORD,
    database : env.DB_DATABASE,
    timezone : 'Asia/Bangkok'
});

connection.connect(function(err) {
  if (err) throw err;
	console.log('Database connected');
});

module.exports = connection;