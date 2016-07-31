<?php
include "../_header.popup.php";

$ar_check_no = explode(',',$_GET['check_no']);
$attendance_no = (int)$_GET['attendance_no'];
$attd = Core::loader('attendance');

if(count($ar_check_no)==0) {
	exit;
}

$query = "
	select
		name
	from
		gd_attendance
	where
		attendance_no='{$attendance_no}'
";
$result = $db->_select($query);
$attd_info = $result[0];

?>
<script type="text/javascript">
function calSmsCnt() {
	var check_count = <?=count($ar_check_no)?>;
	var sms_point = <?=getSmsPoint()?>;
	var frmReserve = $('frmReserve');
	if(frmReserve.smsyn.checked) {
		if(check_count > sms_point) {
			alert('잔여 SMS포인트가 부족합니다');
			frmReserve.smsyn.checked=false;
			return false;
		}
	}
}

function chkSubmit() {
	
}
</script>

<div class="title title_top">적립금 수동 지급 - 출석체크 이벤트</div>

<form name="frmReserve" id="frmReserve" method="post" action="attendance.indb.php" target="ifrmHidden" onsubmit="return chkSubmit(this)">
<input type="hidden" name="mode" value="reserve">
<input type="hidden" name="check_no" value="<?=implode(',',$ar_check_no)?>">
<table>
<tR>
<td valign="top">
	<br><br><br>
	<table cellspacing="0" cellpadding="5" border="1" bordercolor="#cccccc" style="border-collapse:collapse" width="330">
	<tr>
	<td width="100" bgcolor="#eeeeee" nowrap align="right">출석체크명</td>
	<td><?=$attd_info['name']?></td>
	</tr>
	<tr>
	<td width="100" bgcolor="#eeeeee" nowrap align="right">대상</td>
	<td><?=count($ar_check_no)?>명</td>
	</tr>
	<tr>
	<td width="100" bgcolor="#eeeeee" nowrap align="right">적립금액</td>
	<td><input type="text" name="reserve" size="5">원</td>
	</tr>
	<tr>
	<td width="100" bgcolor="#eeeeee" nowrap align="right">적립사유</td>
	<td>출석체크 이벤트 혜택 지급</td>
	</tr>
	</table>
</td>
<td valign="top">
	<table border="0">
	<tr>
		<td>
		<div align=center><input type=checkbox name='smsyn' value='1' onclick='calSmsCnt();' class=null>SMS 동시 발송</div>
		<table width=146 cellpadding=0 cellspacing=0 border=0>
		<tr><td><img src="../img/sms_top.gif"></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align=center height="81"><textarea name=msg cols=16 rows=5 style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;" onkeydown="chkLength(this)" onkeyup="chkLength(this)" onchange="chkLength(this)" required msgR="메세지를 입력해주세요"></textarea></td>
		</tr>
		<tr><td height=31 background="../img/sms_bottom.gif" align=center><font class=ver8 color=262626><input name=vLength type=text style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value=0>/90 Bytes</td></tr>
		</table>

		</td>
	</tr>
	<tr>
		<td>

		<table>
		<tr>
			<td><font class=small1 color=262626>발신번호<td>
			<td>
				<input type=text name=callback size=12 readonly="readonly"><br>
				<a onclick="popup_return('../member/popup.callNumber.php?target=callback','callNumber',450,250,0,0,'yes');" class="hand"><img src="../img/call_number_btn.gif" align="absmiddle"></a>
			</td>
		</tr>
		<tr>
			<td><font class=small1 color=262626>남은건수<td>
			<td><span id=span_sms style="font-weight:bold"><?=number_format(getSmsPoint())?></span>건</td>
		</tr>
		<tr>
			<td colspan=4 height=28><img src="../img/arrow_blue.gif" align=absmiddle><a href="/shop/admin/member/sms.pay.php" target=_new><font class=small1 color=0074BA><u>SMS포인트 충전하기</u></a><td>
		</tr>
		</table>

		</td>
	</tr>
	</table>
</td>
</tr>
</table>
<div style="text-align:center">
<input type="image" src="../img/btn_confirm_s.gif" style="border:0px">
</div>
</form>


