/**
 * GODO password strength checker.
 *
 */
var nsGodo_PasswordStrength = function(){

	return {
		// result constant.
		RESULT : {
			length_too_short		: {level : 0, levelText : '사용불가', msg : '비밀번호는 10자 이상 입력해 주세요.'},
			length_too_long			: {level : 0, levelText : '사용불가', msg : '비밀번호는 16자 이하 입력해 주세요.'},
			mixstatus				: {level : 0, levelText : '사용불가', msg : '영문대문자(26개), 영문소문자(26개), 숫자(10개), 특수문자(32개) 중 최소 2가지가 조합되어야 합니다.'},
			invalid_character		: {level : 0, levelText : '사용불가', msg : '사용할 수 없는 문자가 포함되어 있습니다.'},
			continuous_character	: {level : 0, levelText : '사용불가', msg : '3자 이상 연속적인 문자는 사용할 수 없습니다.'},
			on_blacklist			: {level : 0, levelText : '사용불가', msg : '사용할 수 없는 단어가 포함되어 있습니다.'},
			normal					: {level : 1, levelText : '낮음', msg : '예상하기 쉬운 비밀번호입니다.'},
			nightmare				: {level : 2, levelText : '보통', msg : '보통 보안수준의 비밀번호입니다.'},
			hell					: {level : 3, levelText : '높음', msg : '예측하기 힘든 비밀번호로 더욱 안전합니다.'},
			inferno					: {level : 4, levelText : '높음', msg : '예측하기 힘든 비밀번호로 더욱 안전합니다.'}
		},
		CONFIG : {
			// available characters.
			chars : {
				digit: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
				upper: ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'],
				lower: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
				punct: ['!', '"', '#', '$', '%', '&', '\'', '(', ')', '*', '+', ',', '-', '.', '/', ':', ';', '<', '=', '>', '?', '@', '[', '\\', ']', '^', '_', '`', '{', '|', '}', '~']
			},

			blacklist : ['qwertyuiop[]\\','asdfghjkl;\'','zxcvbnm,./','QWERTYUIOP{}|','ASDFGHJKL:"','ZXCVBNM<>?'],
			blacklist_minlength : 4,

			// minimum password length.
			minlength : 10,

			// maximum password length.
			maxlength : 16
		},
		trim : function(str) {
			return str.replace(/(^\s*)|(\s*$)/g, '');
		},
		in_array: function(arr, val){
			for (var i = 0, m = arr.length; i < m; i++)
				if (arr[i] === val)
					return true;

			return false;
		},
		appendBlacklist : function(param) {

			// 블랙리스트에 param 내용 추가
			if (typeof param.form == 'object' && typeof param.fields == 'object') {
				var _el;

				for (var i=0,m=param.form.elements.length;i<m ;i++) {

					_el = param.form.elements[i];

					if (this.in_array(param.fields, _el.name) && _el.value && !this.in_array(this.CONFIG.blacklist, _el.value)) {
						this.CONFIG.blacklist.push(_el.value);
					}
				}
			}

		},
		wordSpliter : function(str) {

			var minLength = this.CONFIG.blacklist_minlength;

			if (str.length < minLength) return [];

			var words = [];
			var word;
			var i,j,m,n;

			var chars = str.split('');

			// 정방향
			for (i=0,m=chars.length;i<m ;i++) {

				word = [];

				for (j=i,n=j+minLength; j<n;j++) {
					if (chars[j]) word.push( chars[j] );
				}

				if (word.length == minLength) {
					words.push(word.join(''));
				}
			}

			// 역방향
			chars.reverse();

			for (i=0,m=chars.length;i<m ;i++) {

				word = [];

				for (j=i,n=j+minLength; j<n;j++) {
					if (chars[j]) word.push( chars[j] );
				}

				if (word.length == minLength) {
					words.push(word.join(''));
				}
			}

			return words;

		},
		checkMinLength : function(str) {
			if (str.length < this.CONFIG.minlength) {
				return false;
			}

			return true;
		},
		checkMaxLength : function(str) {
			if (str.length > this.CONFIG.maxlength) {
				return false;
			}

			return true;
		},
		checkValidChar : function(str) {

			var _tmp = this.CONFIG.chars.digit.join('') + this.CONFIG.chars.upper.join('') + this.CONFIG.chars.lower.join('') + this.CONFIG.chars.punct.join('');

			for (var i=0,m=str.length;i<m;i++) {

				if (_tmp.indexOf( str.charAt(i) ) < 0) {
					return false;
				}
			}

			return true;

		},
		checkContinueChar : function(str) {

			var char, chars = [];
			var code,precode = 0;

			for (var i=0,m=str.length;i<m;i++) {

				code = str.charCodeAt(i);
				char = str.charAt(i);

				chars.push( [char, Math.abs(code - precode)] );

				precode = code;
			}

			try
			{
				for (var i=0,m=chars.length;i<m;i++) {

					if (chars[i+1][1] == chars[i+2][1]) {
						return false;
					}
				}
			}
			catch (e) { }

			return true;
		},
		checkBlackList : function(str) {

			var blacklists=[];

			var i,j,m,n;
			var words;

			for (i=0,m=this.CONFIG.blacklist.length;i<m ;i++ ) {

				words = this.wordSpliter( this.CONFIG.blacklist[i] );

				for (j=0,n=words.length; j<n;j++) {
					if (words[j]) blacklists.push( words[j] );
				}
			}

			for (i=0,m=blacklists.length;i<m ;i++ ) {
				if (str.indexOf(blacklists[i]) > -1) {
					return false;
				}
			}

			return true;

		},
		getPasswordStrength : function(pwd){

			var char;
			var symbol = {
				digit: false,
				lower: false,
				upper: false,
				punct: false
			};
			var passwordCount = 0;

			for (var i = 0, m = pwd.length; i < m; i++) {

				char = pwd.charAt(i);

				if (symbol.digit == false && this.in_array(this.CONFIG.chars.digit, char)) {
					symbol.digit = true;
					passwordCount++;
				}

				if (symbol.lower == false && this.in_array(this.CONFIG.chars.lower, char)) {
					symbol.lower = true;
					passwordCount++;
				}

				if (symbol.upper == false && this.in_array(this.CONFIG.chars.upper, char)) {
					symbol.upper = true;
					passwordCount++;
				}

				if (symbol.punct == false && this.in_array(this.CONFIG.chars.punct, char)) {
					symbol.punct = true;
					passwordCount++;
				}

			}

			if (passwordCount == 4) {
				return this.RESULT.hell;
			} else if (passwordCount == 3) {
				return this.RESULT.nightmare;
			} else if (passwordCount == 2) {
				return this.RESULT.normal;
			} else {
				return this.RESULT.mixstatus;
			}

		},
		check : function( $var ) {

			switch (typeof $var) {

				case 'object' :
					var pwd = $var.value || $var.innerText;
					break;
				default :
					if (document.getElementById($var)) {
						var pwd = document.getElementById($var).value || document.getElementById($var).innerText;
					}
					else {
						var pwd = $var;
					}
					break;
			}

			pwd = this.trim(pwd);

			// 최소길이
			if (!this.checkMinLength(pwd)) return this.RESULT.length_too_short;

			// 최대길이
			if (!this.checkMaxLength(pwd)) return this.RESULT.length_too_long;

			// 허용문자
			//if (!this.checkValidChar(pwd)) return this.RESULT.invalid_character;

			// 연속(특정간격) 문자
			//if (!this.checkContinueChar(pwd)) return this.RESULT.continuous_character;

			// 블랙리스트
			//if (!this.checkBlackList(pwd)) return this.RESULT.on_blacklist;

			// 암호강도
			return this.getPasswordStrength(pwd);

		}

	}
}();