<?
$popup = 1;
include "../_header.popup.php";

//------- �ֹ������� ��½� _form.php �� �״�� ����ϱ� ������ �������� �þ�� �ּҰ� ©���� ������ ����
//------- ��¿����� _reportForm.php ���� ������, �������� �޴��� ���������� _reportForm.php �� �Բ� �������־�� ��.
@include "../../conf/egg.usafe.php";
@include "../../conf/config.pay.php";
@include "../../conf/phone.php";

### �ֹ�����Ʈ ���۷�
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

// �ֹ�����
$ordno = $_GET[ordno];
$order = new order();
$order->load($ordno);
?>

<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
table.admin-list-table tbody th{border-bottom:1px solid #ccc;}
table.admin-list-table tbody th,table.admin-list-table tbody td{background-color:transparent;}
table.admin-list-table tbody tr.last th,table.admin-list-table tbody tr.last td{border-bottom: none;}
</style>
<?getjskPc080();?>

<div class="title title_top">���������丮<span>�� �ֹ��� ���� ������� �����丮�� ��ȸ�Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><?=$order['orddt']?> <font color=627dce>���ʰ���</font></div>
<table border=2 bordercolor=627dce style="border-collapse:collapse" width=100%>
<tr><td style="padding:1px;">
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>��ȣ</th>
	<th colspan=2><font color=white>��ǰ��</th>
	<th><font color=white>��ǰ����</th>
	<th><font color=white>����</th>
	<th><font color=white>ȸ������</th>
	<th><font color=white>��ǰ����</th>
	<th><font color=white>��������</th>
	<th><font color=white>�����ݾ�</th>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$total_price = $coupon_price = 0;

// �ֹ� ��ǰ
foreach($order->getOrderItems() as $idx => $item) {
	$total_price += $item->getSettleAmount();
	$coupon_price += $item->getPercentCouponDiscount();
?>
<tr>
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<? if ($item['todaygoods'] != 'y') { ?>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<? } else { ?>
	<td width=50 nowrap><a href="../../todayshop/today_goods.php?tgsno=<?=$item[tgsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="../todayshop/goods_reg.php?mode=modify&tgsno=<?=$item[tgsno]?>"  target=_blank>
	<? } ?>
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<�����̼���ǰ>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>������ : <?=$item[maker] ? $item[maker] : '����'?></div>
	<div><font class=small1 color=6d6d6d>�귣�� : <?=$item[brandnm] ? $item[brandnm] : '����'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	</td>
	<td width=55 nowrap><?=number_format($item[price])?></td>
	<td width=55 nowrap><?=number_format($item[ea])?></td>
	<td width=55 nowrap><?=number_format($item->getMemberDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getSpecialDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getPercentCouponDiscount())?></td>
	<td width=85 nowrap><b><?=number_format($item->getSettleAmount())?></b></td>
</tr>
<?
}
?>
<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="lightyellow"><strong>�հ�</strong></td>
	<td colspan="6" bgcolor="lightyellow">
		<table class="admin-list-table">
		<tbody>
			<tr>
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_price)?></strong></td>
			</tr>
			<tr>
				<th>��ۺ�</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order->getDeliveryFee())?></font></td>
			</tr>
			<? if ($order->getCouponDiscount() - $coupon_price > 0){ ?>
			<tr>
				<th>�ֹ�����</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getCouponDiscount() - $coupon_price)?></font></td>
			</tr>
			<? } ?>
			<? if ($order['emoney'] > 0){ ?>
			<tr>
				<th>������</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order['emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getNcashCashDiscount() > 0){ ?>
			<tr>
				<th>���̹�ĳ��</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getNcashCashDiscount())?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getNcashEmoneyDiscount() > 0){ ?>
			<tr>
				<th>���̹����ϸ���</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getNcashEmoneyDiscount())?></font></td>
			</tr>
			<? } ?>
			<? if ($order['eggFee'] > 0){ ?>
			<tr>
				<th>�������������</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order['eggFee'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getEnuriAmount() > 0){ ?>
			<tr>
				<th>������</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getEnuriAmount())?></font></td>
			</tr>
			<? } ?>
			<tr class="last">
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($order->getSettleAmount())?></strong></td>
			</tr>
		</tbody>
		</table>
	</td>
</tr>
</table>
</td></tr></table><p>

