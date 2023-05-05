class Loading {

	constructor() {}

	static show() {
		$('#global_overlay').addClass('show');
		$('#global_loading_indicator').addClass('show');
	}

	static hide() {
		$('#global_overlay').removeClass('show');
		$('#global_loading_indicator').removeClass('show');
	}

	static overlayShow() {
		$('#global_overlay').addClass('show');
	}

	static overlayHide() {
		$('#global_overlay').removeClass('show');
	}

}