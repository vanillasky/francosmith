<?

include "../_header.popup.php";

$type = ( $_POST[type] ) ? $_POST[type] : $_GET['type'];

@include "../../conf/mail.cfg.php";

// 수신동의
$mailBaseAgree = array(
	'agreeFlag' => 'Y',
	'agreeMsg' => '본 메일은 YYYY년 MM월 DD일 기준, 회원님의 수신동의를 확인한 결과 회원님께서 수신동의를 하셨기에 발송되었습니다.'
);
if ($mailCfg['agreeFlag'] == '' && $mailCfg['agreeMsg'] == '') {
	$mailCfg['agreeFlag'] = $mailBaseAgree['agreeFlag'];
	$mailCfg['agreeMsg'] = $mailBaseAgree['agreeMsg'];
}
$checked['agreeFlag'][$mailCfg['agreeFlag']] = "checked";
$mailCfg['agreeMsg'] = stripslashes($mailCfg['agreeMsg']);

$where = '';

// 발송 대상
if ($type == 'select') { // 선택한 회원에게 발송(개별/전체 메일보내기)
	if($_POST['receiveRefuseType'] == 'N'){
		$where = " AND mailling = 'y' ";
	}
	$query = "select * from ".GD_MEMBER." where m_no in (".(is_array($_POST[chk]) ? implode(",",$_POST[chk]) : "''").")" . $where;
}
else if ($type == 'query') { // 검색리스트에 있는 모든 회원에게 발송(개별/전체 메일보내기)
	$query = stripslashes($_POST[query]);
	if($_POST['receiveRefuseType'] == 'N'){
		$where = " mailling = 'y' ";
		$query = preg_replace('/(order)(\s|\w|\D|\d)+/i', '', $query);
		if(preg_match('/where/i', $query)){
			$whereType = ' and ';
		}
		else {
			$whereType = ' WHERE ';
		}
		$query = $query . $whereType . $where;
	}
}
else if ($type == 'direct') { // 특정인에게 발송
	if ($_GET['m_id'] != '') { // 특정회원
		$query = "select * from ".GD_MEMBER." where m_id='$_GET[m_id]'";
	}
	else if ($_GET['email'] != '') { // 특정메일
		$toEmail = $_GET['email'];
		$total = 1;
	}
	else { // 직접입력
		$toEmail = '';
		$total = 1;
	}
}

if ($query){
	$s = strpos($query,"from");
	$e = strpos($query,"order by");
	if (!$e) $e = strlen($query);

	// 수신거부 대상자 인원 체크
	$denyNum = 0;
	list ($denyNum) = $db->fetch("select count(*) ".substr($query,$s,$e-$s)." and mailling = 'n'");

	// 수신대상 총인원
	$total = 0;
	list ($total) = $db->fetch("select count(*) ".substr($query,$s,$e-$s));
}

?>
<? if ($_GET[m_id] == '' && $_GET[email] == ''){ ?>
<body style="margin:0" scroll=no>
<? } ?>

<!-------------- 현재 설정 내용 저장 시작 ---------------------->
<form name="sForm" method="post" action="indb.php" target=ifrmHidden>
<input type="hidden" name="mode" value="setEmailAgree"/>
<input type="hidden" name="set[agreeFlag]"/>
<input type="hidden" name="set[agreeMsg]"/>
</form>
<script type="text/javascript">
function saveAgree() {
	var sFobj = document.sForm;
	var agreeFlag = document.getElementsByName('agreeFlag');
	var agreeMsg = document.getElementsByName('agreeMsg');
	sFobj['set[agreeFlag]'].value = (agreeFlag[0].checked ? 'Y' : 'N');
	sFobj['set[agreeMsg]'].value = agreeMsg[0].value;
	sFobj.submit();
}
</script>
<!-------------- 현재 설정 내용 저장 끝 ------------------------>

<!-------------- 기본값복원 시작 ---------------------->
<form>
<input type="hidden" name="base[agreeFlag]" value="<?=htmlspecialchars($mailBaseAgree['agreeFlag'])?>"/>
<input type="hidden" name="base[agreeMsg]" value="<?=htmlspecialchars($mailBaseAgree['agreeMsg'])?>"/>
</form>
<script type="text/javascript">
function putBaseAgree() {
	var agreeFlag = document.getElementsByName('agreeFlag');
	var agreeMsg = document.getElementsByName('agreeMsg');
	var baseAgreeFlag = document.getElementsByName('base[agreeFlag]');
	var baseAgreeMsg = document.getElementsByName('base[agreeMsg]');
	agreeFlag[0].checked = (baseAgreeFlag[0].value == 'Y' ? true : false);
	agreeMsg[0].value = baseAgreeMsg[0].value;
}
</script>
<!-------------- 기본값복원 끝 ------------------------>

<!-------------- 폼체크 시작 ---------------------->
<script type="text/javascript">
function chkForm2(obj)
{
	if (!chkForm(obj)) return false;

	if(typeof(obj.agreeFlag) != 'undefined' && obj.agreeFlag.checked == true) {
		var baseAgreeMsg = document.getElementsByName('base[agreeMsg]');
		if( obj.agreeMsg.value == baseAgreeMsg[0].value) {
			alert("날짜형식이 맞지 않습니다. 다시 입력해주세요. \n예) 2013년 11월 18일");
			obj.agreeMsg.focus();
			return false;
		}
	}

	return true;
}
</script>
<!-------------- 폼체크 끝 ------------------------>