<?
$cancel_item_count = 0;
foreach($order->getOrderCancels() as $cnt => $cancel) {
	if(!$cancel->hasCancelCompleted()) continue;
	$cancel_item_count += sizeof($cancel->getOrderItems());
	$is_last = sizeof($order->getOrderItems()) == $cancel_item_count;
?>
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><?=$cancel['regdt']?> <font color=F43400><?=$is_last ? '�ֹ����' : '�κ����'?></font></div>
<table border=2 bordercolor=F43400 style="border-collapse:collapse" width=100%>
<tr><td style="padding:1px;">
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>��ȣ</th>
	<th colspan=2><font color=white>��ǰ��</th>
	<th><font color=white>��ǰ����</th>
	<th><font color=white>����</th>
	<th><font color=white>ȸ������</th>
	<th><font color=white>��ǰ����</th>
	<th><font color=white>��������</th>
	<th><font color=white>�����ݾ�</th>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$total_price = $total_settleprice = 0;
$coupon_price = $order->getPercentCouponDiscount();

// �ֹ� ��ǰ
foreach($cancel->getOrderItems() as $idx => $item) {
	$total_price += $item->getSettleAmount();
?>
<tr>
	<td width=35 nowrap><font class=ver8 color=444444><?=++$idx?></td>
	<? if ($item['todaygoods'] != 'y') { ?>
	<td width=50 nowrap><a href="../../goods/goods_view.php?goodsno=<?=$item[goodsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$item[goodsno]?>',825,600)">
	<? } else { ?>
	<td width=50 nowrap><a href="../../todayshop/today_goods.php?tgsno=<?=$item[tgsno]?>" target=_blank><?=goodsimg($item[img_s],30,"style='border:1 solid #cccccc'",1)?></a></td>
	<td width=100%><a href="../todayshop/goods_reg.php?mode=modify&tgsno=<?=$item[tgsno]?>"  target=_blank>
	<? } ?>
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<�����̼���ǰ>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>������ : <?=$item[maker] ? $item[maker] : '����'?></div>
	<div><font class=small1 color=6d6d6d>�귣�� : <?=$item[brandnm] ? $item[brandnm] : '����'?></div>
	<? if ($item[deli_msg]){ ?><div><font class=small1 color=6d6d6d>(<?=$item[deli_msg]?>)</font></div><? } ?>
	</td>
	<td width=55 nowrap><?=number_format($item[price])?></td>
	<td width=55 nowrap><?=number_format($item[ea])?></td>
	<td width=55 nowrap><?=number_format($item->getMemberDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getSpecialDiscount())?></td>
	<td width=55 nowrap><?=number_format($item->getPercentCouponDiscount())?></td>
	<td width=85 nowrap><b><?=number_format($item->getSettleAmount())?></b></td>
</tr>
<?
}
?>
<tr>
	<td colspan="3" align="center" valign="middle" bgcolor="lightyellow"><strong>�հ�</strong></td>
	<td colspan="6" bgcolor="lightyellow">
		<table class="admin-list-table">
		<tbody>
			<?
			if(!$is_last) {
				$total_settleprice =
					+ $total_price
					+ $cancel->getPaycoCancelCompletedDeliveryFee($order['pg'])
					- $cancel['rncash_cash']
					- $cancel['rncash_emoney']
					- $cancel['rfee'];
			?>
			<tr>
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_price)?></strong></td>
			</tr>
			<tr>
                <th>��ۺ�</th>
                <td align="right" class="blue"><font class=ver8>+ <?=number_format($cancel->getPaycoCancelCompletedDeliveryFee($order['pg']))?></font></td>
            </tr>
			<? if ($cancel['rncash_cash'] > 0){ ?>
			<tr>
				<th>���̹�ĳ��</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_cash'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_emoney'] > 0){ ?>
			<tr>
				<th>���̹����ϸ���</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rfee'] > 0){ ?>
			<tr>
				<th>ȯ�Ҽ�����</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rfee'])?></font></td>
			</tr>
			<? } ?>
			<tr class="last">
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_settleprice)?></strong></td>
			</tr>
			<?
			} else {
				$deliver_fee = $cancel->getPaycoCancelCompletedDeliveryFee($order['pg']) > 0 ? $cancel->getPaycoCancelCompletedDeliveryFee($order['pg']) : $order->getDeliveryFee();
				$total_settleprice =
					+ $total_price
					+ $deliver_fee
					- ($order->getCouponDiscount() - $coupon_price)
					- $order['emoney']
					- $cancel['rncash_cash']
					- $cancel['rncash_emoney']
					+ $order['eggFee']
					- $order['enuri']
					+ $cancel['rfee'];
			?>
			<tr>
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_price)?></strong></td>
			</tr>
			<tr>
				<th>��ۺ�</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($deliver_fee)?></font></td>
			</tr>
			<? if ($order->getCouponDiscount() - $coupon_price > 0){ ?>
			<tr>
				<th>�ֹ�����</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getCouponDiscount() - $coupon_price)?></font></td>
			</tr>
			<? } ?>
			<? if ($order['emoney'] > 0){ ?>
			<tr>
				<th>������</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order['emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_cash'] > 0){ ?>
			<tr>
				<th>���̹�ĳ��</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_cash'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_emoney'] > 0){ ?>
			<tr>
				<th>���̹����ϸ���</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order['eggFee'] > 0){ ?>
			<tr>
				<th>�������������</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order['eggFee'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getEnuriAmount() > 0){ ?>
			<tr>
				<th>������</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getEnuriAmount())?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rfee'] > 0){ ?>
			<tr>
				<th>ȯ�Ҽ�����</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rfee'])?></font></td>
			</tr>
			<? } ?>
			<tr class="last">
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_settleprice)?></strong></td>
			</tr>
			<?}?>
		</tbody>
		</table>
	</td>
</tr>
</table>
</td></tr></table><p>
<?
}
?>


<div style="padding:10px" align=center><a href="javascript:window.close()"><img src="../img/btn_close_s.gif"></a></div>

<script>window.onload = function(){ UNM.inner();};</script>
<?
if($order[inflow]=="openstyle"){
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // ������ũ_��Ŭ���
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // ������ũ_��Ŭ���
}

?>
