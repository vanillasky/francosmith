// 회원 로그인상태에 따른 메뉴표시
(function(updateData){
	if (updateData.userStatus === true) {
		var logoutElement = document.getElementsByClassName("user-status-login");
		for (var index = 0; index < logoutElement.length; index++) {
			logoutElement[index].style.display = "";
		}
		var loginElement = document.getElementsByClassName("user-status-logout");
		for (var index = 0; index < loginElement.length; index++) {
			loginElement[index].parentNode.removeChild(loginElement[index]);
		}
	}
	if (updateData.userStatus === false) {
		var logoutElement = document.getElementsByClassName("user-status-login");
		for (var index = 0; index < logoutElement.length; index++) {
			logoutElement[index].parentNode.removeChild(logoutElement[index]);
		}
		var loginElement = document.getElementsByClassName("user-status-logout");
		for (var index = 0; index < loginElement.length; index++) {
			loginElement[index].style.display = "";
		}
	}
})(updateData);