<!--
	�����̼� ��ǰ�� �ֹ��� ����Ʈ (�ǹ���ǰ)
-->

<div class="title title_top">�ֹ����<span></div>

<form name="frmStatus" action="" method="post" target="ifrmHidden" onsubmit="return changeStatus()">
<input type="hidden" name="mode" value="status" />
<input type="hidden" name="goodsno" value="<?=$_GET['goodsno']?>" />
<?
if ($_GET[step]){
	foreach ($_GET[step] as $v) {
?>
<input type="hidden" name="step[]" value="<?=$v?>" />
<?
	}
}

if ($_GET[step2]) {
	foreach ($_GET[step2] as $v) {
?>
<input type="hidden" name="step2[]" value="<?=$v?>" />
<?
	}
}
?>
�Ʒ� �ֹ����� 
<select name="status">
	<option value="">======</option>
	<option value="1">�Ա�Ȯ��</option>
	<option value="2">����غ���</option>
	<option value="3">�����</option>
	<option value="4">��ۿϷ�</option>
</select>
���·� �����մϴ�. <input type="image" src="../img/btn_editsmstext.gif" />
</form>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=7></td></tr>
<tr class=rndbg>
	<th width=60>�ֹ��ڸ�</th>
	<th>�ֹ���ȣ</th>
	<th>�ֹ��Ͻ�</th>
	<th><?=($goods['goodstype'] == 'coupon') ? '������ȣ' : '�����ȣ' ?></th>
	<th>����</th>
	<th>�ֹ�����</th>
	<th>-</th>
</tr>
<tr><td class=rnd colspan=7></td></tr>
<?
while ($row = $db->fetch($rs)) {
	$step = getStepMsg($row[step],$row[step2],$row[ordno]);
?>
<tr height="25" align="center">
	<td><?=$row[nameOrder]?></td>
	<td><a href="javascript:fnViewOrder('<?=$row[ordno]?>');"><font class=ver81 color=0074BA><b><?=$row[ordno]?></b></font></a></td>
	<td><?=$row[orddt]?></td>

	<td class="<?=($row['deliverycode']) ? 'blue' : 'red'?>"><?=($row['deliverycode']) ? $row['deliverycode'] : '�̵��'?></td>
	<td><?=$row[ea]?></td>

	<td class=small4><?=$step?></td>

	<td class=small4>
	<? if ($goods['stats'] == 3) { ?>
		<? if ($row['step2'] < 40) { ?><img src="../img/today_btn_cn.gif" class="hand" onClick="nsTodayshopControl.order.cancel(<?=$row[ordno]?>);"><? } ?>
	<? } elseif ($row['step2'] < 40) { ?>
	<img src="../img/today_btn_no_in.gif" class="hand" onClick="<?if ($goods['stats'] == 4) {?>nsTodayshopControl.order.delivery(<?=$row[ordno]?>);<?} else {?>alert('�ǸŰ� �Ϸ�� ��ǰ�� �����Է��� �����մϴ�.');<?}?>">
	<? } ?>
	</td>

</tr>
<tr><td colspan="7" class="rndline"></td></tr>
<? } ?>
</table>
<div style="margin-bottom:10px;"></div>
<?
switch ((string)$goods['stats']) {
	case '1' :
		//echo '�ǸŴ��';
		break;
	case '3' :	// �ǸŽ���
?>
		<!--img src="../img/today_btn_cn_orderall.gif" onMouseOver='this.src="../img/today_btn_cn_orderallon.gif";' onMouseOut='this.src="../img/today_btn_cn_orderall.gif";' border=0 onClick="nsTodayshopControl.order.cancel_all('<?=$goods['goodsno']?>');" class="hand"-->
<?
		break;
	case '4' :	// �ǸſϷ�
	case '2' :	// �Ǹ���
		if ($goods['stats'] == '2' && $goods['processtype'] != 'i') break;	// �Ǹ��� ��ǰ������, ��ù߼��� �ƴҶ����� ����.
?>
		<img src="../img/today_list05.gif" onMouseOver='this.src="../img/today_list05on.gif";' onMouseOut='this.src="../img/today_list05.gif";' border=0 onClick="nsTodayshopControl.order.download();" class="hand">
		<a href="javascript:popupLayer('../data/popup.orderxls.php?mode=orderTodayGoodsXls',550,700)"><img src="../img/btn_order_data_order_ot.gif" border="0"></a>
		<form name="frmDnXls" method=post>
			<input type="hidden" name="mode" value="goods">
			<input type="hidden" name="goodsno" value="<?=$_GET['goodsno']?>">
			<input type="hidden" name="step" value="<?=$_GET['step']?>">
			<input type="hidden" name="step2" value="<?=$_GET['step2']?>">
		</form>
<?
		include "_deliveryForm.php"; //�����ϰ��Է���
		break;
}
?>