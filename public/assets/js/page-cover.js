class PageCover {

	constructor(id,csrf) {
		this.id = id;
		this.csrf = csrf;
		this.cover = null;
		this.dragable = false;
		this.last_position = {};
		this.imageX = 0;
		this.imageY = 0;
		this.imageW = 0;
		this.imageH = 0;
		this.frameW = 1200;
		this.frameH = 444;
		this.fixedW = 1200;
		this.fixedH = 444;
		this.diffX = 0;
		this.diffY = 0;
		this.onclick = false;
	}

	init() {
		this.frameScale();
		this.bind();
	}

	bind() {

		let _this = this;

		$('#upload_page_cover_btn').on('change', 'input[type="file"]', function(){
			_this.preview(this);
		});

		$('#page_cover_save_btn').on('click',function(){

			if(_this.cover === null) {
				return;
			}

    	if(_this.frameW < this.fixedW) {
  			_this.imageX = Math.floor((_this.imageX * this.fixedW / _this.frameW));
    	}

    	if(_this.frameH < this.fixedH) {
				_this.imageY = Math.floor((_this.imageY * this.fixedH) / _this.frameH);
    	}

			let formData = new FormData(); 
			formData.append('id', _this.id);
			formData.append('image', _this.cover);
			formData.append('x', _this.imageX);
			formData.append('y', _this.imageY);
			formData.append('type', 'cover');

			_this.uploadImage(formData);
		});

		$('#page_cover_cancel_btn').on('click',function(){
			$('#upload_page_cover_btn').find('input[type="file"]').val(null);
			
			$('.page-upload-cover-btn').css('display','block');

			const _modal = new ModalDialog();
			_modal.close();

			// $('.page-header').removeClass('top');
			// $('#c_page_cover').removeClass('db').addClass('dn');
			// $('#r_page_cover').removeClass('dn').addClass('db');
			// $('body').removeClass('overflow-y-hidden');
			// Loading.overlayHide();
		});

		$('.page-cover-wrapper').on('mousedown','#c_page_cover',function(e){
			e.preventDefault();

			_this.onclick = true;
			_this.dragable = true;
		})

		$('.page-cover-wrapper').on('mouseup','#c_page_cover',function(e){
			e.preventDefault();

			_this.onclick = false;
			_this.dragable = false;
			// clear last position
			_this.last_position = {};
		})

		$(document).on('mouseup',function(e){

			if(!_this.onclick) {
				return;
			}
			
			_this.onclick = false;
			_this.dragable = false;
			// clear last position
			_this.last_position = {};
		})

		$('.page-cover-wrapper').on('mouseenter','#c_page_cover',function(e){
			e.preventDefault();

			if(!_this.onclick) {
				return;
			}

			_this.dragable = true;
		})

		$('.page-cover-wrapper').on('mouseout','#c_page_cover',function(e){
			e.preventDefault();
			_this.dragable = false;
		})

		$('.page-cover-wrapper').on('mousemove','#c_page_cover',function(e){
			e.preventDefault();

			if(_this.dragable) {
				_this.drag(e.clientX,e.clientY);
	    }

		});

		$('.page-cover-wrapper').on('touchmove','#c_page_cover',function(e){
			e.preventDefault();
			
			_this.drag(e.originalEvent.touches[0].pageX,e.originalEvent.touches[0].pageY);
		});

		$('.page-cover-wrapper').on('touchend','#c_page_cover',function(){
			// clear last position
			_this.last_position = {};
		});

	}

	preview(input){

		if (input.files && input.files[0]) {

			let parent = $(input).parent();

			if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
			  alert("Your browser does not support new File API! Please upgrade.");
				return false;
			}else{

				let _this = this;
				let file = input.files[0];

			  if(!this.checkImageType(file.type) || !this.checkImageSize(file.size)) {
			  	const snackbar = new Snackbar();
			  	snackbar.setTitle('ไม่สามารถอัพโหลดรูปนี้ได้');
			  	snackbar.display();
			  }else {

			  	let reader = new FileReader();

			  	reader.onload = function (e) {


			  		let image = new Image();
            image.onload = function (imageEvent) {

            	// $('body').addClass('overflow-y-hidden');
            	// $('.page-upload-cover-btn').css('display','none');
            	// $('.page-header').addClass('top');
            	// Loading.overlayShow();

            	let dimension = _this.resizeUploadingImage(image.width,image.height);

            	let canvas = document.createElement('canvas');
            	canvas.width = dimension.width;
            	canvas.height = dimension.height;
            	canvas.getContext('2d').drawImage(image, 0, 0, dimension.width, dimension.height);
            	_this.cover = _this.dataURLToBlob(canvas.toDataURL('image/jpeg'));





            	dimension = _this.resizeImage(image.width,image.height);

            	// Resize the image
            	let _canvas = document.createElement('canvas');
            	_canvas.width = dimension.width;
            	_canvas.height = dimension.height;
            	_canvas.getContext('2d').drawImage(image, 0, 0, dimension.width, dimension.height);
            	let dataUrl = _canvas.toDataURL('image/jpeg');

            	_this.imageW = dimension.width;
            	_this.imageH = dimension.height;

            	_this.imageX = parseInt((_this.frameW - _this.imageW) / 2);
            	_this.imageY = parseInt((_this.frameH - _this.imageH) / 2);

            	_this.diffX = _this.imageW - _this.frameW;
            	_this.diffY = _this.imageH - _this.frameH;

            	// $('#r_page_cover').removeClass('db').addClass('dn');

            	$('#c_page_cover').css({
            		'width': _this.imageW,
            		'height': _this.imageH,
            		'top': _this.imageY,
            		'left': _this.imageX,
            	});

            	$('#c_page_cover').removeClass('dn').addClass('db');

            	$('#c_page_cover').css('cursor','move');

            	$('#c_page_cover').prop('src',dataUrl);

            }
            image.src = e.target.result;

			  	}
			  	
			  	reader.readAsDataURL(file);

			  }

			  const _modal = new ModalDialog();
			  _modal.show('#modal_shop_cover');

			}

		}

	}

	uploadImage(data) {

		let _this = this;

		let request = $.ajax({
	    url: "/upload/page/image",
	    type: "POST",
	    headers: {
	    	'x-csrf-token': this.csrf
	    },
	    data: data,
	    dataType: 'json',
	    contentType: false,
	    cache: false,
	    processData:false,
	    beforeSend: function( xhr ) {
	    	// $('.page-header').removeClass('top');
	    	Loading.show();
	    },
	    mimeType:"multipart/form-data",
	  });

	  request.done(function (response, textStatus, jqXHR){

	  	if(response.uploaded) {

	  		$('#c_page_cover').prop('src','');
	  		$('.page-shop-cover > img').prop('src','/get_image/'+response.filename);
	  		$('.page-shop-cover > .banner-bg').prop('src','/get_image/'+response.filename);

	  	}else{
	  		const snackbar = new Snackbar();
	  		snackbar.setTitle('ไม่รอบรับรูปภาพนี้ หรือ ไม่สามารถอัพโหลดรูปนี้ได้');
	  		snackbar.display();
	  	}

	  	_this.cover = null;
	  	$('#upload_page_cover_btn').find('input[type="file"]').val(null);

	  	$('.page-upload-cover-btn').css('display','block');

	  	// $('#c_page_cover').removeClass('db').addClass('dn');
	  	// $('#r_page_cover').removeClass('dn').addClass('db');
	  	// $('body').removeClass('overflow-y-hidden');
	  	Loading.hide();

	  	const _modal = new ModalDialog();
	  	_modal.close();
	  	
	  });

	  request.fail(function (jqXHR, textStatus, errorThrown){
	    console.error(
	        "The following error occurred: "+
	        textStatus, errorThrown
	    );
	  });

	}

	checkImageType(type){
		let allowedFileTypes = ['image/jpg','image/jpeg','image/png', 'image/pjpeg'];

		let allowed = false;

		for (let i = 0; i < allowedFileTypes.length; i++) {
			if(type == allowedFileTypes[i]){
				allowed = true;
				break;						
			}
		};

		return allowed;
	}

	checkImageSize(size) {
		// 5MB
		let maxSize = 5242880;

		let allowed = false;

		if(size <= maxSize){
			allowed = true;
		}

		return allowed;
	}

	dataURLToBlob(dataURL) {
    let BASE64_MARKER = ';base64,';
    if (dataURL.indexOf(BASE64_MARKER) == -1) {
        let parts = dataURL.split(',');
        let contentType = parts[0].split(':')[1];
        let raw = parts[1];

        return new Blob([raw], {type: contentType});
    }

    let parts = dataURL.split(BASE64_MARKER);
    let contentType = parts[0].split(':')[1];
    let raw = window.atob(parts[1]);
    let rawLength = raw.length;

    let uInt8Array = new Uint8Array(rawLength);

    for (let i = 0; i < rawLength; ++i) {
        uInt8Array[i] = raw.charCodeAt(i);
    }

    return new Blob([uInt8Array], {type: contentType});
	}

	resizeImage(width,height) {

		let dimension = this.resize(this.frameW,width,height);

		if(dimension.width < this.frameW) {
			dimension.height *= this.frameW / dimension.width;
			dimension.width = this.frameW;
		}

		if(dimension.height < this.frameH) {
			dimension.width *= this.frameH / dimension.height;
			dimension.height = this.frameH;
		}

		return dimension;
	}

	resizeUploadingImage(width,height) {
		let dimension = this.resize(this.fixedW,width,height);

		if(dimension.width < this.fixedW) {
			dimension.height *= this.fixedW / dimension.width;
			dimension.width = this.fixedW;
		}

		if(dimension.height < this.fixedH) {
			dimension.width *= this.fixedH / dimension.height;
			dimension.height = this.fixedH;
		}

		return dimension;
	}

	resize(maxSize,width,height) {

		if ((width > height) && (width > maxSize)) {
	    height *= maxSize / width;
	    width = maxSize;
		}else if((height > width) && (height > maxSize)) {
	    width *= maxSize / height;
	    height = maxSize;
		}else if(width > maxSize){
			width = maxSize;
			height = width;
		}

		return {
			width: width,
			height: height
		};

	}

	frameScale() {
		
		let frameW = $('#page_cover_wrapper').width();

		this.frameH = parseInt((this.fixedH * ((frameW * 100) / this.fixedW)) / 100);
		this.frameW = parseInt(frameW);

		$('#page_cover_wrapper').css({
			'height': this.frameH,
			'width': this.frameW
		});
	}

	drag(x,y) {

		if (typeof(this.last_position.y) != 'undefined') {

			//get the change from last position to this position
      let deltaX = this.last_position.x - x,
          deltaY = this.last_position.y - y;

      //check which direction had the highest amplitude and then figure out direction by checking if the value is greater or less than zero
      if (Math.abs(deltaX) > Math.abs(deltaY) && deltaX > 0) {
          //left

          this.imageX += Math.abs(deltaX);

          if(this.imageX > 0) {
          	this.imageX = 0;
          }

        	$('#c_page_cover').css('left',this.imageX);

      } else if (Math.abs(deltaX) > Math.abs(deltaY) && deltaX < 0) {
          //right

          this.imageX -= Math.abs(deltaX);

					if(this.diffX < Math.abs(this.imageX)) {
						this.imageX = (this.imageW - this.frameW) * -1;
					}

        	$('#c_page_cover').css('left',this.imageX);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY > 0) {
        // => up

        this.imageY += Math.abs(deltaY);

        if(this.imageY >= 0) {
        	this.imageY = 0
        }

        $('#c_page_cover').css('top',this.imageY);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY < 0) {
        // => down

        this.imageY -= Math.abs(deltaY);

        if(this.diffY < Math.abs(this.imageY)) {
        	this.imageY = (this.imageH - this.frameH) * -1;
        }

        $('#c_page_cover').css('top',this.imageY);
      }

    }

    //set the new last position to the current for next time
    this.last_position = {
      x : x,
      y : y
    };

	}

}