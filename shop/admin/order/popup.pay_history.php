<?
$popup = 1;
include "../_header.popup.php";

//------- 주문내역서 출력시 _form.php 를 그대로 출력하기 때문에 페이지가 늘어나고 주소가 짤리는 문제로 인해
//------- 출력용으로 _reportForm.php 파일 생성함, 현페이지 메뉴및 쿼리수정시 _reportForm.php 도 함께 수정해주어야 함.
@include "../../conf/egg.usafe.php";
@include "../../conf/config.pay.php";
@include "../../conf/phone.php";

### 주문리스트 리퍼러
$referer = ($_GET[referer]) ? $_GET[referer] : $_SERVER[HTTP_REFERER];

// 주문정보
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

<div class="title title_top">결제히스토리<span>이 주문에 대한 결제취소 히스토리를 조회하실 수 있습니다</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=2')"><img src="../img/btn_q.gif" border=0 hspace=2 align=absmiddle></a></div>

<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><?=$order['orddt']?> <font color=627dce>최초결제</font></div>
<table border=2 bordercolor=627dce style="border-collapse:collapse" width=100%>
<tr><td style="padding:1px;">
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>번호</th>
	<th colspan=2><font color=white>상품명</th>
	<th><font color=white>상품가격</th>
	<th><font color=white>수량</th>
	<th><font color=white>회원할인</th>
	<th><font color=white>상품할인</th>
	<th><font color=white>쿠폰할인</th>
	<th><font color=white>결제금액</th>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$total_price = $coupon_price = 0;

// 주문 상품
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
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<투데이샵상품>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>제조사 : <?=$item[maker] ? $item[maker] : '없음'?></div>
	<div><font class=small1 color=6d6d6d>브랜드 : <?=$item[brandnm] ? $item[brandnm] : '없음'?></div>
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
	<td colspan="3" align="center" valign="middle" bgcolor="lightyellow"><strong>합계</strong></td>
	<td colspan="6" bgcolor="lightyellow">
		<table class="admin-list-table">
		<tbody>
			<tr>
				<th>&nbsp;</th>
				<td align="right"><strong><?=number_format($total_price)?></strong></td>
			</tr>
			<tr>
				<th>배송비</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order->getDeliveryFee())?></font></td>
			</tr>
			<? if ($order->getCouponDiscount() - $coupon_price > 0){ ?>
			<tr>
				<th>주문쿠폰</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getCouponDiscount() - $coupon_price)?></font></td>
			</tr>
			<? } ?>
			<? if ($order['emoney'] > 0){ ?>
			<tr>
				<th>적립금</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order['emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getNcashCashDiscount() > 0){ ?>
			<tr>
				<th>네이버캐시</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getNcashCashDiscount())?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getNcashEmoneyDiscount() > 0){ ?>
			<tr>
				<th>네이버마일리지</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getNcashEmoneyDiscount())?></font></td>
			</tr>
			<? } ?>
			<? if ($order['eggFee'] > 0){ ?>
			<tr>
				<th>보증보험수수료</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order['eggFee'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getEnuriAmount() > 0){ ?>
			<tr>
				<th>에누리</th>
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
<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><?=$cancel['regdt']?> <font color=F43400><?=$is_last ? '주문취소' : '부분취소'?></font></div>
<table border=2 bordercolor=F43400 style="border-collapse:collapse" width=100%>
<tr><td style="padding:1px;">
<table class=tb cellpadding=4 cellspacing=0>
<tr height=25 bgcolor=#2E2B29 class=small4 style="padding-top:8px">
	<th><font color=white>번호</th>
	<th colspan=2><font color=white>상품명</th>
	<th><font color=white>상품가격</th>
	<th><font color=white>수량</th>
	<th><font color=white>회원할인</th>
	<th><font color=white>상품할인</th>
	<th><font color=white>쿠폰할인</th>
	<th><font color=white>결제금액</th>
</tr>
<col align=center span=3><col>
<col align=center span=10>
<?
$total_price = $total_settleprice = 0;
$coupon_price = $order->getPercentCouponDiscount();

// 주문 상품
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
	<font class=small color=0074BA><? if ($item['todaygoods']=='y') echo '<투데이샵상품>'?><?=$item[goodsnm]?>
	<? if ($item[opt1]){ ?>[<?=$item[opt1]?><? if ($item[opt2]){ ?>/<?=$item[opt2]?><? } ?>]<? } ?>
	<? if ($item[addopt]){ ?><div>[<?=str_replace("^","] [",$item[addopt])?>]</div><? } ?></a>
	<div style="padding-top:3"><font class=small1 color=6d6d6d>제조사 : <?=$item[maker] ? $item[maker] : '없음'?></div>
	<div><font class=small1 color=6d6d6d>브랜드 : <?=$item[brandnm] ? $item[brandnm] : '없음'?></div>
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
	<td colspan="3" align="center" valign="middle" bgcolor="lightyellow"><strong>합계</strong></td>
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
                <th>배송비</th>
                <td align="right" class="blue"><font class=ver8>+ <?=number_format($cancel->getPaycoCancelCompletedDeliveryFee($order['pg']))?></font></td>
            </tr>
			<? if ($cancel['rncash_cash'] > 0){ ?>
			<tr>
				<th>네이버캐시</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_cash'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_emoney'] > 0){ ?>
			<tr>
				<th>네이버마일리지</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rfee'] > 0){ ?>
			<tr>
				<th>환불수수료</th>
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
				<th>배송비</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($deliver_fee)?></font></td>
			</tr>
			<? if ($order->getCouponDiscount() - $coupon_price > 0){ ?>
			<tr>
				<th>주문쿠폰</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getCouponDiscount() - $coupon_price)?></font></td>
			</tr>
			<? } ?>
			<? if ($order['emoney'] > 0){ ?>
			<tr>
				<th>적립금</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order['emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_cash'] > 0){ ?>
			<tr>
				<th>네이버캐시</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_cash'])?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rncash_emoney'] > 0){ ?>
			<tr>
				<th>네이버마일리지</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($cancel['rncash_emoney'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order['eggFee'] > 0){ ?>
			<tr>
				<th>보증보험수수료</th>
				<td align="right" class="blue"><font class=ver8>+ <?=number_format($order['eggFee'])?></font></td>
			</tr>
			<? } ?>
			<? if ($order->getEnuriAmount() > 0){ ?>
			<tr>
				<th>에누리</th>
				<td align="right" class="red"><font class=ver8>- <?=number_format($order->getEnuriAmount())?></font></td>
			</tr>
			<? } ?>
			<? if ($cancel['rfee'] > 0){ ?>
			<tr>
				<th>환불수수료</th>
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
	@include dirname(__FILE__) . "/../interpark/_openstyle_order_form.php"; // 인터파크_인클루드
}else{
	@include dirname(__FILE__) . "/../interpark/_order_form.php"; // 인터파크_인클루드
}

?>
