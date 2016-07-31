<?
$SET_HTML_DEFINE = true;
include "../_header.popup.php";
include dirname(__FILE__)."/../../lib/callNumber.class.php";

$callNumber = new callNumber;
$callNumberData = $callNumber->getCallNumberData();
$callbackData = $callNumber->getCallNumberData('callback');
?>

<div>
	<div class="title title_top">SMS �߽Ź�ȣ ���</div>

	<div id="callNumberManage" class="right"><a href="https://www.godo.co.kr/mygodo/sms/intro.gd" target="_blank"><span class="blue">[�߽Ź�ȣ ����]</span></a></div>
	<table class="tb" width="100%">
	<col class="cellL" width="40"><col class="cellL"><col class="cellL"><col class="cellL" width="120">
	<tr>
		<th style="background:#f6f6f6;">����</th>
		<th style="background:#f6f6f6;">�߽Ź�ȣ</th>
		<th style="background:#f6f6f6;">�����޸�</th>
		<th style="background:#f6f6f6;">������</th>
	</tr>
	<? if (is_array($callNumberData) && count($callNumberData)>0) foreach($callNumberData as $data){ ?>
	<tr>
		<td align="center"><input type="radio" name="chkCallNumber" id="chk-<?echo $data['callback']?>" value="<?echo $data['callback']?>"></td>
		<td align="center" class="blue"><label for="chk-<?echo $data['callback']?>"><?echo $data['callback']?></label></td>
		<td align="center"><?echo $data['title']?></td>
		<td align="center"><?echo $data['apvdtime']?></td>
	</tr>
	<? } else { ?>
	<tr>
		<td colspan="4" align="center"><span class="red">* ������Ż���� ��84���� �ǰ� 2015�� 10�� 16�Ϻ���<br>������ ��ϵ� �߽Ź�ȣ�θ� ���� ������ �����մϴ�.</span><br>
		<a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1247" target="_blank">[�ڼ��� ����]</a> <a href="https://www.godo.co.kr/mygodo/sms/intro.gd" target="_blank">[�߽Ź�ȣ ����ϱ�]</a></td>
	</tr>
	<? } ?>
	</table>

	<p align="center"><img src="../img/btn_confirm_o.gif" onclick="selectCallNumber()" style="cursor:pointer;"></p>
</div>

<script type="text/javascript">
var chkCallNumber = document.getElementsByName('chkCallNumber');
var changeColor = "<?echo $_GET['changeColor']?>";
var target = "<?echo $_GET['target'] ? $_GET['target'] : 'callback'?>";
var openerTarget = opener.document.getElementsByName(target);

if (typeof(chkCallNumber[0]) == 'undefined') document.getElementById('callNumberManage').style.display = "none";
if (typeof(chkCallNumber[0]) == 'object' && typeof(openerTarget[0]) == 'object') {
	for (var i = 0; i < chkCallNumber.length; i++){
		if (chkCallNumber[i].value == openerTarget[0].value){
			chkCallNumber[i].checked = true;
			break;
		}
	}
}

function selectCallNumber(){
	if (typeof(chkCallNumber[0]) == 'undefined') {
		window.close();
	} else if (typeof(chkCallNumber[0]) == 'object') {
		for (var i = 0; i < chkCallNumber.length; i++){
			if (chkCallNumber[i].checked == true) break;
		}

		if (i >= chkCallNumber.length) {
			alert('���õ� �߽Ź�ȣ�� �����ϴ�');
		} else if (!chkCallNumber[i].value) {
			alert('���õ� �߽Ź�ȣ�� �����ϴ�');
		} else {
			if (typeof(openerTarget) != 'undefined') {
				openerTarget[0].value = chkCallNumber[i].value;
				if (changeColor == 'Y' && opener) opener.smsRecallColor('<?echo $_GET[target]?>', chkCallNumber[i].value,'<?echo @implode($callbackData, ",")?>');
			}
			window.close();
		}
	}
}
</script>