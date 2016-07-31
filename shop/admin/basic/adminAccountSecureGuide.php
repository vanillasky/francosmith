<script type="text/javascript">
if (window.jQuery) {
	jQuery(document).ready(function(){
		jQuery("button.admin-account-secure-guide-toggle").click(function(){
			jQuery(".admin-account-secure-guide").toggleClass("off");
		});;
	});
}
else {
	document.observe("dom:loaded", function(){
		$$("button.admin-account-secure-guide-toggle")[0].observe("click", function(){
			$$(".admin-account-secure-guide")[0].toggleClassName("off");
		});
	});
}
</script>
<style type="text/css">
.admin-account-secure-guide {
	margin-top: 20px;
	margin-bottom: 20px;
}
.admin-account-secure-guide.off button.admin-account-secure-guide-toggle {
	background-image: url(../img/btn_adminSecurity_off.gif);
}
button.admin-account-secure-guide-toggle {
	background-image: url(../img/btn_adminSecurity_on.gif);
	background-repeat: no-repeat;
	background-position: left top;
	background-size: 100% 100%;
	border: none;
	font-size: 0;
	text-indent: -1000px;
	display: block;
	width: 195px;
	height: 30px;
	cursor: pointer;
}
.admin-account-secure-guide-box {
	border: solid #cccccc 1px;
	padding: 23px;
	font-family: dotum;
	font-size: 12px;
	margin-top: 4px;
	line-height: 16px;
}
.admin-account-secure-guide.off .admin-account-secure-guide-box {
	display: none;
}
.admin-account-secure-guide-list {
	padding: 0 0 0 23px;
	margin: 0;
}
.admin-account-secure-guide-list li {
	list-style: decimal;
	padding: 3px 0;
}
.admin-account-secure-guide-list li a {
	font-weight: bold;
}
.admin-account-secure-subaccount-guide {
	border: #cccccc double 3px;
	margin-top: 20px;
	font-family: dotum;
	font-size: 11px;
	color: #627dce;
	line-height: 18px;
}
.admin-account-secure-subaccount-guide .subject {
	padding-left: 5px;
	margin: 7px 0 3px 7px;
	font-weight: bold;
	background-image: url(../img/icon_list.gif);
	background-repeat: no-repeat;
	background-position: left center;
}
.admin-account-secure-subaccount-guide-list {
	padding: 0 0 0 15px;
	margin: 0;
}
.admin-account-secure-subaccount-guide-list li {
	list-style: none;
	padding: 0;
}
.admin-account-secure-subaccount-guide-list li a {
	color: inherit;
	font-weight: bold;
}
.admin-account-secure-subaccount-guide-list li a:hover {
	color: #555555;
}
</style>
<div class="admin-account-secure-guide<?php echo $adminAccountSecureGuideInitStatus ? ' '.$adminAccountSecureGuideInitStatus : ''; ?>">
	<button class="admin-account-secure-guide-toggle">관리자 계정 보안 안전 수칙</button>
	<div class="admin-account-secure-guide-box">
		<ol class="admin-account-secure-guide-list">
			<li>"관리자"계정은 쇼핑몰 소유자(또는 주관리자) 외에 절대 공유 하지 않습니다.</li>
			<li>관리자가 여러명 필요한 경우 부관리자 마다 별도의 아이디를 생성하여 관리 권한을 부여 합니다.</li>
			<li>"관리자"계정 또는 하나의 부관리자 계정을 여러명이 동시에 사용하지 않습니다.</li>
			<li>
				부관리자의 업무에 따라 메뉴 별 접근 권한을 설정하여 사용 합니다.<br/>
				(ex,회원/주문/상품 등 보안이 필요한 메뉴)
			</li>
			<li>필요에 따라 <a href="../basic/adm_basic_login_cert.php" target="_blank">[관리자보안 설정]</a> <a href="../basic/adm_basic_ip_access.php" target="_blank">[관리자IP접속제한 설정]</a> 기능을 함께 사용 합니다.</li>
		</ol>
		<div class="admin-account-secure-subaccount-guide">
			<div class="subject">부관리자 계정 생성 및 관리 방법 (권장사항)</div>
			<ul class="admin-account-secure-subaccount-guide-list">
				<li>- <a href="../basic/adminGroup.php" target="_blank">[기본관리 > 관리자그룹권한설정 > 관리자 그룹추가]</a> 클릭</li>
				<li>- 관리자그룹추가 팝업 > 관리자명 설정 > 그룹레벨 80~99 중 설정 > 관리권한에서 메뉴 별 권한 부여 > 저장</li>
				<li>- 부관리자 쇼핑몰 일반 회원으로 가입</li>
				<li>- <a href="../basic/adminGroup.php" target="_blank">[기본관리 > 관리자그룹권한설정]</a> 또는 <a href="../member/list.php" target="_blank">[회원관리 > 회원리스트 > 회원 정보]</a>의 회원리스트에서 회원 정보 수정</li>
				<li>- 회원정보 > 그룹 항목에 생성한 부관리자 선택 > 저장</li>
			</ul>
		</div>
	</div>
</div>