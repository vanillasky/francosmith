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
<?
while ($row = $db->fetch($res,1)) {

	$row['orddt'] = substr($row['orddt'], 0, -3);

	if($row['goods_cnt']>1) {
		$row['goodsnm'] = $row['goodsnm'].' �� '.($row['goods_cnt']-1).'��';
	}

?>
<tr height="25" bgcolor="#ffffff" bg=""#ffffff" align=center>
	<td class="noline"><input type="checkbox" name="target[ordno][]" value="<?=$row['ordno']?>" class="chk_ordno" address="<?=htmlspecialchars($row['address'])?>" onclick="iciSelect(this)"></td>
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><font class="ver81" color="#616161"><?=$row['orddt']?></font></td>
	<td>
		<a href="view.php?ordno=<?=$row['ordno']?>"><font class=ver81 color=<?=$row['flg_inflow'] == 'sugi' ? 'ED6C0A' : '0074BA'?>><b><?=$row['ordno']?><?=$row['flg_inflow'] == 'sugi' ? '<span class="small1">(����)</span>' : ''?></b></font></a>
		<a href="javascript:popup('popup.order.php?ordno=<?=$row['ordno']?>',800,600)"><img src="../img/btn_newwindow.gif" border=0 align=absmiddle></a>
	</td>
	<td>
		<div>
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

	<td><font class="small1" color="#444444"><?=getStepMsg($row['step'],$row['step2'])?></font></td>
</tr>
<tr><td colspan="7" bgcolor="#E4E4E4"></td></tr>
<? } ?>
</table>

<div class=pageNavi align=center>
	<font class=ver8><?=$pg->page[navi]?></font>
</div>

<div class="el-goodsflow-descript">
	<h4>�½��÷� �����ȣ �߱�</h4>
	<dl>
		<dt>�ֹ��Ǻ� �����ȣ �߱�</dt>
		<dd>������ �ֹ��ǿ� �����ȣ�� �߱� �˴ϴ�.</dd>

		<dt>������ �����ȣ �߱�</dt>
		<dd>�ֹ��ڰ� ���� ��ǰ�� �������Ͽ� �ϳ��� �����ȣ�� �߱� ���� �� �ֽ��ϴ�.</dd>
	</dl>

	<p>* �½��÷� �����ȣ �߱��� �ܿ� ��ġ���� �־�� �߱��� �����մϴ�. <a href="http://b2c.goodsflow.com/main_login.asp" target=_blank><font class=extext_l>[�½��÷� ��ġ�� Ȯ��]</font></a></p>

	<hr>

	<select name="target_type" align="absmiddle">
		<option value="choice">���õ� �ֹ�����</option>
		<option value="query">�˻��� �ֹ�����</option>
	</select>

	<button class="default-btn" onClick="fnGoodsflowInvoice('casebyorder');return false;">�ֹ��Ǻ� �����ȣ �߱�</button>
	<button class="default-btn" onClick="fnGoodsflowInvoice('package');return false;">������ �����ȣ �߱�</button>
</div>
