<?
$location = "ȸ������ > �Ŀ� ���Ϻ�����";
include "../_header.php";
include "../../lib/page.class.php";
?>
<script>
function sendAmail(fm)
{
	if (fm.type.value=="select" && !isChked(document.getElementsByName('chk[]'))) return false;
	if (fm.type.value=="query" && !fm.query.value){
		alert('�˻� ����� �����ϴ�.!');
		return false;
	}
	popup_return('','powerMail',900,700,50,10,1);
	fm.target = "powerMail";
	fm.action = "amail.php";
	fm.submit();
}
function checkSendAmail(fm)
{
	if(fm.receiveRefuseCount.value > 0){
		openLayerPopupReceiveRefuse('powermail');
	}
	else {
		sendAmail(fm);
	}
}
</script>
<?include "member_list.php";?>
<div align="right"><a href="javascript:popup('amail.php?charge=y',850,700);"><img src="../img/btn_point.gif"></a></div>
<div style='font:0;height:10'></div>
<div align=center>
<table bgcolor=F7F7F7 width=100%>
<tr>
	<td class=noline width=57% align=right>
	<select name=type onchange="javascript:getCountActReceiveRefuse('powermail');">
	<option value="select">������ ȸ���鿡��
	<option value="query">���� �˻�����Ʈ�� �ִ� ��� ȸ������
	</select>
	</td>
	<td width=43% style="padding-left:10px">
	<a href="javascript:void(0)" onClick="javascript:checkSendAmail(document.fmList);"><img src="../img/btn_mailpower.gif"></a>
	</td>
</tr>
</table>
</div><p>

</form>

<script>window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>