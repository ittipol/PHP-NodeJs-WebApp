String.prototype.stripTags = function()
{
  return this.replace(/<\/?\w+[^>]*\/?>/g, '');
};

class HeaderBar {
	constructor() {}

	init() {
		if ($(window).scrollTop() > 10){
		  $('.gn-menu-main').addClass('bg-header');
		}

		this.bind();
	}

	bind() {
		$(window).on('scroll',function(){
		  if ($(this).scrollTop() > 10){
		    $('.gn-menu-main').addClass('bg-header');
		  } else {
		    $('.gn-menu-main').removeClass('bg-header');
		  }
		});
	}

}

class FloatingButton {

	constructor() {}

	hideButton(show) {
	  if(show) {
	    $('.btn-hide-when-input').removeClass('db').addClass('dn');
	  }else {
	    $('.btn-hide-when-input').removeClass('dn').addClass('db');
	  }
	}

}

$(document).ready(function(){
	const headerBar = new HeaderBar();
	headerBar.init();

	if(window.innerWidth <= 1024) {
		$(document).on('focus','input',function(){

			switch($(this).attr('type')) {
				case 'text':
					floatingBtnShow();
				break;

				case 'tel':
					floatingBtnShow();
				break;

				case 'email':
					floatingBtnShow();
				break;

				case 'number':
					floatingBtnShow();
				break;

				case 'password':
					floatingBtnShow();
				break;
			}

		});

		$(document).on('blur','input',function(){

			switch($(this).attr('type')) {
				case 'text':
					floatingBtnHide();
				break;

				case 'tel':
					floatingBtnHide();
				break;

				case 'email':
					floatingBtnHide();
				break;

				case 'number':
					floatingBtnHide();
				break;

				case 'password':
					floatingBtnHide();
				break;
			}

		});

		$(document).on('focus','textarea',function(){
			floatingBtnShow();
		});

		$(document).on('blur','textarea',function(){
			floatingBtnHide();
		});

		function floatingBtnShow() {
			$('.btn-hide-when-input').removeClass('db').addClass('dn');
			$('#main_btn_group').removeClass('db').addClass('dn');
			// $('#cart_check_out_btn').removeClass('db').addClass('dn');
		}

		function floatingBtnHide() {
			$('.btn-hide-when-input').removeClass('dn').addClass('db');
			$('#main_btn_group').removeClass('dn').addClass('db');
			// $('#cart_check_out_btn').removeClass('dn').addClass('db');
		}
	}
});