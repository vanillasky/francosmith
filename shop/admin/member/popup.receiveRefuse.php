<?php
include '../_header.popup.php';

if(!trim($_GET['mode'])){
	msg('정상적인 접속이 아닙니다.\n고객센터에 문의하여 주세요.', 'close');
}

$infoMessage = '';
$totalCount = $_GET['totalCount'];
$receiveRefuseCount = $_GET['receiveRefuseCount'];
$mode = $_GET['mode'];

switch($mode){
	case 'smsPopup': case 'smsBatch':
		$infoMessage = "SMS 발송대상 ".number_format($_GET['totalCount'])."명 중 수신거부회원이 <span style='font-weight: bold;'>".number_format($_GET['receiveRefuseCount'])."</span>명 포함되어 있습니다.<br />수신거부 회원을 제외하고 SMS를 발송하시겠습니까?";
	break;

	case 'powermail': case 'individualEmail':
		$infoMessage = "발송대상 ".number_format($_GET['totalCount'])."명 중 수신거부회원이 <span style='font-weight: bold;'>".number_format($_GET['receiveRefuseCount'])."</span>명 포함되어 있습니다.<br />수신거부 회원을 제외하고 메일을 발송하시겠습니까?";
	break;
}
?>
<style>
.msgInfo	{ text-align: center; padding: 40px 0px 0px 0px; font:13px Dotum; color:#666666; }
.buttonArea { text-align: center; padding-top: 20px; }
.submitBtn	{ cursor: pointer; border: none; }
</style>
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td class="msgInfo">
	<?php echo $infoMessage; ?><br />
	<span style="color: red;">수신거부한 회원에게 광고성 정보를 발송 시 과태료가 부과될 수 있습니다.</span>
	</td>
</tr>
<tr>
	<td class="buttonArea">
		<img src="../img/btn_excludeSend.png" onclick="javascript:smsSubmit('N');" class="submitBtn" alt="제외하고 발송" />&nbsp;
		<img src="../img/btn_includeSend.png" onclick="javascript:smsSubmit('Y');" class="submitBtn" alt="포함하고 발송" />&nbsp;
		<img src="../img/btn_sendCancel.png" onclick="javascript:parent.closeLayer();" class="submitBtn" alt="발송 취소" />
	</td>
</tr>
</table>

<script>
function smsSubmit(val)
{
	var mode = '<?php echo $mode; ?>';
	parent.document.getElementById('receiveRefuseType').value = val;

	switch(mode){
		case 'smsPopup':
			var form = parent.document.getElementById("popupSmsForm");
			parent.chkForm2(form);
			form.submit();
		break;

		case 'smsBatch':
			if(parent.chkFuncForm(parent.document.fmList) == true){
				parent.document.fmList.submit();
			}
		break;

		case 'powermail':
			parent.sendAmail(parent.document.fmList);
		break;

		case 'individualEmail':
			parent.sendMail(parent.document.fmList);
		break;

		default :
			alert("정상적인 호출이 아닙니다.\n고객센터에 문의하여 주세요.");
		break;
	}
	parent.closeLayer();
}
</script>
<?php include '../_footer.popup.php'; ?>