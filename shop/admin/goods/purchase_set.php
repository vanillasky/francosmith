<?
	$location = "����ó���� > ����ó ���� ��� ����";
	include "../_header.php";
	@include "../../conf/config.purchase.php";
?>

<div class="title title_top">����ó ���� ��� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=30')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<form method="post" action="./indb.purchase.php">
<input type="hidden" name="mode" value="pchs_set">
<table cellpadding="5" cellspacing="1" bgcolor="#E6E6E6" width="100%" border="0">
<colgroup>
	<col style="width:160px; color:#333333; background:#F6F6F6; font-weight:bold;"><col style="color:#000000; background:#FFFFFF;">
<colgroup>
<tr>
	<td>��ǰ ����ó ����</td>
	<td>
		<input type="radio" name="usePurchase" id="usePurchase1" style="border:0px;" value="Y"<?=($purchaseSet['usePurchase'] == "Y") ? " checked" : ""?> /> <label for="usePurchase1">���</label>
		<input type="radio" name="usePurchase" id="usePurchase2" style="border:0px;" value="N"<?=($purchaseSet['usePurchase'] != "Y") ? " checked" : ""?> /> <label for="usePurchase2">��� �� ��</label>
		&nbsp; &nbsp; <span class="small" style="color:#6D6D6D;">��ǰ�� ����ó ���� ��뿩�θ� �����մϴ�.</span>
	</td>
</tr>
</table>

<div style="height:20px;"></div>

<div class="title title_top">��ǰ ���� �˸� ��� ���� <span>��ǰ ���� �˸� ���� �� ����ó�� ������ ��ǰ���� ���� �˴ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=30')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>

<table cellpadding="5" cellspacing="1" bgcolor="#E6E6E6" width="100%" border="0">
<col style="width:160px; color:#333333; background:#F6F6F6; font-weight:bold;"><col style="color:#000000; background:#FFFFFF;">
<tr>
	<td>��ǰ ���� �˸� ��뼳��</td>
	<td>
		<input type="radio" name="soldoutAlarm" id="soldoutAlarm1" style="border:0px;" value="Y"<?=($purchaseSet['soldoutAlarm'] == "Y") ? " checked" : ""?> /> <label for="soldoutAlarm1">���</label>
		<input type="radio" name="soldoutAlarm" id="soldoutAlarm2" style="border:0px;" value="N"<?=($purchaseSet['soldoutAlarm'] != "Y") ? " checked" : ""?> /> <label for="soldoutAlarm2">��� �� ��</label>
		&nbsp; &nbsp; <span class="small" style="color:#6D6D6D;">�˶� ��뿩�θ� �����մϴ�.</span>
	</td>
</tr>
<tr>
	<td>�˾� �˸� ��� <input type="checkbox" name="popYn" id="popYn" style="border:0px;" value="1"<?=($purchaseSet['popYn'] == 1) ? " checked" : ""?> /></td>
	<td>
		������ �α��ν� ��� <input type="text" name="popStock" id="popStock" value="<?=$purchaseSet['popStock']?>" size="3" /> �� �̸��� ��� �˾�â�� ���ϴ�.
	</td>

</tr>
</table>

<div style="height:20px;"></div>

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td align="center"><input type="image" src="../img/btn_confirm.gif" class="null"></td>
</tr>
</table>

<div style="height:20px"></div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="5"></td></tr>
<tr>
	<td>����ó ���� ���� ��  [����/������/��� ����] , [���� ��� ����] �޴��� ��� �Է� �׸� ��� [���� �̷� ���] ����� ��� �˴ϴ�.</td>
</tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>
<? include "../_footer.php"; ?>