class Datepicker {

	constructor(input = '#date',readable = true, allowedClear = false, dateFormat = 'yy-mm-dd') {
		this.input = input;
		this.dateFormat = dateFormat;
		this.readable = readable;
		this.allowedClear = allowedClear;
		this.el = null;
	}

	init() {

		$(this.input).datepicker({
      dateFormat: this.dateFormat,
      prevText: '<i class="fas fa-angle-left"></i>',
      nextText: '<i class="fas fa-angle-right"></i>'
	  });

		if(this.readable) {
	  	this.setReadable();
		}

	  // this.bind();
	}

	setReadable() {

		let _this = this;

		this.el = document.createElement('div');
	  this.el.setAttribute('class','date-readable');
	  // bind
	  $(this.el).on('click',function(){
	  	$(_this.input).datepicker('show');
	  });

	  $(this.input).parent().append(this.el);

	  $(this.input).css('color','#fff');
	  // check if has value
	  this.covertToReadable();
	  // bind
	  $(this.input).on('change',function(){
	    _this.covertToReadable();
	  });

	  if(this.allowedClear) {
	  	let a = document.createElement('a');
	  	a.setAttribute('class','date-clear');
	  	a.innerHTML = '<span aria-hidden="true">×</span>';

	  	$(a).on('click',function(){
		  	_this.clearValue();
		  });

	  	$(this.input).parent().append(a);

	  }
	}

	clearValue() {
		$(this.el).text('');
		$(this.input).val('');
	}

	covertToReadable() {
		if($(this.input).val() !== '') {
		  let date = $(this.input).val().split('-');
		  $(this.el).text(parseInt(date[2])+' '+DateTime.getMonthName(parseInt(date[1]))+' '+(parseInt(date[0])+543));
		}
	}

}