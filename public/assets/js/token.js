class Token {

	constructor() {}

	static generateToken(tokenLen = 64) {
		let codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	  codeAlphabet += "abcdefghijklmnopqrstuvwxyz";
	  codeAlphabet += "0123456789";

	  let code = '';
	  let len = codeAlphabet.length;

	  for (let i = 0; i <= tokenLen; i++) {
	  	// code += codeAlphabet[Math.floor(Math.random() * (len - 0) + 0)];
	  	code += codeAlphabet[Math.floor(Math.random() * (len - 1))];
	  };

		return code;
	}

}