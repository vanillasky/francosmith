<?
$ordno = $_GET[ordno];
$order = Core::loader('order');
$order->load($ordno);

### 입금계좌 정보
$query = "select * from ".GD_LIST_BANK." where  sno = ".$order['bankAccount']."   order by sno";
$bankData= $db->fetch($query);
 
### 배송업체 정보
$query = "select deliverycomp from ".GD_LIST_DELIVERY." where useyn='y' and deliveryno = '".$order['deliveryno']."' order by deliverycomp";
list($deliverycomp)= $db->fetch($query);
?>
<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}

.layer-title{
	font-size: 15px;
	font-weight:bold;
	color:#000000;
	padding-bottom:15px
}

.info-layer{
	margin-bottom:23px;
}

.bottom-line{
	border-bottom:1px solid #999999
}

.grayfont{
	color:#868686;
}
</style>
<script>
function orderPrint(ordno,type)
{
	if (!type){
		alert("인쇄할 문서 종류를 선택하세요");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600");
	orderPrint.window.print();
}
 
</script>
 
<div style="margin-bottom:20px">
	<span style="color:#000000;font:bold 25px verdana">주문내역서</span>
</div>

<!--주문정보-->
<div class='info-layer'>
	<div class='layer-title'>주문정보</div>
	<table width=100% cellpadding=5 cellspacing=0>
		<colgroup>
			<col width='20%'>
			<col>
			<col width='20%'>
			<col>
		</colgroup>
		<tr>
			<td class='grayfont'>주문번호</td>
			<td><?=$ordno?></td>
			<td class='grayfont'>주문일</td>
			<td><?=$order['orddt']?></td>
		</tr>
		<tr>
			<td class='grayfont'>주문자(ID)</td>
			<td>
				<?=$order['nameOrder']?>
				<? if ($order['m_id']){ ?> (<?=$order['m_id']?>)
				<? } ?>
			</td>
			<td class='grayfont'>이메일</td>
			<td><?=$order['email']?></td>
		</tr>
		<tr>
			<td class='grayfont'>전화번호</td>
			<td><?=$order['phoneOrder']?></td>
			<td class='grayfont'>휴대전화</td>
			<td><?=$order['mobileOrder']?></td>
		</tr>
	</table>
</div>
 
<!--상품정보-->
<div class='info-layer'>
	<div class='layer-title'>상품정보</div>
	<table   cellpadding=4 cellspacing=0 width='100%' border='0'>
		<colgroup>
			<col width='10%'>
			<col>
			<col width='10%'>
			<col width='14%' align='right'>
			<col width='14%' align='right'>
			<col width='14%' align='right'>
		</colgroup>
		<tr height=25    align='center' class='grayfont'>
			<td class="bottom-line">번호</td>
			<td class="bottom-line" >상품명 / 모델명</td>
			<td class="bottom-line">수량</td>
			<td class="bottom-line" >판매가</td>
			<td class="bottom-line" >할인금액</td>
			<td class="bottom-line" >결제금액</td>
		</tr>

		<?
		$idx = $goodsprice = 0;

		unset($goodsprice,$memberdc,$coupon,$dc);
		$total_ea = 0;
		$total_price = 0;
		$total_discount = 0;
		$total_settleAmount = 0;
		
		foreach($order->getOrderItems() as $item) {
			if($item->hasCanceled()) {
				continue;
			}

			$optnm = explode('|',$item['optnm']);
			if($item['opt1']){
				$opt1 = $optnm[0].' : '.$item['opt1'];
			}
			
			if($item['opt2']){
				$opt2 = $optnm[1].' : '.$item['opt2'];
			}
			
			if($item['addopt']){
				$addOpt = str_replace("^","]<br/>[",'['.$item['addopt'].']');
			}
			
			$query = "select model_name from ".GD_GOODS." where goodsno = ".$item['goodsno'];
			unset($model_name);
			list($model_name) = $db->fetch($query);
			
			$total_ea += $item['ea'];
			$total_price += $item['price'];
			
			$goods_discount = $item->getPercentCouponDiscount() + $item->getSpecialDiscount() + $item->getMemberDiscount();
			$total_discount += $goods_discount;
			$total_settleAmount += $item->getSettleAmount();
		?>
		<tr align='center'>
			<td  class="bottom-line"><?=++$idx?></td>
			<td  class="bottom-line" align='left' >
				<ul style="list-style:none;margin:0px;padding:5px">
					<li><font class=def><?=$item['goodsnm']?> <?if($model_name)echo ' / '.$model_name?></li>
					<?if($item['opt1']){?>
					<li><?=$opt1?></li>
					<?}?>
					
					<?if($item['opt2']){?>
					<li><?=$opt2?></li>
					<?}?>
					
					<?if($item['addopt']){?>
					<li><?=$addOpt?></li>
					<?}?>
				</ul>
			</td>
			<td class="bottom-line" ><?=number_format($item['ea'])?></td>
			<td class="bottom-line" ><?=number_format($item['price'])?>원</td>
			<td class="bottom-line" ><?=number_format($goods_discount) ?>원</td>
			<td class="bottom-line" ><?=number_format($item->getSettleAmount())?>원</td>
		</tr>
		<?}?>
		<tr height='40' align='center'>
			<td class="bottom-line" colspan='2' align='center'>합&nbsp;계</td>
			<td class="bottom-line"><?=number_format($total_ea)?></td>
			<td class="bottom-line" ><?=number_format($total_price)?>원</td>
			<td class="bottom-line" ><?=number_format($total_discount)?>원</td>
			<td class="bottom-line" ><?=number_format($total_settleAmount)?>원</td>
		</tr>
		
		<tr height='30'>
			<td class="bottom-line" colspan='6' align='right' style='font-size:13px'>
				결제총액 : <b><?=number_format($order->getAmount() - $order->getCanceledAmount())?></b>원 + 배송비 <b><?=number_format($order->getDeliveryFee())?></b>원  - 할인금액 <b><?=number_format($order->getDiscount())?></b>원
				<? if($order->getDiscount()>0){ 
				 echo '(';
				 if($order->getDiscountDetailArray(true)){
					 echo implode(' ', $order->getDiscountDetailArray(true));
				 }
				 ?>
				 
				 <?if($order['enuri']!=0){?> + 에누리 (<?=number_format($order['enuri'])?>원)<?}?> 
				 <?}?>
				 )
				 = 
				<b><?=number_format($order->getRealSettleAmount())?></b>원
			</td>
		</tr>
	</table>
