<?

$location = "회원관리 > 아이핀서비스(구 한국신용정보)관리";
include "../_header.php";
include "../../conf/fieldset.php";

$checked[ipin][useyn][$ipin[useyn]] = "checked";
$checked[ipin][minoryn][$ipin[minoryn]] = "checked";

?>

<form name=frmField method=post action="indb.php" onsubmit="return checkForm()">
<input type=hidden name=mode value=ipin>

<div class="title title_top">아이핀서비스(구 한국신용정보)관리<span>아이핀 서비스에 필요한 정보를 설정합니다. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>* 본 페이지는 기존 한국신용정보를 통해 아이핀서비스를 제공 받고 있는 회원사 전용 관리자 페이지입니다.</b></div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>질문1.</b> NICE신용평가정보는 어떤 회사인가요?<br />국내 최대 신용정보 회사인 한국신용정보와 한국신용평가사의 합병으로<br />현재는 NICE신용평가정보로 상호명이 변경 되었습니다.</div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>질문2.</b> 서비스의 달라진 부분이 있나요?<br />서비스 및 제공방식의 달라진 점은 없습니다.<br />다만, 아이핀서비스의 업데이트로 인해 신규모듈을 적용하여 구분을 두었습니다.</div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>질문3.</b> 아이핀서비스(구 한국신용정보) 관리자페이지의 용도는 무엇인가요?<br />본 페이지는 기존 회원사의 아이핀서비스 추가등록 및 사용여부를 수정하실 수 있습니다.</div>
<div style="padding-top:10px; color:#666666;" class="g9">기타 궁금한 사항은 NICE신용평가정보 e-인프라사업실 김가별담당자에게 언제든지 연락주세요.<br />(전화번호 : 02-2122-4548 , 이메일 : gastar1@nice.co.kr)</div>
</td></tr>
</table>


<div style="padding-top:10"></div>

<div class="extext">
이 페이지는 (구)한국신용정보의 아이핀서비스를 등록 및 사용설정을 하는 페이지 입니다.<br />
신규 아이핀서비스 등록 및 사용설정을 하실 회원님은 ‘아이핀관리’ 설정페이지에서 관리 및 설정을 하여 주세요. <a href="../member/ipin_new.php" class="extext"><strong>[ 아이핀관리 바로가기 ]</strong></a>
</div>

<div style="padding-top:10"></div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=28>
	<td>회원사 ID</td>
	<td><input type=text name="ipin[id]" class=line value="<?=$ipin[id]?>"> <font class=extext>NICE신용평가정보와 계약후 발급 받은 ID를 입력하세요</font></td>
</tr>
<tr height=28>
	<td>사이트식별정보 SIKey</td>
	<td><input type=text name="ipin[SIKey]" class=line value="<?=$ipin[SIKey]?>" > <font class=extext>NICE신용평가정보와 계약후 발급 받은 사이트식별정보 SIKey값를 입력하세요</font></td>
</tr>
<tr height=28>
	<td>키스트링 80자리</td>
	<td><input type=text name="ipin[athKeyStr]" class=lline value="<?=$ipin[athKeyStr]?>" style="width:600px;"> <br><font class=extext>NICE신용평가정보와 계약후 발급 받은 키스트링 80자리를 입력하세요</font></td>
</tr>
<tr height=28>
	<td>아이핀사용여부</td>
	<td class="noline">
	<input type="radio" name="ipin[useyn]" value="y" <?=$checked[ipin][useyn][y]?> onclick="setDisabled()"> 사용
	<input type="radio" name="ipin[useyn]" value="n" <?=$checked[ipin][useyn][n]?> onclick="setDisabled()"> 사용안함
	<?=($ipin['nice_useyn'] == 'y') ? " <font class=\"extext\" style=\"color:#FF0000\">'사용'으로 설정하시면 '구 한국신용정보'의 사용 설정은 '사용안함'으로 자동 설정 됩니다.</font>" : ""?>
	</td>
</tr>
<tr height=28>
	<td>성인인증여부</td>
	<td class="noline">
	<input type="radio" name="ipin[minoryn]" value="y" <?=$checked[ipin][minoryn][y]?>> 사용 <font class=extext>(19세 미만 회원가입 불가)</font>
	&nbsp;&nbsp;<input type="radio" name="ipin[minoryn]" value="n" <?=$checked[ipin][minoryn][n]?>> 사용안함
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스(NICE신용평가정보)가 될 수 있도록
프로그램이 기본탑재 되어 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스를 하기 위해서는 NICE신용평가정보와 계약만 진행하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">아이핀인증서비스 제공업체: <a href="https://www.nuguya.com/" target=_new><font color=white>NICE신용평가정보 <font class=ver7>(https://www.nuguya.com)</font></a>
</font></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td height=20></td></tr>

<tr><td><font class=def1 color=white>[필독] 아이핀인증서비스 절차</b></font></td></tr>
<tr><td><font class=def1 color=white>①</font> NICE신용평가정보의 계약 약정서를 작성하여 발송하세요.</td></tr>
<tr><td><font class=def1 color=white>②</font> NICE신용평가정보의 담당자로부터 회원사ID를 발급 받으시게 됩니다.</td></tr>
<tr><td><font class=def1 color=white>③</font> 발급 받으신 회원사ID,사이트식별정보, 키스트링를 본 페이지에 입력하세요.</td></tr>
<tr><td><font class=def1 color=white>④</font> 쇼핑몰 회원가입절차중에 실명확인 서비스가 정상동작하는지 확인하세요.</td></tr>

<tr><td height=8></td></tr>

<tr><td><font class=def1 color=white>[외부호스팅] 아이핀인증 서비스를 위한 환경설정 주의사항</b></font></td></tr>
<tr><td><font class=def1 color=white>①</font> PHP 4.30 이전 버전일 경우 iconv 함수가 기본적으로 제공되지 않습니다.<br>
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv 사이트를 참고 하시어 해당서버에 맞는 dll을 설치 하시기 바랍니다.</td></tr>
<tr><td><font class=def1 color=white>②</font> 아이핀인증 서비스는 설치하시는 서버에서 NICE신용평가정보 서버로 80포트를 이용한 통신이 이루어 집니다.<br>
&nbsp;&nbsp;&nbsp;방화벽에 secure.nuguya.com(반드시 DNS서비스여야 합니다.) 80 outer 포트가 열려 있는지 확인해 주시기 바랍니다.</td></tr>
</table>
</div>
<script>
function setDisabled(){
	var fm = document.frmField;
	fm['ipin[minoryn]'][0].disabled = (fm['ipin[useyn]'][0].checked ? false : true);
	fm['ipin[minoryn]'][1].disabled = (fm['ipin[useyn]'][0].checked ? false : true);
}

function checkForm() {
	var ipin_id = document.getElementsByName('ipin[id]'); // 회원사 ID
	var ipin_SIKey = document.getElementsByName('ipin[SIKey]'); // 사이트식별정보 SIKey
	var ipin_athKeyStr = document.getElementsByName('ipin[athKeyStr]'); // 키스트링 80자리
	var ipin_useyn = document.getElementsByName('ipin[useyn]'); // 아이핀사용여부

	if(ipin_useyn[0].checked) {
		if(!ipin_id[0].value || !ipin_SIKey[0].value || !ipin_athKeyStr[0].value) {
			alert("회원사 ID, 사이트식별정보 SIKey, 키스트링을\n모두 입력하셔야 사용 가능한 서비스입니다.");
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