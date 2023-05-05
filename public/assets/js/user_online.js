class UserOnline {

	constructor(){
		this.io;
	}

	init() {
		let _this = this;
		this.io = new IO();
		
		this.join();
		this.bind();
	}

	bind() {

		this.io.socket.on('check-user-online', function(res){

			let el = $(".online_status_indicator_"+res.user);

			if(el.length) {
				if(res.online) {
					el.addClass('is-online');
				}else{
					el.removeClass('is-online');
				}				
			}

		});

	}

	join() {
		this.io.join('check-online');
	}

	check(user) {
		this.io.socket.emit('check-user-online', {userId: user});
	}

}