</div>

 <!--결제정보-->
<div class='info-layer'>
	<div class='layer-title'>결제정보</div>
	<table width=100% cellpadding=5 cellspacing=0 border=0>
		<colgroup>
			<col width='10%'>
			<col>
			<col width='10%'>
			<col>
			<col width='10%'>
			<col>
		</colgroup>
		<tr >
			<td class='grayfont'>결제종류</td>
			<td align='center'><?=$r_settlekind[$order['settlekind']]?></td>
			<? if ($order['settlekind']=="a"){ ?>
			<td class='grayfont'>입금계좌</td>
			<td><?=$bankData['bank']?>  <?=$bankData['account']?> <?=$bankData['name']?> </td>
			<td class='grayfont'>입금자</td>
			<td><?=$order['bankSender']?></td>
			<?}
			else if($order['settlekind']=='v'){?>
				<td>가상계좌</td>
				<td><?=$order['vAccount']?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?}
			else{?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			<?}?>
		</tr>
	</table>
</div>
 
 <!--배송정보-->
<div class='info-layer'>
	<div class='layer-title'>배송정보</div>
	<table width=100% cellpadding=5 cellspacing=0 border=0>
		<colgroup>
			<col width='15%'>
			<col width='240px'>
			<col width='15%'>
			<col>
		</colgroup>
		<tr >
			<td class='grayfont'>받는사람</td>
			<td><?=$order['nameReceiver']?></td>
			<td class='grayfont'>연락처</td>
			<td><?=$order['phoneReceiver']?> &nbsp;/&nbsp;
				   <?=$order['mobileReceiver']?></td>
		</tr>
		<tr >
			<td class='grayfont'>송장번호</td>
			<td><?=$deliverycomp?> (<?=$order['deliverycode']?>)</td>
			<td class='grayfont'>배송일(출고일)</td>
			<td><?=$order['ddt']?></td>
		</tr>
		<tr >
			<td class='grayfont'>배송지주소</td>
			<td colspan='3'><?php echo $order['zonecode']; ?> <?php if(str_replace("-", "", $order['zipcode'])) echo '('.substr($order['zipcode'],0,3).' - '.substr($order['zipcode'],4).')'; ?><br/>
				<?if($order['road_address']) { ?>지번 : <? } ?><?=$order['address']?><?if($order['road_address']) { ?><br/>도로명 : <?=$order['road_address']?><? } ?>
			</td>
		</tr>
		<tr >
			<td class='grayfont'>요청사항</td>
			<td colspan='3'>&nbsp;<?=nl2br($order['memo'])?></td>
		</tr>
	</table>
</div>