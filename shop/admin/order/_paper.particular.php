<?
	### ����
	include_once dirname(__FILE__) . '/../../conf/config.pay.php';
	$sealpath = '/shop/data/skin/' . $cfg['tplSkin'] . '/img/common/' . $set['tax']['seal'];

	$classids = array( 'cssblue', 'cssred' ); //-- ���޹޴��ڿ�, �����ڿ�
	$headuser = array( 'cssblue'=>'���޹޴��ں�����', 'cssred'=>'�����ں�����' );

	$order = new order();
	$order->load($ordno);

	//����������
	if (!$cfg['road_address']) {
		$address = $cfg['address'];
	} else {
		$address = $cfg['road_address'];
	}

	//�հ�
	$totalAmount = $order->getRealPrnSettleAmount();

	//��ü��ҵ� �ֹ���
	if((int)$order['step2'] == 44){
		$totalAmount = 0;
	}
?>
<style type = "text/css">
td, th { font-family: ����; font-size: 9pt; color: 333333;}

#cssblue { width: 604px; border: solid 2px #364F9E;  }
#cssblue strong { font:18pt ����; color:#364F9E; font-weight:bold; }
#cssblue table { border-collapse: collapse; }
#cssblue td, #cssblue table { border-color: #364F9E; border-style: solid; border-width: 0; }

#cssblue #head { border-width: 1px 1px 0 1px; }
#cssblue #box td { border-width: 1px; padding-top: 3px; }

#cssred { width: 604px; border: solid 2px #FF4633;  }
#cssred strong { font:18pt ����; color:#FF4633; font-weight:bold; }
#cssred table { border-collapse: collapse; }
#cssred td, #cssred table { border-color: #FF4633; border-style: solid; border-width: 0; }

#cssred #head { border-width: 1px 1px 0 1px; }
#cssred #box td { border-width: 1px; padding-top: 3px; }
</style>
<div <? if( array_search('cssblue', $classids) !== false ){ ?>id="taxtable" taxsno="<?=$tax_data[sno]?>"<? } ?>>
<center>

