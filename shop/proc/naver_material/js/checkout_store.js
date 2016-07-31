function iptNullCheck(ipt_obj, ipt_class) {
	if(!ipt_obj.value) ipt_obj.className = ipt_class;
	else ipt_obj.className = "";
}

function setBorder(obj, colorCode) {
	obj.style.borderColor = colorCode;
}

function checkForm(f) {
	if(!f.shopId.value) {
		alert("아이디를 입력해 주세요!");
		f.shopId.focus();
		return false;
	}

	if(!f.shopPassword.value) {
		alert("비밀번호를 입력해 주세요!");
		f.shopPassword.focus();
		return false;
	}

	if(!f.MallUserSSN1.value || f.MallUserSSN1.value.length != 6) {
		alert("주민등록번호 앞자리를 정확히 입력해주세요. (6자)");
		f.MallUserSSN1.focus();
		return false;
	}
	if(!f.MallUserSSN2.value || f.MallUserSSN2.value.length != 7) {
		alert("주민등록번호 뒷자리를 정확히 입력해주세요. (7자)");
		f.MallUserSSN2.focus();
		return false;
	}

	return true;
}

function checkAgreeForm(f) {
	if(!f.agree.checked) {
		alert("네이버 체크아웃 쇼핑몰 회원 구매 서비스를\n이용하시려면 고유식별 정보 제공에 동의하셔야 합니다.");
		f.agree.focus();
		return false;
	}

	if(!f.agree2.checked) {
		alert("네이버 체크아웃 쇼핑몰 회원 구매 서비스를\n이용하시려면 개인정보 제공에 동의하셔야 합니다.");
		f.agree2.focus();
		return false;
	}

	return true;
}