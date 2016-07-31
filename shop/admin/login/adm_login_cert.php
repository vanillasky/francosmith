<?
include "../../lib/library.php";

// 관리모드 내에서 관리자보안 인증 로그인상태 체크
$alCert = Core::loader('adminLoginCert');
if ($alCert->inStatus() == 'failure') {
	unset($ici_admin);
}

// 관리자 체크
if ($ici_admin) go("../index.php");

// OTP 수신처
$contacts = $alCert->getOtpContants('Y');

// 토큰 정의
$otp = Core::loader('gd_otp');
$_token = $otp->getToken();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="../common.js"></script>

<script language="JavaScript">
/* AJAX */
function gd_ajax(obj) {
	try {
		var ajax = new XMLHttpRequest();
	}
	catch (e) {
		var ajax = new ActiveXObject("Microsoft.XMLHTTP");
	}

	if (! obj.param ) obj.param = '';
	if (! obj.type ) obj.type = 'post';

	ajax.onreadystatechange = function() {
		// oncomplete
		if(ajax.readyState==4) {if(ajax.status==200){
			if (obj.success) {
				obj.success(ajax.responseText);
			}
		}}
	}

	obj.type = obj.type.toLowerCase();

	// send
	if (obj.type != 'post') {
		obj.url = obj.url + "?" + obj.param;
		obj.param = null;
	}

	ajax.open(obj.type, obj.url, true);
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.setRequestHeader("Connection", "Keep-Alive");
	ajax.send(obj.param);
}

var otpCert = function() {
	return {
		raiseError : function(code) {
			switch (code) {
				case '0001':
					alert('사용자 정보가 존재하지 않습니다.');
					break;
				case '0002':
					alert('잘못된 접근입니다. 다시 시도해 주세요.');
					break;
				case '0003':
					alert('인증번호 입력 시간이 초과되었습니다. 인증번호를 다시 요청해 주세요.');
					break;
				case '0004':
					alert('인증번호 사용이 만료되었습니다. 인증번호를 다시 요청해 주세요.');
					break;
				case '0005':
					alert('휴대폰으로 인증번호를 전송하지 못했습니다.\n전송이 불가능한 휴대폰 번호 입니다.');
					break;
				case '0006':
					alert('인증번호가 일치하지 않습니다.');
					break;
				case '0008':
					alert('휴대폰으로 인증번호를 전송하지 못했습니다.\nSMS 포인트가 부족합니다.');
					break;
				case '9999':
					alert('관리자보안 인증을 이용하고 있지 않습니다.');
					break;
				default:
					alert('기타 오류');
					break;
			}
			return false;
		},

		sendOTP : function(mobileAocSno,token) {
			gd_ajax({
				url : '../login/indb.adm_login_cert.php',
				type : 'POST',
				param : '&mode=sendLoginOtp&mobileAocSno='+mobileAocSno+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						alert('휴대폰으로 인증번호를 전송하였습니다. \n전송된 인증번호를 확인 후 입력해 주세요.');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		},

		compareOTP : function(otp,mobileAocSno,token) {
			gd_ajax({
				url : '../login/indb.adm_login_cert.php',
				type : 'POST',
				param : '&mode=compareLoginOtp&otp='+otp+'&mobileAocSno='+mobileAocSno+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						window.location.replace('../login/login.php');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		}
	}
}();

function callSendOtp() {
	var token = _ID('token').value;
	var mobileAocSno = _ID('mobileAocSno').value;

	otpCert.sendOTP(mobileAocSno, token);
}

function callCompareOtp(f) {
	var token = f.token.value;
	var mobileAocSno = f.mobileAocSno.value;
	var certKey = f.certKey.value;

	if (!certKey || certKey.length < 8) {
		alert('인증번호를 입력해 주세요.');
		f.certKey.focus();
		return false;
	}

	otpCert.compareOTP(certKey, mobileAocSno, token);
	return false;
}
</script>
</head>

<body style="margin:0; background-color:#ffffff;">
<form name="certForm" method="post" action="" onsubmit="return callCompareOtp(this)">
<input type="hidden" name="token" id="token" value="<?=$_token?>" />

<div style="text-align:center;">
	<div style="background:url(../img/login_cert/logo.gif) no-repeat 14px 9px; height:31px; width:1002px; margin:0 auto;"></div>

	<div style="background-color:#2fade7; height:100px;">
		<div style="background:url(../img/login_cert/tit_cp_security.png) no-repeat 14px 20px; height:78px; width:1002px; margin:0 auto;"></div>
	</div>

	<div style="width:670px; margin:0 auto;text-align:left;">
		<div style="background:url(../img/login_cert/txt_security_login.gif) no-repeat; margin-top:80px; height:44px;"></div>

		<div style="font:12px Dotum; color:#767676; line-height:22px; padding:15px 0;">
			1) 인증 휴대폰으로 등록한 휴대폰번호를 선택 후 <b>'인증번호 요청'</b>을 클릭합니다.<br/>
			2) 휴대폰으로 전송된 인증번호를 입력하고 <b>'확인'</b> 버튼을 클릭합니다.
		</div>

		<div style="border:solid 1px #e0e0e0; height:158px;">
		<table width="410" cellpadding="0" cellspacing="0" border="0" style="margin-top:40px; margin-left:130px;">
		<tr>
			<td width="83px"><img src="../img/login_cert/txt_cp.gif" alt="휴대폰번호"/></td>
			<td>
				<select name="mobileAocSno" id="mobileAocSno" style="width:218px;height:34px;font:14px dotum;color:#767676;border:solid 1px #e0e0e0;padding-left:3px;" label="휴대폰번호">
					<? foreach ($contacts as $data) echo '<option value="'.$data['aoc_sno'].'">'.$data['aoc_mobile'].'</option>'; ?>
				</select>
				<a href="javascript:void(0);" onClick="callSendOtp();"><img src="../img/login_cert/btn_code.gif" align="absmiddle" alt="인증번호 요청" /></a>
			</td>
		</tr>
		<tr><td height="11px" colspan="2"></td></tr>
		<tr>
			<td width="83px"><img src="../img/login_cert/txt_code.gif" alt="인증번호"/></td>
			<td><input type="text" name="certKey" id="certKey" maxlength="8" onkeydown="onlynumber()" style="width:218px;height:34px;font:14px dotum;color:#767676;border:solid 1px #e0e0e0;padding-left:3px;" required label="인증번호" /></td>
		</tr>
		</table>
		</div>

		<div style="font:12px Dotum; color:#9e9e9e; line-height:22px; padding:15px 0 25px;">
			※ 인증번호의 유효시간은 <b>3분</b>입니다.<br/>
			※ 인증번호 요청 시 <b>SMS 1포인트가 소진</b>됩니다.
		</div>

		<div style="text-align:center"><input type="image" src="../img/login_cert/btn_confirm.gif" border="0"></div>
	</div>
</div>
</form>
</body>
</html>