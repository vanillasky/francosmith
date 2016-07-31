<?php
include '../_header.popup.php';

if(!trim($_GET['mode'])){
	msg('�������� ������ �ƴմϴ�.\n�����Ϳ� �����Ͽ� �ּ���.', 'close');
}

$infoMessage = '';
$totalCount = $_GET['totalCount'];
$receiveRefuseCount = $_GET['receiveRefuseCount'];
$mode = $_GET['mode'];

switch($mode){
	case 'smsPopup': case 'smsBatch':
		$infoMessage = "SMS �߼۴�� ".number_format($_GET['totalCount'])."�� �� ���Űź�ȸ���� <span style='font-weight: bold;'>".number_format($_GET['receiveRefuseCount'])."</span>�� ���ԵǾ� �ֽ��ϴ�.<br />���Űź� ȸ���� �����ϰ� SMS�� �߼��Ͻðڽ��ϱ�?";
	break;

	case 'powermail': case 'individualEmail':
		$infoMessage = "�߼۴�� ".number_format($_GET['totalCount'])."�� �� ���Űź�ȸ���� <span style='font-weight: bold;'>".number_format($_GET['receiveRefuseCount'])."</span>�� ���ԵǾ� �ֽ��ϴ�.<br />���Űź� ȸ���� �����ϰ� ������ �߼��Ͻðڽ��ϱ�?";
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
	<span style="color: red;">���Űź��� ȸ������ ���� ������ �߼� �� ���·ᰡ �ΰ��� �� �ֽ��ϴ�.</span>
	</td>
</tr>
<tr>
	<td class="buttonArea">
		<img src="../img/btn_excludeSend.png" onclick="javascript:smsSubmit('N');" class="submitBtn" alt="�����ϰ� �߼�" />&nbsp;
		<img src="../img/btn_includeSend.png" onclick="javascript:smsSubmit('Y');" class="submitBtn" alt="�����ϰ� �߼�" />&nbsp;
		<img src="../img/btn_sendCancel.png" onclick="javascript:parent.closeLayer();" class="submitBtn" alt="�߼� ���" />
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
			alert("�������� ȣ���� �ƴմϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
		break;
	}
	parent.closeLayer();
}
</script>
<?php include '../_footer.popup.php'; ?>