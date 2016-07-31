<?

$location = '회원관리 > 아이핀관리';
include '../_header.php';
include '../../conf/fieldset.php';

$checked['ipin']['nice_useyn'][$ipin['nice_useyn']] = 'checked';
$checked['ipin']['nice_minoryn'][$ipin['nice_minoryn']] = 'checked';

?>

<form name="frmField" method="post" action="indb.php" onsubmit="return checkForm()">
<input type="hidden" name="mode" value="ipin">

<div class="title title_top">아이핀관리<span>아이핀 서비스에 필요한 정보를 설정합니다. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse" width="700">
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>※ 아이핀서비스에 대한 안내입니다. 꼭 읽어보세요.</b></div>
<div style="padding-top:10px; color:#666666;" class="g9">① 아이핀인증 서비스를 제공하는 NICE신용평가정보의 계약 약정서를 작성합니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">② 작성한 약정서와 제출서류(사업자등록증)를 NICE신용평가정보로 팩스(2122-4579)로 보내주세요.</div>
<div style="padding-top:3px; color:#666666;" class="g9">③ NICE신용평가정보의 주소는 "서울시 영등포구 여의도동 14-33 NICE신용평가정보 CB사업본부 e-인프라사업실<br />　 김가별대리 앞" 입니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">④ NICE신용평가정보의 담당자로부터 회원사Code, 회원사Password를 발급 받으시게 됩니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">⑤ 발급 받으신 회원사Code, 회원사Password를 아래 기입란에 입력하고 등록버튼을 누릅니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">⑥ 이제 쇼핑몰화면에서 회원가입 절차중에 아이핀인증 서비스가 정상적으로 동작되는지 확인하세요.</div>
</td></tr>
</table>


<div style="padding-top:10"></div>

<div class="extext">
이 페이지는 신규 아이핀서비스 등록 및 사용설정을 하는 페이지 입니다.<br />
(구)한국신용정보 아이핀서비스의 ID, SIkey, 키스트링을 보유하고 계신 회원님은 ‘아이핀서비스(구 한국신용정보)’ 설정페이지에서 관리 및 설정을 하여 주세요.<br />
<a href="../member/ipin.php" class="extext"><strong>[ 아이핀서비스(구 한국신용정보) 바로가기 ]</strong></a>
</div>

<div style="padding-top:10"></div>


<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="28">
	<td>회원사 Code</td>
	<td><input type="text" name="ipin[code]" id="code" class="line" value="<?=$ipin['code']?>"> <font class="extext">NICE신용평가정보와 계약 후 발급 받은 사이트 CODE를 입력하세요.</font></td>
</tr>
<tr height="28">
	<td>회원사 Password</td>
	<td><input type=text name="ipin[password]" id="password" class="line" value="<?=$ipin['password']?>"> <font class="extext">NICE신용평가정보와 계약 후 발급 받은 사이트 Password를 입력하세요.</font></td>
</tr>
<tr height="28">
	<td>아이핀사용여부</td>
	<td class="noline">
		<input type="radio" name="ipin[nice_useyn]" id="nice_usey" value="y" <?=$checked['ipin']['nice_useyn']['y']?> onclick="setDisabled()"> 사용
		<input type="radio" name="ipin[nice_useyn]" id="nice_usen" value="n" <?=$checked['ipin']['nice_useyn']['n']?> onclick="setDisabled()"> 사용안함
		<?=($ipin['useyn'] == 'y') ? " <font class=\"extext\" style=\"color:#FF0000\">'사용'으로 설정하시면 '구 한국신용정보'의 사용 설정은 '사용안함'으로 자동 설정 됩니다.</font>" : ""?>
	</td>
</tr>
<tr height="28">
	<td>성인인증여부</td>
	<td class="noline">
	<input type="radio" name="ipin[nice_minoryn]" value="y" <?=$checked['ipin']['nice_minoryn']['y']?>> 사용 <font class="extext">(19세 미만 회원가입 불가)</font>
	&nbsp;&nbsp;<input type="radio" name="ipin[nice_minoryn]" value="n" <?=$checked['ipin']['nice_minoryn']['n']?>> 사용안함
	</td>
</tr>
</table>

<div class="button"><input type="image" src="../img/btn_register.gif"> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a></div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스(NICE신용평가정보)가 될 수 있도록
프로그램이 기본탑재 되어 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스를 하기 위해서는 NICE신용평가정보와 계약만 진행하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스 제공업체: <a href="http://www.idcheck.co.kr/" target="_new"><font color="white">NICE신용평가정보 <font class="ver7">(http://www.idcheck.co.kr)</font></a>
</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white">[필독] 아이핀인증서비스 절차</b></font></td></tr>
<tr><td><font class="def1" color="white">①</font> NICE신용평가정보의 계약 약정서를 작성하여 발송하세요.</td></tr>
<tr><td><font class="def1" color="white">②</font> NICE신용평가정보의 담당자로부터 회원사Code, 회원사Password를 발급 받으시게 됩니다.</td></tr>
<tr><td><font class="def1" color="white">③</font> 발급 받으신 회원사Code, 회원사Password를 본 페이지에 입력하세요.</td></tr>
<tr><td><font class="def1" color="white">④</font> 쇼핑몰 회원가입절차중에 실명확인 서비스가 정상동작하는지 확인하세요.</td></tr>

<tr><td height="8"></td></tr>
<tr><td><font class="def1" color="white">[외부호스팅] 아이핀인증 서비스를 위한 환경설정 주의사항</b></font></td></tr>
<tr><td><font class="def1" color="white">①</font> PHP 4.30 이전 버전일 경우 iconv 함수가 기본적으로 제공되지 않습니다.<br />
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv 사이트를 참고 하시어 해당서버에 맞는 dll을 설치 하시기 바랍니다.</td></tr>
<tr><td><font class="def1" color="white">②</font> 아이핀인증 서비스는 설치하시는 서버에서 NICE신용평가정보 서버로 80포트를 이용한 통신이 이루어 집니다.<br /> &nbsp;&nbsp;&nbsp;방화벽에 secure.nuguya.com(반드시 DNS서비스여야 합니다.) 80 outer 포트가 열려 있는지 확인해 주시기 바랍니다.</td></tr>
</table>
</div>

<script>
	function setDisabled() {
		var fm = document.frmField;
		fm['ipin[nice_minoryn]'][0].disabled = (fm['ipin[nice_useyn]'][0].checked ? false : true);
		fm['ipin[nice_minoryn]'][1].disabled = (fm['ipin[nice_useyn]'][0].checked ? false : true);
	}

	function checkForm(f) {
		if($('nice_usey').checked) {
			if(!$('code').value || !$('password').value) {
				alert("회원사 Code와 회원사 Password를\n모두 입력하셔야 사용 가능한 서비스입니다.");
				return false;
			}
		}

		return true;
	}

	window.onload = function() {
		cssRound('MSG01');
		setDisabled();
	}
</script>

<? include "../_footer.php"; ?>