<form method=post action="iframe.email.php" onsubmit="return chkForm2(this)" target=boxEmail>
<input type=hidden name=mode value="sendmail">
<input type=hidden name=query value="<?=$query?>">
<input type=hidden name=total value="<?=$total?>">

<div class="title title_top">메일보내기<span>회원들에게 메일을 전송합니다</span></div>

<table width=100%>
<col class=cellC><col class=cellL>
<tr>
	<td>전송완료 리스트<br><font class=small color=6d6d6d>메일전송이 시작되면<br>이곳에 메일리스트가<br>보여집니다</font></td>
	<td>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr>
		<td style="padding-right:3px"><iframe name=boxEmail style="width:100%;height:100px" style="border:1 solid #cccccc" frameborder=0></iframe></td>
		<td width=100><input type=submit style="width:100%;height:104px;background:#4A3F38;color:#ffffff" value="메일발송하기"></td>
	</tr>
	<tr>
		<td style="padding-top:3px" colspan=2>
		<div style="height:8px;font:0;background:#f7f7f7;border:1 solid #cccccc">
		<div id=progressBar style="height:8px;background:#ff0000;width:0"></div>
		</div>
		</td>
	</tr>
	</table>

	</td>
</tr>
<? if ($type == 'direct' && $query == ''){ ?>
<tr>
	<td>받는분 이메일</td>
	<td><input type=text name=toEmail value="<?=$toEmail?>" class=lline required></td>
</tr>
<? } else { ?>
<tr>
	<td>수신대상</td>
	<td><b><?=number_format($total)?>명</b> <? if ($denyNum) echo '<span style="color:#FF0066"><b>(※수신거부 대상자가 포함되어 있습니다.)</b></span>'; ?></td>
</tr>
<? } ?>
<tr>
	<td>제목</td>
	<td><input type=text name=subject style="width:100%" required class=lline></td>
</tr>
<tr>
	<td>내용</td>
	<td>
	<textarea name=body style="width:100%;height:480px" type=editor class=tline><?=$tmp?></textarea>
	</td>
</tr>
</table>

<? if ($query != ''){ ?>
<table width="100%">
<col class=cellC><col class=cellL><col width="120">
<tr>
	<td class="noline">수신동의문구 <input type="checkbox" name="agreeFlag" value="Y" <?=$checked['agreeFlag']['Y']?> /></td>
	<td><textarea name="agreeMsg" style="width:100%; height: 60px;" class=lline><?=$mailCfg['agreeMsg']?></textarea></td>
	<td>
		<div style="line-height:16px;">
		<a href="javascript:saveAgree();" class="extext" style="font-weight:bold;">[현재 설정 내용 저장]</a><br/>
		<a href="javascript:putBaseAgree();" class="extext" style="font-weight:bold;">[기본값복원]</a>
		</div>
	</td>
</tr>
<tr>
	<td class="noline">수신거부 <input type="checkbox" name="denyFlag" value="Y" checked /></td>
	<td>
		<div style="padding:5px; background-color:rgb(250, 250, 250); border:solid 1px #cccccc;">
			※ [메일내용]에 자체 수신거부기능을 넣으신 경우, 체크해제를 하면 됩니다.<br/><br/>
			- 이메일의 수신을 더 이상 원하지 않으시면 [수신거부]를 클릭해 주세요.<br/>
			- If you don’t want to receive this mail, click here.
		</div>
	</td>
	<td></td>
</tr>
</table>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse" width="100%">
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>※ 광고성 메일 수신거부 간소화 정책으로 인한 메일 설정 안내 <a href="javascript:popupLayer('../member/popup.email_deny_law.php',550,230);" class="extext"><b>[법적고지확인]</b></a></b></div>
<div style="padding-top:10px; color:#666666;" class="g9">주요 공지사항을 제외한 상품의 홈보나 뉴스레터, 이벤트 등 광고성 메일을 발송하게 될 경우
	<b><u>메일 수신에 동의한 회원에 대해서만 발송</u></b>해야 하며, 이 때 메일을 받은 회원에게 <b><u>‘수신동의’에 대한 문구와 손쉽게 ‘수신거부’를 할 수 있도록 기능을 제공</u></b>해야 합니다.
</div>
<div style="padding-top:10px; color:#666666;" class="g9">- <b>수신동의문구</b> : 수신동의를 한 시기와 내용에 대해 구체적으로 명시하여야 합니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">- <b>수신거부기능</b> : 수신거부 기능을 자체적으로 구축하지 못한 경우에 사용할 수 있습니다.</div>
<div style="padding-top:3px; color:#666666;" class="g9">- <b>수신거부완료 안내 메일 발송</b> : 메일을 수신한 회원이 ‘수신거부’를 할 경우, 수신거부 처리내용에 대한 안내메일을 발송해야 합니다.
	이에 대한 설정을 <a href="../member/email.cfg.php?mode=30" class="extext" target="_blank"><b>[자동메일설정 > 수신거부완료 안내메일]</b></a>에서 하실 수 있습니다.
</div>
</td></tr>
</table>
<? } ?>

</form>

<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor('../../lib/meditor/');</script>