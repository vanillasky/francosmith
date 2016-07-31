<?
include '../_header.popup.php';

### 그룹명 가져오기
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$res = $db->query("select m_no, m_id, name, level from ".GD_MEMBER." where level >= 80 order by regdt desc");

// 토큰 정의
$otp = Core::loader('gd_otp');
$_token = $otp->getToken();

?>
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

		sendOTP : function(mobile,token) {
			gd_ajax({
				url : '../basic/indb.login_cert.php',
				type : 'POST',
				param : '&mode=sendRegitOtp&mobile='+mobile+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						_ID('mobileCompared').value = '';
						_ID('mobileClone').value = _ID('mobile').value;
						alert('휴대폰으로 인증번호를 전송하였습니다. \n전송된 인증번호를 확인 후 입력해 주세요.');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		},

		compareOTP : function(otp,token) {
			gd_ajax({
				url : '../basic/indb.login_cert.php',
				type : 'POST',
				param : '&mode=compareRegitOtp&otp='+otp+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						_ID('mobileCompared').value = 'Y';
						alert('휴대폰번호 인증이 완료 되었습니다.');
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
	var mobile = _ID('mobile').value;

	if (!mobile) {
		alert('휴대폰번호를 입력해 주세요.');
		_ID('mobile').focus();
		return;
	}

	otpCert.sendOTP(mobile, token);
}

function callCompareOtp() {
	var token = _ID('token').value;
	var certKey = _ID('certKey').value;
	var mobile = _ID('mobile').value;
	var mobileClone = _ID('mobileClone').value;

	if (!certKey || certKey.length < 8) {
		alert('인증번호를 입력해 주세요.');
		_ID('certKey').focus();
		return;
	}

	if (mobile != mobileClone) {
		alert('인증번호 요청한 핸드폰번호가 다릅니다.\n인증번호를 다시 요청해 주세요.');
		_ID('mobile').focus();
		return;
	}

	otpCert.compareOTP(certKey, token);
}

/*** 폼체크 ***/
function chkForm2(obj)
{
	if (obj.mobileCompared.value != 'Y') {
		alert('휴대폰인증이 완료되지 않았습니다.\n인증을 먼저 진행해 주세요.');
		obj.certKey.focus();
		return false;
	}

	if (obj.mobile.value != obj.mobileClone.value) {
		alert('인증번호 요청한 핸드폰번호가 다릅니다.\n인증번호를 다시 요청해 주세요.');
		obj.mobile.focus();
		return false;
	}

	var isChked = false;
	var El = document.getElementsByName('mno');
	if (El) for (i=0;i<El.length;i++) if (El[i].checked) isChked = true;
	if (isChked != true) {
		alert ('관리자ID를 선택해 주세요.');
		return false;
	}

	if (!chkForm(obj)) return false;

	return true;
}
</script>

<form action="indb.login_cert.php" method=post onsubmit="return chkForm2(this)">
<input type="hidden" name="mode" value="regitContact" />
<input type="hidden" name="token" id="token" value="<?=$_token?>" />
<input type="hidden" name="mobileClone" id="mobileClone" value="" />
<input type="hidden" name="mobileCompared" id="mobileCompared" value="" />

<!-- 휴대폰번호 인증 : Start -->
<div class="title title_top">휴대폰번호 추가<span>인증 휴대폰으로 등록할 휴대폰번호를 입력 후 인증해주세요.</span></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>휴대폰번호</td>
	<td>
		<input type="text" name="mobile" id="mobile" value="" required fld_esssential class="line" label="휴대폰번호">
		<a href="javascript:void(0);" onClick="callSendOtp();"><img src="../img/login_cert/btn_get_authnum.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>인증번호</td>
	<td>
		<input type="text" name="certKey" id="certKey" maxlength="8" onkeydown="onlynumber()" value="" class="line" label="인증번호">
		<a href="javascript:void(0);" onClick="callCompareOtp();"><img src="../img/login_cert/btn_form_confirm.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>

<div style="margin-top:5px"><span class="small"><font class="extext">※ 인증번호의 유효시간은 <span class="red"><b>3분</b></span>이며, 인증번호를 입력 후 반드시 <b>'확인'</b> 버튼을 클릭하셔야 합니다.</font></span></div>
<!-- 휴대폰번호 인증 : End -->

<!-- 관리자ID 매칭 : Start -->
<div style="padding-top:20px"></div>
<div class="title title_top">관리자ID 매칭<span>등록한 휴대폰과 매칭할 관리자ID를 선택해주세요..</span></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="3"></td></tr>
<tr class="rndbg">
	<th>선택</th>
	<th>관리자ID</th>
	<th>이름(그룹)</th>
</tr>
<tr><td class="rnd" colspan="3"></td></tr>
<col width="30" align="center">
<? while ($data=$db->fetch($res)){ ?>
<tr height="30" align="center">
	<td class="noline"><input type="radio" name="mno" value="<?=$data['m_no']?>"></td>
	<td><?=$data['m_id']?></td>
	<td><?=$data['name']?>(<?=$r_grp[$data['level']]?>)</td>
</tr>
<tr><td colspan="3" class="rndline"></td></tr>
<? } ?>
</table>

<div style="margin-top:5px"><span class="small"><font class="extext">※ <a href="../basic/adminGroup.php" target="_top" class="extext_l">'관리자권한 설정'</a> 메뉴의 <b>'관리자권한그룹'</b>으로 등록된 관리자ID만 매칭하실 수 있습니다.</font></span></div>
<!-- 관리자ID 매칭 : End -->

<div style="padding:15px 0 0 0px" align=center><input type=image src="../img/btn_regist.gif" class=null></div>

</form>

<script>table_design_load();</script>