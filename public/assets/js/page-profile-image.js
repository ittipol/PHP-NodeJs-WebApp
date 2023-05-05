class PageProfileImage {

	constructor(id,csrf) {
		this.id = id;
		this.csrf = csrf;
		this.profileImage = null;
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
		this.bind();
	}

	bind() {

		let _this = this;

		$('#upload_page_profile_image_btn').on('change', 'input[type="file"]', function(){
			_this.preview(this);
		});

		$('#page_profile_image_save_btn').on('click',function(){

			if(_this.profileImage === null) {
				return;
			}

			let formData = new FormData(); 
			formData.append('id', _this.id);
			formData.append('image', _this.profileImage);
			formData.append('x', _this.imageX);
			formData.append('y', _this.imageY);
			formData.append('type', 'avatar');

			_this.uploadImage(formData);
		});

		$('#page_profile_image_cancel_btn').on('click',function(){
			$('#upload_page_profile_image_btn').find('input[type="file"]').val(null);
			$('#model_upload_page_profile_image').removeClass('show');
		});

		$(document).on('mouseup',function(e){
			if(!_this.onclick) {
				return;
			}
			
			_this.onclick = false;
			_this.dragable = false;
		})

		$('.page-profile-image-view').on('mousedown','img',function(e){
			e.preventDefault();
			_this.onclick = true;
			_this.dragable = true;
		})

		$('.page-profile-image-view').on('mouseup','img',function(e){
			e.preventDefault();
			_this.onclick = false;
			_this.dragable = false;
		})

		$('.page-profile-image-view').on('mouseenter','img',function(e){
			e.preventDefault();

			if(!_this.onclick) {
				return;
			}

			_this.dragable = true;
		})

		$('.page-profile-image-view').on('mouseout','img',function(e){
			e.preventDefault();
			_this.dragable = false;
		})

		$('.page-profile-image-view').on('mousemove','img',function(e){
			e.preventDefault();

			if(_this.dragable) {
				_this.drag(e.clientX,e.clientY);
	    }

		});

		// $('.page-profile-image-view').on('touchstart','img',function(e){
		// 	e.preventDefault();
		// 	_this.onclick = true;
		// 	_this.dragable = true;
		// })

		// $('.page-profile-image-view').on('touchend','img',function(e){
		// 	e.preventDefault();
		// 	_this.onclick = false;
		// 	_this.dragable = false;
		// })

		$('.page-profile-image-view').on('touchmove','img',function(e){
			e.preventDefault();

			_this.drag(e.originalEvent.touches[0].pageX,e.originalEvent.touches[0].pageY);

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
            	_this.profileImage = _this.dataURLToBlob(dataUrl);

            	_this.imageW = dimension.width;
            	_this.imageH = dimension.height;

            	_this.imageX = parseInt((_this.frameW - _this.imageW) / 2);
            	_this.imageY = parseInt((_this.frameH - _this.imageH) / 2);

            	_this.diffX = _this.imageW - _this.frameW;
            	_this.diffY = _this.imageH - _this.frameH;

            	let _img = $('.page-profile-image-panel').find('img');
            	_img.css({
            		'width': _this.imageW,
            		'height': _this.imageH,
            		'top': _this.imageY,
            		'left': _this.imageX,
            	});

            	$('.page-profile-image-view > img').css('cursor','move');

            	_img.prop('src',dataUrl);

            	$('#model_upload_page_profile_image').addClass('show');
            	
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
	    	Loading.show();
	    },
	    mimeType:"multipart/form-data",
	  });

	  request.done(function (response, textStatus, jqXHR){

	  	if(response.uploaded) {

	  		// $('.page-profile-image-view > img').css('cursor','move');

	  		$('.page-profile-image > img').prop('src','/get_image/'+response.filename);
	  		// $('.page-profile-image > img').css({
	  		// 	'width': _this.imageW,
	  		// 	'height': _this.imageH,
	  		// 	'top': _this.imageY,
	  		// 	'left': _this.imageX,
	  		// });

	  		// const snackbar = new Snackbar();
	  		// snackbar.setTitle('รูปภาพถูกเปลี่ยนแล้ว');
	  		// snackbar.display();

	  	}else{
	  		const snackbar = new Snackbar();
	  		snackbar.setTitle('ไม่รอบรับรูปภาพนี้ หรือ ไม่สามารถอัพโหลดรูปนี้ได้');
	  		snackbar.display();
	  	}

	  	_this.profileImage = null;
	  	$('#upload_page_profile_image_btn').find('input[type="file"]').val(null);

	  	$('#model_upload_page_profile_image').removeClass('show');

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

		// if ((width > height) && (width > maxSize)) {
	 //    width *= maxSize / height;
	 //    height = maxSize;
		// }else if((height > width) && (height > maxSize)) {
		// 	maxSize = 400;

	 //    height *= maxSize / width;
	 //    width = maxSize;
		// }else if(width > maxSize){
		// 	width = maxSize;
		// 	height = width;
		// }

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

          this.imageX += 8

          if(this.imageX > 0) {
          	this.imageX = 0;
          }

        	$('.page-profile-image-edit').css('left',this.imageX);

      } else if (Math.abs(deltaX) > Math.abs(deltaY) && deltaX < 0) {
          //right

          this.imageX -= 8

					if(this.diffX < Math.abs(this.imageX)) {
						this.imageX = (this.imageW - this.frameW) * -1;
					}

        	$('.page-profile-image-edit').css('left',this.imageX);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY > 0) {
        //up

        this.imageY += 8

        if(this.imageY > 0) {
        	this.imageY = 0;
        }

        $('.page-profile-image-edit').css('top',this.imageY);
      } else if (Math.abs(deltaY) > Math.abs(deltaX) && deltaY < 0) {
        //down

        this.imageY -= 8

        if(this.diffY < Math.abs(this.imageY)) {
        	this.imageY = (this.imageH - this.frameH) * -1;
        }

        $('.page-profile-image-edit').css('top',this.imageY);
      }

    }

    //set the new last position to the current for next time
    this.last_position = {
      x : x,
      y : y
    };
	}

}