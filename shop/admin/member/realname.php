<?

$location = "회원관리 > 실명확인관리";
include "../_header.php";
include "../../conf/fieldset.php";

$checked[realname][useyn][$realname[useyn]] = "checked";
$checked[realname][minoryn][$realname[minoryn]] = "checked";

?>

<form name=frmField method=post action="indb.php">
<input type=hidden name=mode value=realname>

<div class="title title_top">실명확인관리<span>실명확인에 필요한 항목을 설정합니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=13')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b>※ 실명확인서비스에 대한 안내입니다. 꼭 읽어보세요.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>① 실명확인서비스를 제공하는 NICE신용평가정보의 계약 약정서를 작성합니다. <a href="http://www.godo.co.kr/service/nice_godo.zip"><font class=extext_l>[약정서 다운로드받기]</font></a></div>
<div style="padding-top:5"><font class=g9 color=666666>② 작성한 약정서와 제출서류(사업자등록증)를 NICE신용평가정보로 팩스(2122-4579)로 보내주세요.</div>
<div style="padding-top:5"><font class=g9 color=666666>③ NICE신용평가정보의 주소는 "서울시 영등포구 여의도동 14-33 NICE신용평가정보 CB사업본부 e-인프라사업실<br />　 김가별대리 앞" 입니다.</div>
<div style="padding-top:5"><font class=g9 color=666666>④ NICE신용평가정보의 담당자로부터 회원사 ID를 발급 받으시게 됩니다.</div>
<div style="padding-top:5"><font class=g9 color=666666>⑤ 발급 받으신 회원사 ID를 아래 기입란에 입력하고 등록버튼을 누릅니다.</div>
<div style="padding-top:5"><font class=g9 color=666666>⑥ 이제 쇼핑몰화면에서 회원가입 절차중에 실명확인 서비스가 정상적으로 동작되는지 확인하세요.</div></td></tr>
</table>


<div style="padding-top:10"></div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=28>
	<td>회원사 ID</td>
	<td><input type=text name="realname[id]" class=line value="<?=$realname[id]?>"> <font class=extext>NICE신용평가정보와 계약후 발급 받은 ID를 입력하세요</font></td>
</tr>
<tr height=28>
	<td>실명확인여부</td>
	<td class="noline">
	<input type="radio" name="realname[useyn]" value="y" <?=$checked[realname][useyn][y]?> onclick="setDisabled()"> 사용
	<input type="radio" name="realname[useyn]" value="n" <?=$checked[realname][useyn][n]?> onclick="setDisabled()"> 사용안함
	</td>
</tr>
<tr height=28>
	<td>성인인증여부</td>
	<td class="noline">
	<input type="radio" name="realname[minoryn]" value="y" <?=$checked[realname][minoryn][y]?>> 사용 <font class=extext>(19세 미만 회원가입 불가)</font>
	&nbsp;&nbsp;<input type="radio" name="realname[minoryn]" value="n" <?=$checked[realname][minoryn][n]?>> 사용안함 <font class=extext>(성인 여부에 관계없이 실명확인만 함)</font>
	</td>
</tr>
</table>

<table style="margin-top:20px;">
<tr>
	<td colspan="2" style="font-weight:bold;text-align:center;height:25px;">실명확인이 활용되는 페이지</td>
</tr>
<tr align="center">
	<td style="line-height:180%;">
	인트로 페이지에서 실명확인<br>
	<img src="../img/img_realname_intro.gif">
	</td>
	<td style="line-height:180%;">
	회원가입 페이지에서 실명확인<br>

	<img src="../img/img_realname_member.gif">
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">실명확인서비스(NICE신용평가정보)가 될 수 있도록
프로그램이 기본탑재 되어 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">실명확인서비스를 하기 위해서는 NICE신용평가정보와 계약만 진행하시면 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">실명확인서비스 제공업체: <a href="http://www.idcheck.co.kr/" target="_new"><font color="white">NICE신용평가정보 <font class="ver7">(http://www.idcheck.co.kr)</font></a>
</font></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td height=20></td></tr>

<tr><td><font class=def1 color=white>[필독] 실명확인서비스 절차</b></font></td></tr>
<tr><td><font class=def1 color=white>①</font> NICE신용평가정보의 계약 약정서를 작성하여 발송하세요.</td></tr>
<tr><td><font class=def1 color=white>②</font> NICE신용평가정보의 담당자로부터 회원사ID를 발급 받으시게 됩니다.</td></tr>
<tr><td><font class=def1 color=white>③</font> 발급 받으신 회원사ID를 본 페이지에 입력하세요.</td></tr>
<tr><td><font class=def1 color=white>④</font> 실명확인 사용 여부를 설정합니다.</td></tr>
<tr><td><font class=def1 color=white>⑤</font> 성인인증 여부를 선택합니다.</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;* 성인 인증여부는 회원가입 연령제한 설정입니다. </td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;* 성인 전용 사이트 방문시 성인임을 인증하는 기능과는 다른 기능임으로 착오 없으시길 바랍니다.</td></tr>
<tr><td><font class=def1 color=white>⑥</font> 쇼핑몰 회원가입 절차 중에 실명확인 서비스가 정상동작 하는지 확인하세요.</td></tr>

<tr><td height=8></td></tr>

<tr><td><font class=def1 color=white>[외부호스팅] 실명확인 서비스를 위한 환경설정 주의사항</b></font></td></tr>
<tr><td><font class=def1 color=white>①</font> PHP 4.30 이전 버전일 경우 iconv 함수가 기본적으로 제공되지 않습니다.<br>
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv 사이트를 참고 하시어 해당서버에 맞는 dll을 설치 하시기 바랍니다.</td></tr>
<tr><td><font class=def1 color=white>②</font> 개인실명확인 서비스는 설치하시는 서버에서 NICE신용평가정보 서버로 80포트를 이용한 통신이 이루어 집니다.<br>
&nbsp;&nbsp;&nbsp;방화벽에 secure.nuguya.com(반드시 DNS서비스여야 합니다.) 80 outer 포트가 열려 있는지 확인해 주시기 바랍니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>
function setDisabled(){
	var fm = document.frmField;
	fm['realname[minoryn]'][0].disabled = (fm['realname[useyn]'][0].checked ? false : true);
	fm['realname[minoryn]'][1].disabled = (fm['realname[useyn]'][0].checked ? false : true);
}
setDisabled()
</script>


<? include "../_footer.php"; ?>