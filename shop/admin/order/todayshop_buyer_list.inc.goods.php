<!--
	투데이샵 상품별 주문자 리스트 (실물상품)
-->

<div class="title title_top">주문목록<span></div>

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
아래 주문들을 
<select name="status">
	<option value="">======</option>
	<option value="1">입금확인</option>
	<option value="2">배송준비중</option>
	<option value="3">배송중</option>
	<option value="4">배송완료</option>
</select>
상태로 변경합니다. <input type="image" src="../img/btn_editsmstext.gif" />
</form>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=7></td></tr>
<tr class=rndbg>
	<th width=60>주문자명</th>
	<th>주문번호</th>
	<th>주문일시</th>
	<th><?=($goods['goodstype'] == 'coupon') ? '쿠폰번호' : '송장번호' ?></th>
	<th>수량</th>
	<th>주문상태</th>
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

	<td class="<?=($row['deliverycode']) ? 'blue' : 'red'?>"><?=($row['deliverycode']) ? $row['deliverycode'] : '미등록'?></td>
	<td><?=$row[ea]?></td>

	<td class=small4><?=$step?></td>

	<td class=small4>
	<? if ($goods['stats'] == 3) { ?>
		<? if ($row['step2'] < 40) { ?><img src="../img/today_btn_cn.gif" class="hand" onClick="nsTodayshopControl.order.cancel(<?=$row[ordno]?>);"><? } ?>
	<? } elseif ($row['step2'] < 40) { ?>
	<img src="../img/today_btn_no_in.gif" class="hand" onClick="<?if ($goods['stats'] == 4) {?>nsTodayshopControl.order.delivery(<?=$row[ordno]?>);<?} else {?>alert('판매가 완료된 상품만 송장입력이 가능합니다.');<?}?>">
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
		//echo '판매대기';
		break;
	case '3' :	// 판매실패
?>
		<!--img src="../img/today_btn_cn_orderall.gif" onMouseOver='this.src="../img/today_btn_cn_orderallon.gif";' onMouseOut='this.src="../img/today_btn_cn_orderall.gif";' border=0 onClick="nsTodayshopControl.order.cancel_all('<?=$goods['goodsno']?>');" class="hand"-->
<?
		break;
	case '4' :	// 판매완료
	case '2' :	// 판매중
		if ($goods['stats'] == '2' && $goods['processtype'] != 'i') break;	// 판매중 상품이지만, 즉시발송이 아닐때에는 스톱.
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
		include "_deliveryForm.php"; //송장일괄입력폼
		break;
}
?>