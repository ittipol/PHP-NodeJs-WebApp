module.exports = class StringHelper {

	constructor() {}

	static truncString(string, len = 0) {

		if((len <= 0) || typeof string !== 'string') {
		  return '';
		}

		if(string.length <= len) {
		  return string;
		}

		return string.substr(0,len)+'...';
	}

	static getUrlFromString(string) {

		const regex = /(?:http|ftp|https):\/\/(?:[\w_-]+(?:(?:\.[\w_-]+)+))(?:[\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-])?/g;

		let _str = string;

		let m;
		while ((m = regex.exec(string)) !== null) {
		    // This is necessary to avoid infinite loops with zero-width matches
		    if (m.index === regex.lastIndex) {
		        regex.lastIndex++;
		    }
		    
		    // The result can be accessed through the `m`-variable.
		    m.forEach((match, groupIndex) => {
		      _str = _str.replace(match,'<a href="'+match+'">'+this.truncString(match,60)+'</a>');
		    });
		}

		return _str;

	}

	static getHashtagFromString(string) {

		const regex = /(?:#[^=#,:;()*\-^&!%<>|$\'\"\\\\\/\[\]\s]+)/g;
		
		let _str = string;

		let m;
		while ((m = regex.exec(string)) !== null) {
	    // This is necessary to avoid infinite loops with zero-width matches
	    if (m.index === regex.lastIndex) {
	        regex.lastIndex++;
	    }
	    
	    // The result can be accessed through the `m`-variable.
	    m.forEach((match, groupIndex) => {
	       _str = _str.replace(match,'<a href="/hashtag/'+match.substr(1)+'">'+match+'</a>');
	       // _str = _str.replace(match,'<a href="/?q=%23'+match.substr(1)+'">'+match+'</a>');
	    });
		}

		return _str;

	}

}