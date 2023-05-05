class UploadPreview {

	constructor(topElem,model) {
		this.topElem = topElem;
		this.model = model;
		this.banner = null;
		this.dragable = false;
		this.last_position = {};
		this.imageX = 0;
		this.imageY = 0;
		this.imageW = 0;
		this.imageH = 0;
		this.frameW = 560; // 400
		this.frameH = 315; // 225
		this.diffX = 0;
		this.diffY = 0;
		this.onclick = false;
		this.token = null;
	}

	init() {

		this.token = Token.generateToken();

		this.setValue('Preview[token]',this.token);

		this.frameScale();
		this.bind();

	}

	bind() {

		let _this = this;

		$(this.topElem).on('change', 'input[type="file"]', function(){
			_this.preview(this);
		});

		$(this.topElem).on('click','.preview-save-btn',function(){

			if(_this.banner === null) {
				return;
			}

    	if(_this.frameW < 560) {
  			_this.imageX = Math.floor((_this.imageX * 560 / _this.frameW));
    	}

    	if(_this.frameH < 315) {
				_this.imageY = Math.floor((_this.imageY * 315) / _this.frameH);
    	}

			let formData = new FormData(); 
			formData.append('model', _this.model);
			formData.append('token', _this.token);
			formData.append('image', _this.banner);
			formData.append('x', _this.imageX);
			formData.append('y', _this.imageY);
			formData.append('type', 'preview');

			_this.uploadImage(formData);
		});

		$(this.topElem).on('click','.preview-cancel-btn',function(){

			$(_this.topElem).find('input[type="file"]').val(null);

			$(_this.topElem).removeClass('top');
			$(_this.topElem).find('.c-data-preview').removeClass('db').addClass('dn');
			$(_this.topElem).find('.r-data-preview').removeClass('dn').addClass('db');
			$('body').removeClass('overflow-y-hidden');
			Loading.overlayHide();
		});

		$(this.topElem).on('mousedown','.c-data-preview',function(e){
			e.preventDefault();

			_this.onclick = true;
			_this.dragable = true;
		})

		$(this.topElem).on('mouseup','.c-data-preview',function(e){
			e.preventDefault();

			_this.onclick = false;
			_this.dragable = false;
			// clear last position
			_this.last_position = {};
		});

		$(this.topElem).on('click','.data-preview-delete-btn',function(){
			// remove
			$(this).removeClass('db').addClass('dn');

			$(_this.topElem).find('input[type="file"]').val(null);
			$(_this.topElem).find('.c-data-preview').prop('src','');
			$(_this.topElem).find('.r-data-preview').prop('src','');

			if($(_this.topElem).find('input[name="Preview[filename]"]').length > 0) {
				$(_this.topElem).find('input[name="Preview[filename]"]').val('');
			}

			if(this.getAttribute('data-filename') != null) {
				_this.setValue('Preview[delete]',1);
				this.removeAttribute('data-filename');
			}

		});

		$(document).on('mouseup',function(e){

			if(!_this.onclick) {
				return;
			}
			
			_this.onclick = false;
			_this.dragable = false;
			// clear last position
			_this.last_position = {};
		})

		$(this.topElem).on('mouseenter','.c-data-preview',function(e){
			e.preventDefault();

			if(!_this.onclick) {
				return;
			}

			_this.dragable = true;
		})

		$(this.topElem).on('mouseout','.c-data-preview',function(e){
			e.preventDefault();
			_this.dragable = false;
		})

		$(this.topElem).on('mousemove','.c-data-preview',function(e){
			e.preventDefault();

			if(_this.dragable) {
				_this.drag(e.clientX,e.clientY);
	    }

		});

		$(this.topElem).on('touchmove','.c-data-preview',function(e){
			e.preventDefault();

			_this.drag(e.originalEvent.touches[0].pageX,e.originalEvent.touches[0].pageY);
		});

		$(this.topElem).on('touchend','.c-data-preview',function(){
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

            	$(document).scrollTop($(_this.topElem).offset().top - 120);

            	$('body').addClass('overflow-y-hidden');
            	$(_this.topElem).addClass('top');
            	Loading.overlayShow();

            	let dimension = _this.resizeUploadingImage(image.width,image.height);

            	let canvas = document.createElement('canvas');
            	canvas.width = dimension.width;
            	canvas.height = dimension.height;
            	canvas.getContext('2d').drawImage(image, 0, 0, dimension.width, dimension.height);
            	_this.banner = _this.dataURLToBlob(canvas.toDataURL('image/jpeg'));




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

            	let cPreview = $(_this.topElem).find('.c-data-preview');

            	$(_this.topElem).find('.r-data-preview').removeClass('db').addClass('dn');

            	cPreview.css({
            		'width': _this.imageW,
            		'height': _this.imageH,
            		'top': _this.imageY,
            		'left': _this.imageX,
            	});

            	cPreview.removeClass('dn').addClass('db');

            	cPreview.prop('src',dataUrl);

            }
            image.src = e.target.result;

			  	}
			  	
			  	reader.readAsDataURL(file);

			  }
			}

		}

	}

	uploadImage(data) {

		let _this = this;

		let request = $.ajax({
	    url: "/upload/preview",
	    type: "POST",
	    headers: {
	    	'x-csrf-token': $('[name="_token"]').val()
	    },
	    data: data,
	    dataType: 'json',
	    contentType: false,
	    cache: false,
	    processData:false,
	    beforeSend: function( xhr ) {
	    	$(_this.topElem).removeClass('top');
	    	Loading.show();
	    },
	    mimeType:"multipart/form-data",
	  });

	  request.done(function (response, textStatus, jqXHR){

	  	if(response.uploaded) {

	  		$(_this.topElem).find('.c-data-preview').prop('src','');
	  		$(_this.topElem).find('.r-data-preview').prop('src','/t_image/'+response.filename);

	  		let obj = $(_this.topElem).find('.data-preview-delete-btn')[0];

	  		if(obj.getAttribute('data-filename') != null) {
	  			_this.setValue('Preview[delete]',obj.getAttribute('data-filename'));
	  			obj.removeAttribute('data-filename');
	  		}

	  		$(_this.topElem).find('.data-preview-delete-btn').removeClass('dn').addClass('db');

	  		if($(_this.topElem).find('input[name="Preview[filename]"]').length > 0) {
	  			$(_this.topElem).find('input[name="Preview[filename]"]').remove();
	  		}

	  		_this.setValue('Preview[filename]',response.filename);

	  	}else{
	  		const snackbar = new Snackbar();
	  		snackbar.setTitle('ไม่รอบรับรูปภาพนี้ หรือ ไม่สามารถอัพโหลดรูปนี้ได้');
	  		snackbar.display();
	  	}

	  	_this.banner = null;
	  	$(_this.topElem).find('input[type="file"]').val(null);

	  	$(_this.topElem).find('.c-data-preview').removeClass('db').addClass('dn');
	  	$(_this.topElem).find('.r-data-preview').removeClass('dn').addClass('db');
	  	$('body').removeClass('overflow-y-hidden');
	  	Loading.hide();
	  	
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
		let dimension = this.resize(560,width,height);

		if(dimension.width < 560) {
			dimension.height *= 560 / dimension.width;
			dimension.width = 560;
		}

		if(dimension.height < 315) {
			dimension.width *= 315 / dimension.height;
			dimension.height = 315;
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

		let frameW = $(this.topElem).find('.data-preview-wrapper').width();

		this.frameH = parseInt((315 * ((frameW * 100) / 560)) / 100);
		this.frameW = parseInt(frameW);

		$(this.topElem).find('.data-preview-wrapper').css({
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

        	$(this.topElem).find('.c-data-preview').css('left',this.imageX);

      } else if (Math.abs(deltaX) > Math.abs(deltaY) && deltaX < 0) {
          //right

          this.imageX -= Math.abs(deltaX);

					if(this.diffX < Math.abs(this.imageX)) {
						this.imageX = (this.imageW - this.frameW) * -1;
					}

        	$(this.topElem).find('.c-data-preview').css('left',this.imageX);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY > 0) {
        // => up

        this.imageY += Math.abs(deltaY);

        if(this.imageY >= 0) {
        	this.imageY = 0
        }

        $(this.topElem).find('.c-data-preview').css('top',this.imageY);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY < 0) {
        // => down

        this.imageY -= Math.abs(deltaY);

        if(this.diffY < Math.abs(this.imageY)) {
        	this.imageY = (this.imageH - this.frameH) * -1;
        }

        $(this.topElem).find('.c-data-preview').css('top',this.imageY);
      }

    }

    //set the new last position to the current for next time
    this.last_position = {
      x : x,
      y : y
    };

	}

	setValue(name,value) {
		let hidden = document.createElement('input');
	  hidden.setAttribute('type','hidden');
	  hidden.setAttribute('name',name);
	  hidden.setAttribute('value',value);
	  $(this.topElem).append(hidden);
	}

}