<?
// ����Ʈ ������ �� �ٸ��Ƿ�, �迭�� ���� �� ��, ����Ѵ�.
$arRows = array();

while ($row = $db->fetch($res,1)) {
	$arRows[$row['ordno']][] = $row;
}

?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<col align="center" width="40"/>
<col align="center" width="50" />
<col align="center" width="110" />
<col align="center" width="130" />
<col align="left" />
<col align="center" width="90" />
<col align="center" width="80" />
<tr><td class="rnd" colspan="7"></td></tr>
<tr class="rndbg">
	<th><span onclick="chkBoxAll()" style="cursor:pointer">����</span></th>
	<th>��ȣ</th>
	<th>�ֹ��Ͻ�</th>
	<th>�ֹ���ȣ</th>
	<th>�ֹ���ǰ</th>
	<th>�ֹ���</th>
	<th>ó������</th>
</tr>
<tr><td class="rnd" colspan="7"></td></tr>
</table>

<table width="100%" cellpadding="0" cellspacing="0" border="1" bordercolor="#E4E4E4" style="border-collapse:collapse;">
<col align="center" width="40"/>
<col align="center" width="50" />
<col align="center" width="110" />
<col align="center" width="130" />
<col align="left" />
<col align="center" width="90" />
<col align="center" width="80" />
<?
foreach ($arRows as $rows) {

	$rowspan = sizeof($rows);
	$rowskip = 0;

	foreach($rows as $row) {

		$row['orddt'] = substr($row['orddt'], 0, -3);

?>
<tr height="25" bgcolor="#ffffff" bg=""#ffffff" align=center>
	<td class="noline"><input type="checkbox" name="target[ordno][]" value="<?=$row['ordno'].'_'.$row['sno']?>" class="chk_ordno"  onclick="iciSelect(this)"></td>

	<? if ($rowskip == 0) { ?>
	<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<? } ?>

	<? if ($rowskip == 0) { ?>
	<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>><font class="ver81" color="#616161"><?=$row['orddt']?></font></td>
	<? } ?>

	<? if ($rowskip == 0) { ?>
	<td <?=$rowspan > 1 ? 'rowspan="'.$rowspan.'"' : ''?>>
		<a href="view.php?ordno=<?=$row['ordno']?>"><font class=ver81 color=<?=$row['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$row['ordno']?><?=$row['flg_inflow'] == 'sugi' ? '<span class="small1">(����)</span>' : ''?></b></font></a>
		<a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<? } ?>

	<td>
		<div style="padding-left:3px;">
			<? if (!empty($row[old_ordno])){	?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_twice_order.gif"></a><? } ?>
			<? if ($row['flg_escrow']=="y"){	?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/btn_escrow.gif"></a><? } ?>
			<? if ($row['flg_egg']=="y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_guar_order.gif"></a><? } ?>
			<? if (!empty($row['flg_cashreceipt'])){	?><img src="../img/icon_cash_receipt.gif"><? } ?>
			<? if ($row['flg_cashbag']=="Y"){		?><a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/icon_okcashbag.gif" align=absmiddle></a><? } ?>
			<font class=small1 color=444444><?=$row['goodsnm']?></font>
		</div>
	</td>

	<td><? if ($row['m_id']) { ?><span id="navig" name="navig" m_id="<?=$row['m_id']?>" m_no="<?=$row['m_no']?>"><? } ?><font class=small1 color=0074BA>
		<b><?=$row['nameOrder']?></b><? if ($row['m_id']){ ?> (<?=$row['m_id']?>)</font><? if ($row['m_id']) { ?></span><? } ?>
		<? } ?>
	</td>

	<td><font class=small4><?=$r_istep[$row['istep']]?></font></td>
</tr>
<?
		$rowskip++;
		}
	}
?>
</table>

<div class=pageNavi align=center>
	<font class=ver8><?=$pg->page[navi]?></font>
</div>

<div class="el-goodsflow-descript">
	<h4>�½��÷� �����ȣ �߱�</h4>
	<dl>
		<dt>��ǰ�� �����ȣ �߱�</dt>
		<dd>�� ��ǰ���� ������ �����ȣ�� �߱��Ͽ� ����� ��� ����մϴ�.</dd>

		<dt>�κ� ��� �����ȣ �߱�</dt>
		<dd>�Ѱ��� �ֹ� ���� �κ� ��� �Ǵ� ��ǰ ���� ���� �ϳ��� �����ȣ�� �߱��Ͽ� ����ϴ� ��� ����մϴ�.</dd>
	</dl>

	<p>* �½��÷� �����ȣ �߱��� �ܿ� ��ġ���� �־�� �߱��� �����մϴ�. <a href="http://b2c.goodsflow.com/main_login.asp" target=_blank><font class=extext_l>[�½��÷� ��ġ�� Ȯ��]</font></a></p>

	<select name="target_type" align="absmiddle">
		<option value="choice">���õ� �ֹ�����</option>
		<option value="query">�˻��� �ֹ�����</option>
	</select>

	<button class="default-btn" onClick="fnGoodsflowInvoice('casebygoods');return false;">��ǰ�� �����ȣ �߱�</button>
	<button class="default-btn" onClick="fnGoodsflowInvoice('partial');return false;">�κй�� �����ȣ �߱�</button>

</div>
