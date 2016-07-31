<?
include "../../lib/library.php";

// 로그인에서 관리자보안 인증상태 체크
$alCert = Core::loader('adminLoginCert');
$alStat = $alCert->loginStatus();
if ($alStat == 'failure') {
	go("../login/adm_login_cert.php");
}
else if ($alStat == 'success') {
	unset($ici_admin);
}

// 관리자 체크
if ($ici_admin) go("../index.php");

setCookie('Xtime',time(),0,'/');

### 보안서버용 로긴url
if ($cfg['ssl'] == "1") { //보안서버를 사용하면...
	$loginActionUrl = $sitelink->link('member/login_ok.php','ssl');
} else {
	$loginActionUrl = "../../member/login_ok.php";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="../common.js"></script>
</head>

<body onload="document.getElementsByName('m_id')[0].focus();" style="margin:0; background:url(../img/login/intro_bg_back.jpg) top left repeat-x #ffffff;">
<form method="post" action="<?=$loginActionUrl?>" onsubmit="return chkForm(this);">
<input type="hidden" name="returnUrl" value="../admin/index.php">
<input type="hidden" name="adm_login" value="1" />

<div style="background:url(../img/login/intro_logo.jpg) no-repeat left top; height:33px"></div>
<div style="background:url(../img/login/intro_bg.jpg) center -33px no-repeat; height:790px;padding-top:362px; text-align:center;">
	<div style="width:435px;margin:0 auto;text-align:left;">
		<table cellpadding="0" cellspacing="0" border="0" style="margin-left:67px;">
		<tr>
			<td width="240">
				<div><input type=text name="m_id" style="width:230px;height:22px;font:10pt tahoma;border-width:0px;padding-left:3px;background:url(../img/login/bg_id.gif) no-repeat 4px left;" required label="아이디" value="<?=$_GET[m_id]?>" onKeyUp="if(this.value != '') this.style.backgroundImage='';"></div>
				<div style="padding-top:5px"><input type="password" name="password" style="width:230px;height:22px;font:10pt tahoma;border-width:0px;padding-left:3px;background:url(../img/login/bg_pass.gif) no-repeat 4px left;" required label="비밀번호" value="<?=$_GET[password]?>" onKeyUp="if(this.value != '') this.style.backgroundImage='';"></div>
			</td>
			<td style="padding-left:10px"><input type="image" src="../img/login/btn_login.gif" border="0"></td>
		</tr>
		</table>

		<? if($cfg[ssl] == "1" ) { ?>
		<script type="text/javascript">
		<!--
		function checkedSSL(chkObj) {
			var form = chkObj.form;
			if(chkObj.checked) { //보안접속체크
				form.action="<?=$loginActionUrl?>";
			} else { //보안접속체크해제
				form.action="../../member/login_ok.php";
			}
		}
		//-->
		</script>
		<div style="margin:8px 0 0 60px;">
			<label><input type="checkbox" name="sslcheck" value="1" checked onclick="checkedSSL(this)" /><img src="../img/login/login_security.gif" style="cursor:pointer;" onmouseover="openLayer('ssltooltip','block')" onmouseout="openLayer('ssltooltip','none')" /></label>
			<div style="position:relative"><div id="ssltooltip" style="display:none;position:absolute;left:68px;top:-42px"><img src="../img/login/login_security_info.png"/></div></div>
		</div>
		<? } ?>
		<div style="margin:8px 0 0 60px;font:8pt Dotum;">
			<span style="cursor:pointer; color:#555555;" onmouseover="openLayer('logintooltip','block')" onmouseout="openLayer('logintooltip','none')">관리자 로그인 정보 찾기</span>
			<div style="position:relative"><div id="logintooltip" style="display:none;position:absolute;width:470px;left:140px;top:-42px;background-color:#ffffff;border:solid 1px #808080;padding:5px; color:#555555;">
				[ 관리자 로그인 정보 찾기 ]<br>
				<br>
				1. 쇼핑몰 신청후 관리자 로그인 정보를 변경한 적이 없는 경우<br>
				- 쇼핑몰 세팅 정보를 메일로 다시 받아 확인 할 수 있습니다.<br>
				- 확인방법 :<br>
				godo.co.kr > 고도회원 로그인 > 마이고도 > 상단의 쇼핑몰관리 > 본문에서<br>
				쇼핑몰 주소 오른쪽 [관리]버튼 클릭 > 쇼핑몰 기본정보의 [세팅메일 받기] 오른쪽의<br>
				[메일보내기] 버튼 클릭하여 세팅정보를 메일로 다시 받아 확인 할 수 있습니다.<br>
				<br>
				2. 관리자 로그인 정보를 변경한 적이 있는 경우<br>
				- 원하는 관리자 아이디와 비밀번호를 재설정 요청 할 수 있습니다.<br>
				- 요청방법 :<br>
				godo.co.kr > 고도회원 로그인 > [1:1문의] 버튼 클릭 > 문의 내용에 관리자 로그인 정보를<br>
				잊어버렸다고 원하는 관리자 아이디와 비밀번호를 기재하여 재설정 해달라고 남겨주시면<br>
				고도에서 재설정을 해드립니다.
			</div></div>
		</div>
	</div>
</div>
</form>
</body>
</html>