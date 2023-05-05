class UploadAvatar {

	constructor() {
		// this.topElem = topElem;
		// this.csrf = csrf;
		this.avatar = null;
		this.dragable = false
		this.last_position = {};
		this.imageX = 0;
		this.imageY = 0;
		this.imageW = 0;
		this.imageH = 0;
		this.frameW = 320;
		this.frameH = 320;
		this.diffX = 0;
		this.diffY = 0;
		this.onclick = false;
	}

	init() {

		this.token = Token.generateToken();

		this.setValue('Avatar[token]',this.token);

		this.bind();
	}

	bind() {

		let _this = this;

		$('.avatar-upload').on('change', 'input[type="file"]', function(){
			_this.preview(this);
		});

		$('.avatar-upload').on('click', '.avatar-delete-btn', function(){
			
			$(this).removeClass('db');
			$(this).addClass('dn');

			$('.avatar-upload').find('input[type="file"]').val(null);
			$('.avatar-upload').find('img').prop('src','');
			$('.avatar-upload').find('img').removeClass('show');

			if($('.avatar-upload').find('input[name="Avatar[filename]"]').length > 0) {
				$('.avatar-upload').find('input[name="Avatar[filename]"]').remove();
			}

			if(this.getAttribute('data-filename') != null) {
				_this.setValue('Avatar[delete]',1);
				this.removeAttribute('data-filename');
			}

		});

		$('#avatar_save_btn').on('click',function(){

			if(_this.avatar === null) {
				return;
			}

			let formData = new FormData(); 
			formData.append('token', _this.token);
			formData.append('image', _this.avatar);
			formData.append('x', _this.imageX);
			formData.append('y', _this.imageY);
			formData.append('type', 'avatar');

			_this.uploadImage(formData);
		});

		$('#avatar_cancel_btn').on('click',function(){
			$('.avatar-upload').find('input[type="file"]').val(null);
			$('#model_upload_avatar').removeClass('show');
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

		$('.avatar-upload-view').on('mousedown','img',function(e){
			e.preventDefault();

			_this.onclick = true;
			_this.dragable = true;
		})

		$('.avatar-upload-view').on('mouseup','img',function(e){
			e.preventDefault();

			_this.onclick = false;
			_this.dragable = false;
			// clear last position
			_this.last_position = {};
		})

		$('.avatar-upload-view').on('mouseenter','img',function(e){
			e.preventDefault();

			if(!_this.onclick) {
				return;
			}

			_this.dragable = true;
		})

		$('.avatar-upload-view').on('mouseout','img',function(e){
			e.preventDefault();
			_this.dragable = false;
		})

		$('.avatar-upload-view').on('mousemove','img',function(e){
			e.preventDefault();

			if(_this.dragable) {
				_this.drag(e.clientX,e.clientY);
	    }

		});

		$('.avatar-upload-view').on('touchmove','img',function(e){
			e.preventDefault();

			_this.drag(e.originalEvent.touches[0].pageX,e.originalEvent.touches[0].pageY);
		});

		$('.avatar-upload-view').on('touchend','img',function(){
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
			  	snackbar.setTitle('ไมาสามารถอัพโหลดรูปนี้ได้');
			  	snackbar.display();
			  }else {

			  	let reader = new FileReader();

			  	reader.onload = function (e) {
			  	
			  		let image = new Image();
            image.onload = function (imageEvent) {

            	let dimension = _this.resizeImage(image.width,image.height);

            	// Resize the image
            	let canvas = document.createElement('canvas');
            	canvas.width = dimension.width;
            	canvas.height = dimension.height;
            	canvas.getContext('2d').drawImage(image, 0, 0, dimension.width, dimension.height);
            	let dataUrl = canvas.toDataURL('image/jpeg');
            	_this.avatar = _this.dataURLToBlob(dataUrl);

            	_this.imageW = dimension.width;
            	_this.imageH = dimension.height;

            	_this.imageX = parseInt((_this.frameW - _this.imageW) / 2);
            	_this.imageY = parseInt((_this.frameH - _this.imageH) / 2);

            	_this.diffX = _this.imageW - _this.frameW;
            	_this.diffY = _this.imageH - _this.frameH;

            	let _img = $('.avatar-upload-panel').find('img');
            	_img.css({
            		'width': _this.imageW,
            		'height': _this.imageH,
            		'top': _this.imageY,
            		'left': _this.imageX,
            	});

            	$('.avatar-upload-view > img').css('cursor','move');

            	_img.prop('src',dataUrl);

            	$('#model_upload_avatar').addClass('show');
            	
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
	    url: "/upload/avatar",
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
	    	Loading.show();
	    },
	    mimeType:"multipart/form-data",
	  });

	  request.done(function (response, textStatus, jqXHR){

	  	if(response.uploaded) {

	  		$('.avatar-upload').find('img').prop('src','/t_image/'+response.filename);
	  		$('.avatar-upload').find('img').addClass('show');

	  		_this.setValue('Avatar[filename]',response.filename);

	  		$('.avatar-upload').find('.avatar-delete-btn').css('display','block');

	  	}else{
	  		const snackbar = new Snackbar();
	  		snackbar.setTitle('ไม่รอบรับรูปภาพนี้ หรือ ไม่สามารถอัพโหลดรูปนี้ได้');
	  		snackbar.display();
	  	}

	  	_this.avatar = null;
	  	$('.avatar-upload').find('input[type="file"]').val(null);

	  	$('#model_upload_avatar').removeClass('show');

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

		let maxSize = 320;

		if (width > height) {
	    width *= maxSize / height;
	    height = maxSize;

    	if (width < maxSize) {
        width *= maxSize / height;
        height = maxSize;
    	}

		}else if(height > width) {
			maxSize = 400;

	    height *= maxSize / width;
	    width = maxSize;

	    if(height < maxSize) {
  	    height *= maxSize / width;
  	    width = maxSize;
  		}
		}else if(width > maxSize){
			width = maxSize;
			height = width;
		}

		return {
			width: width,
			height: height
		};

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

        	$('.avatar-upload-edit').css('left',this.imageX);

      } else if (Math.abs(deltaX) > Math.abs(deltaY) && deltaX < 0) {
          //right

          this.imageX -= Math.abs(deltaX);

					if(this.diffX < Math.abs(this.imageX)) {
						this.imageX = (this.imageW - this.frameW) * -1;
					}

        	$('.avatar-upload-edit').css('left',this.imageX);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY > 0) {
        //up

        this.imageY += Math.abs(deltaY);

        if(this.imageY > 0) {
        	this.imageY = 0;
        }

        $('.avatar-upload-edit').css('top',this.imageY);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY < 0) {
        //down

        this.imageY -= Math.abs(deltaY);

        if(this.diffY < Math.abs(this.imageY)) {
        	this.imageY = (this.imageH - this.frameH) * -1;
        }

        $('.avatar-upload-edit').css('top',this.imageY);
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
	  $('.avatar-upload').append(hidden);
	}

}