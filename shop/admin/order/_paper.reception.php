<?
	$classids = array( "cssblue", "cssred" ); //-- ���޹޴��ڿ�, �����ڿ�
	$headuser = array( "���޹޴��ڿ�", "�����ڿ�" ); //-- ���޹޴��ڿ�, �����ڿ�

	### ����
	include_once dirname(__FILE__) . "/../../conf/config.pay.php";
	$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set[tax][seal];

	$order = new order();
	$order->load($ordno);

	//����������
	if (!$cfg['road_address']) {
		$address = $cfg['address'];
	} else {
		$address = $cfg['road_address'];
	}

	$totalAmount = $order->getRealPrnSettleAmount();
?>
<style type = "text/css">
td, th { font-family: ����; font-size: 9pt; color: 333333;}

#cssblue { width: 306px; border: solid 2px #364f9e;  }
#cssblue table { border-collapse: collapse; }
#cssblue td { border-color:#364f9e; border-width:2px; border-style:solid; }

#cssblue #head { border-color:#364f9e; border-width:2px 2px 0px 2px; border-style:solid; }
#cssblue #head td { border-width:0px; border-style:solid; }

#cssred { width: 306px; border: solid 2px #ff4633;  }
#cssred table { border-collapse: collapse; }
#cssred td { border-color:#ff4633; border-width:2px; border-style:solid; }

#cssred #head { border-color:#ff4633; border-width:2px 2px 0px 2px; border-style:solid; }
#cssred #head td { border-width:0px; border-style:solid; }
</style>

<table cellspacing=10 cellpadding=0 border=0 align="center">
<tr valign="top">
<? for($loop=0; $loop < 2; $loop++){ ?>
	<td>
	<div id="<?=$classids[$loop]?>">
	<table id=head cellspacing=0 cellpadding=0 width="100%" border=0>
	<tr>
		<td width="23%" height=40>&nbsp;</td>
		<td align=middle width="44%">&nbsp;<font size=4>�� �� ��</font></td>
		<td width="33%"><font style="font-weight: normal" size=1>( <?=$headuser[$loop]?> )</font></td>
	</tr>
	</table>

	<table width=100% border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td height=100% valign=top style="border-width: 3px 1px 0 0; background: url(<?=$sealpath?>) no-repeat; background-position: 235px 15px;">
			<table cellspacing=0 cellpadding=2 width="100%" border=0>
			<col width="8%"><col width="20%"><col width="30%"><col width="12%">
			<tr>
				<td valign=bottom colspan=2>no.</td>
				<td style="border-top-width: 0px;" align=right colspan=3>&nbsp;<font style="font-weight: normal; font-size: 12pt" color=black><?=$order[nameOrder]?>&nbsp;&nbsp;</font><font size=3>����</font>&nbsp;</td>
			</tr>
			<tr align=middle>
				<td rowspan=4 height="100%">��<br><br>��<br><br>��</td>
				<td>�� �� ��<br>��Ϲ�ȣ</td>
				<td colspan=3 align=left style="padding-left:10">&nbsp;<font size=3><?=$cfg[compSerial]?></font></td>
			</tr>
			<tr align=middle height=30>
				<td>�� ȣ</td>
				<td>&nbsp;<?=$cfg[compName]?></td>
				<td>����</td>
				<td>&nbsp;<?=$cfg[ceoName]?></td>
			</tr>
			<tr align=middle>
				<td>�� �� ��<br>�� �� ��</td>
				<td colspan=3>&nbsp;<?=$address?></td>
			</tr>
			<tr align=middle height=30>
				<td>����</td>
				<td>&nbsp;<?=$cfg[service]?></td>
				<td>����</td>
				<td>&nbsp;<?=$cfg[item]?></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=2 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px;">�ۼ������</td>
		<td style="border-left-width: 3px; border-right-width: 3px;">���޴밡�Ѿ�</td>
		<td style="border-top-width: 0px;">�� ��</td>
	</tr>
	<tr align=middle>
		<td>&nbsp; <?=toDate(str_replace("-","",$order[orddt]),". ")?></td>
		<td style="border-left-width: 3px; border-right-width: 3px; border-bottom-width: 4px;">&nbsp;��<?=number_format($totalAmount)?></td>
		<td align=right></td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=4 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px; border-bottom-width: 0px;">�� �ݾ��� ���� ����( û�� )��</td>
	</tr>
	</table>

	<table cellspacing=0 cellpadding=2 width="100%" border=0>
	<tr align=middle>
		<td>��</td>
		<td>��</td>
		<td>ǰ ��</td>
		<td>����</td>
		<td>�ܰ�</td>
		<td>�ݾ�</td>
	</tr>
	<?
	$total = array(
		'etc' => $totalAmount,	// ����, ��ۺ� ���� �ջ� (=�ֹ��� �� �����ݾ� - ��ǰ���� ��)
	);

	$rowCount = 0;

	foreach ($order->getOrderItems() as $v){
		if ($v->hasCancelCompleted()) continue;

		$rowCount++;

		// �ݾ� ����
		$total['etc'] -= $v->getAmount();
	?>
	<tr>
		<td align=middle><?=substr($order[orddt],5,2)?></td>
		<td align=middle><?=substr($order[orddt],8,2)?></td>
		<td height=20>
		<?=$v[goodsnm]?>
		<? if ($v[opt1]){ ?>[<?=$v[opt1]?><? if ($v[opt2]){ ?>/<?=$v[opt2]?><? } ?>]<? } ?>
		<? if ($v[addopt]){ ?><div>[<?=str_replace("^","] [",$v[addopt])?>]</div><? } ?>
		</td>
		<td align=middle><?=$v[ea]?></td>
		<td align=right><?=number_format($v[price])?></td>
		<td align=right><?=number_format($v->getAmount())?></td>
	</tr>
	<? } ?>
	<?
	if ($total['etc']) {
		$rowCount++;
	?>
	<tr>
		<td align=middle><?=substr($order[orddt],5,2)?></td>
		<td align=middle><?=substr($order[orddt],8,2)?></td>
		<td height=20>��Ÿ (��ۺ�, ���� ��)</td>
		<td align=middle>1</td>
		<td align=right><?=number_format($total['etc'])?></td>
		<td align=right><?=number_format($total['etc'])?></td>
	</tr>
	<? } ?>
	<tr>
		<td align=middle colspan=6>*** �� �� �� �� *** </td>
	</tr>
	<? for ($i=$rowCount;$i<9;$i++){ ?>
	<tr align=middle>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td align=right>&nbsp;</td>
	</tr>
	<? } ?>
	</table>

	<table cellspacing=0 cellpadding=4 width="100%" border=0>
	<tr align=middle>
		<td style="border-top-width: 0px;" height=25><font style="font-weight: normal" size=1>�ΰ���ġ���������Ģ ��25�� ������ ���� ( ������ )���� ����.</font></td>
	</tr>
	</table>
	</div>
	</td>
<? } ?>
</tr>
</table>