<? foreach($classids as $cloop=>$classid){ ?>
<? if ( $cloop != 0 ){ ?>
<hr style="border:1px dashed #d9d9d9; width:500;">
<? } ?>
<div id="<?=$classid?>">
<table id="head" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="50%" align="right"><strong>�� �� �� �� ǥ</strong></td>
		<td width="50%" style="padding-left:6px">(<?=$headuser[$classid]?>)</td>
	</tr>
	</table>
	</td>
	<td width="60" style="border-right-width: 3px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="28" align="center">å&nbsp;��&nbsp;ȣ</td>
	</tr>
	<tr>
		<td height="24" align="center">�Ϸù�ȣ</td>
	</tr>
	</table>
	</td>
	<td width="150">
	<table width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr height="26">
		<td width="50%" align="right" style="border-right-width: 1px; border-bottom-width: 1px;"> ��</td>
		<td width="50%" align="right" style="border-bottom-width: 1px;"> ȣ</td>
	</tr>
	<tr height="26">
		<td align="center" style="border-right-width: 1px;">&nbsp;</td>
		<td align="center">&nbsp;</td>
	</tr>
	</table>
	</td>
</tr>
</table>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<col width=50%><col width=3%>
<tr>
	<!-- �հ�ݾ� -->
	<td valign="top" height="100%" style="border-width: 3px 1px 0 1px;">
	<table cellSpacing="0" cellPadding="2" width="100%" border="0">
	<tr>
		<td colspan="2">&nbsp;&nbsp;<?=substr($order['orddt'],0,4)?> ��&nbsp; <?=substr($order['orddt'],5,2)?> ��&nbsp; <?=substr($order['orddt'],8,2)?> ��</td>
		<td style="font-weight: normal; font-family: times new roman" width="30%"><I>No.</I></td>
	</tr>
	<tr>
		<td style="padding-right: 10px; border-bottom: thin solid"></td>
		<td style="padding-right: 10px; border-bottom: thin solid" vAlign="bottom" align="right" colspan="2">&nbsp;<font size=3><?=$order['nameOrder']?></font>&nbsp; <font size="3">����</font></td>
	</tr>
	<tr>
		<td></td>
		<td valign="bottom" colspan="2">�Ʒ��� ���� ����մϴ�.</td>
	</tr>
	<tr>
		<td align="middle" width="20%">�հ�ݾ�</td>
		<td style="padding-right: 10px" align="right" colspan="2">&nbsp;<font size="3"><?=number_format($totalAmount)?></font>&nbsp;<font size="3">����</font></td>
	</tr>
	</table>
	</td>
	<!-- ������ -->
	<td align="center" style="border-top-width: 3px; border-right-width: 1px; line-height: 36px; padding-left: 2px">��<br>��<br>��</td>
	<td valign="top" height="100%" style="border-top-width: 3px; background: url(<?=$sealpath?>) no-repeat; background-position: 207px 0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<col width=53><col width=100><col width=26>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 2px;">��Ϲ�ȣ</td>
		<td colspan="3" style="border-bottom-width: 1px; padding-left:6px;"><?=$cfg['compSerial']?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 3px 2px;">��&nbsp;&nbsp;&nbsp;&nbsp;ȣ<br>(���θ�) </td>
		<td style="padding:0 6px; border-bottom-width:3px;"><?=$cfg['compName']?></td>
		<td style="border-width: 0 1px 3px 1px;">����</td>
		<td style="border-bottom-width: 3px; padding-right:10px;"><?=$cfg['ceoName']?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-width: 0 1px 1px 0px;">�� �� ��<br>�� �� �� </td>
		<td colspan="3" style="padding: 0 6px; border-bottom-width: 1px;" align=left><?=$address?></td>
	</tr>
	<tr height="38" align="center">
		<td style="border-right-width: 1px;">��&nbsp;&nbsp;&nbsp;&nbsp;��</td>
		<td style="padding:0 6px;"><?=$cfg['service']?></td>
		<td style="border-width: 0 1px; padding-left:4px">��<br>�� </td>
		<td style="padding: 0 6px;"><?=$cfg['item']?></td>
	</tr>
	</table>
	</td>
</tr>
</table>

<!-- �ֹ�list -->
<table id="box" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top-width:2px;">
<colgroup span="2" width="3%"></colgroup><col><colgroup span="2" width="6%"></colgroup><col width="10%"><col width="14%"><col width="10%">
<tr height="24" align="center">
	<td>��</td>
	<td>��</td>
	<td>ǰ&nbsp;��&nbsp; / &nbsp;��&nbsp;��</td>
	<td>��&nbsp;��</td>
	<td>��&nbsp;��</td>
	<td>��&nbsp;��</td>
	<td>��&nbsp;��&nbsp;��&nbsp;��</td>
	<td>��&nbsp;��</td>
</tr>
<?
$total = array(
	'etc' => $totalAmount,
	'supply' => 0,
	'tax' => 0,
);

$rowCount = 0;
$refundedFeeAmount = 0; //ȯ�Ҽ�����
foreach ($order->getOrderItems() as $v){
	if ($v->hasCancelCompleted()) {
		$refundedFeeAmount += array_sum($v->getRefundedFeeAmount());
		continue;
	}

	$rowCount++;

	// �ݾ� ���
	$supply = $v->getSupplyPrice();
	$tax = $v->getTax();

	// �ݾ� ����
	$total['etc'] -= $v->getAmount();
	$total['supply'] += $supply;
	$total['tax'] += $tax;
?>
<tr height="25">
	<td align="middle"><?=substr($order['orddt'],5,2)?></td>
	<td align="middle"><?=substr($order['orddt'],8,2)?></td>
	<td>
	<?=$v['goodsnm']?>
	<? if ($v['opt1']){ ?>[<?=$v['opt1']?><? if ($v['opt2']){ ?>/<?=$v['opt2']?><? } ?>]<? } ?>
	<? if ($v['addopt']){ ?><div>[<?=str_replace('^','] [',$v['addopt'])?>]</div><? } ?>
	</td>
	<td>&nbsp;</td>
	<td align="middle"><?=$v['ea']?></td>
	<td align="right" style="padding-right:6px"><?=number_format($v['price'])?></td>
	<td align="right" style="padding-right:6px"><?=number_format($supply)?></td>
	<td align="right" style="padding-right:6px"><?=number_format($tax)?></td>
</tr>
<? } ?>
<?
if ($total['etc']) {
	//��ۺ� + ������������� + ȯ�Ҽ�����
	$total['etcAddPrice'] = $order->getTaxAddAmount() + $refundedFeeAmount - $order->getCancelCompletedDeliveryFee();
	if($total['etcAddPrice'] > 0){
		$total['etcAddPriceSupply'] = round($total['etcAddPrice']/1.1);
		$total['etcAddPriceSurtax'] = $total['etcAddPrice'] - $total['etcAddPriceSupply'];
	}

	//����
	$total['discount'] = $order->getDiscount() - $order->getCancelCompletedMemberDiscount() - $order->getCancelCompletedGoodsDiscount() - $order->getCancelCompletedCouponDiscount() - $order->getCancelCompletedEmoney();
	if($total['discount'] > 0){
		$total['discountSupply'] = round($total['discount']/1.1);
		$total['discountSurtax'] = $total['discount'] - $total['discountSupply'];
	}

	$total['supply'] = $total['supply']+$total['etcAddPriceSupply']-$total['discountSupply'];
	$total['tax'] = $total['tax']+$total['etcAddPriceSurtax']-$total['discountSurtax'];

	$rowCount++;
?>
<tr height="25">
	<td align="middle"><?=substr($order['orddt'],5,2)?></td>
	<td align="middle"><?=substr($order['orddt'],8,2)?></td>
	<td>��Ÿ �߰� (��ۺ�, ȯ�� ������ ��)</td>
	<td>&nbsp;</td>
	<td align="middle">1</td>
	<td align="right" style="padding-right:6px"><?=number_format($total['etcAddPrice'])?></td>
	<td align="right" style="padding-right:6px"><?=number_format($total['etcAddPriceSupply'])?></td>
	<td align="right" style="padding-right:6px"><?=number_format($total['etcAddPriceSurtax'])?></td>
</tr>
<tr height="25">
	<td align="middle"><?=substr($order['orddt'],5,2)?></td>
	<td align="middle"><?=substr($order['orddt'],8,2)?></td>
	<td>��Ÿ (���� ��)</td>
	<td>&nbsp;</td>
	<td align="middle">1</td>
	<td align="right" style="padding-right:6px"><?=number_format($total['discount']*-1)?></td>
	<td align="right" style="padding-right:6px"><?=number_format($total['discountSupply']*-1)?></td>
	<td align="right" style="padding-right:6px"><?=number_format($total['discountSurtax']*-1)?></td>
</tr>
<? } ?>
<tr height="25">
	<td align=middle colspan=8>*** �� �� �� �� *** </td>
</tr>
<? for ($i=$rowCount;$i<5;$i++){ ?>
<tr height="25">
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<? } ?>
</table>

<table id="box" width="100%" border="0" cellspacing="0" cellpadding="0">
<col width="10%"><col width="20%"><col width="10%"><col width="20%"><col width="16%"><col width="14%"><col width="10%">
<tr align="center">
	<td style="border-top-width: 0;" colspan="5">�� ��</td>
	<td style="border-top-width: 0; padding-right:6px" align="right"><?=number_format($total['supply'])?></td>
	<td style="border-top-width: 0; padding-right:6px" align="right"><?=number_format($total['tax'])?></td>
</tr>
<tr height="25" align="center">
	<td>�̼���</td>
	<td>&nbsp;</td>
	<td>��&nbsp;��</td>
	<td><?=number_format($totalAmount)?></td>
	<td>�μ���</td>
	<td colspan="2" align="right" style="padding-right:6px;"><?=$order['nameOrder']?></td>
</tr>
</table>
</div>
<? } ?>

</center>
</div>