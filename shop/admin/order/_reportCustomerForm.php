<?
$ordno = $_GET[ordno];
$order = Core::loader('order');
$order->load($ordno);

### �Աݰ��� ����
$query = "select * from ".GD_LIST_BANK." where  sno = ".$order['bankAccount']."   order by sno";
$bankData= $db->fetch($query);
 
### ��۾�ü ����
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
		alert("�μ��� ���� ������ �����ϼ���");
		return;
	}
	var orderPrint = window.open("_paper.php?ordno=" + ordno + "&type=" + type,"orderPrint","width=750,height=600");
	orderPrint.window.print();
}
 
</script>
 
<div style="margin-bottom:20px">
	<span style="color:#000000;font:bold 25px verdana">�ֹ�������</span>
</div>

<!--�ֹ�����-->
<div class='info-layer'>
	<div class='layer-title'>�ֹ�����</div>
	<table width=100% cellpadding=5 cellspacing=0>
		<colgroup>
			<col width='20%'>
			<col>
			<col width='20%'>
			<col>
		</colgroup>
		<tr>
			<td class='grayfont'>�ֹ���ȣ</td>
			<td><?=$ordno?></td>
			<td class='grayfont'>�ֹ���</td>
			<td><?=$order['orddt']?></td>
		</tr>
		<tr>
			<td class='grayfont'>�ֹ���(ID)</td>
			<td>
				<?=$order['nameOrder']?>
				<? if ($order['m_id']){ ?> (<?=$order['m_id']?>)
				<? } ?>
			</td>
			<td class='grayfont'>�̸���</td>
			<td><?=$order['email']?></td>
		</tr>
		<tr>
			<td class='grayfont'>��ȭ��ȣ</td>
			<td><?=$order['phoneOrder']?></td>
			<td class='grayfont'>�޴���ȭ</td>
			<td><?=$order['mobileOrder']?></td>
		</tr>
	</table>
</div>
 
<!--��ǰ����-->
<div class='info-layer'>
	<div class='layer-title'>��ǰ����</div>
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
			<td class="bottom-line">��ȣ</td>
			<td class="bottom-line" >��ǰ�� / �𵨸�</td>
			<td class="bottom-line">����</td>
			<td class="bottom-line" >�ǸŰ�</td>
			<td class="bottom-line" >���αݾ�</td>
			<td class="bottom-line" >�����ݾ�</td>
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
			<td class="bottom-line" ><?=number_format($item['price'])?>��</td>
			<td class="bottom-line" ><?=number_format($goods_discount) ?>��</td>
			<td class="bottom-line" ><?=number_format($item->getSettleAmount())?>��</td>
		</tr>
		<?}?>
		<tr height='40' align='center'>
			<td class="bottom-line" colspan='2' align='center'>��&nbsp;��</td>
			<td class="bottom-line"><?=number_format($total_ea)?></td>
			<td class="bottom-line" ><?=number_format($total_price)?>��</td>
			<td class="bottom-line" ><?=number_format($total_discount)?>��</td>
			<td class="bottom-line" ><?=number_format($total_settleAmount)?>��</td>
		</tr>
		
		<tr height='30'>
			<td class="bottom-line" colspan='6' align='right' style='font-size:13px'>
				�����Ѿ� : <b><?=number_format($order->getAmount() - $order->getCanceledAmount())?></b>�� + ��ۺ� <b><?=number_format($order->getDeliveryFee())?></b>��  - ���αݾ� <b><?=number_format($order->getDiscount())?></b>��
				<? if($order->getDiscount()>0){ 
				 echo '(';
				 if($order->getDiscountDetailArray(true)){
					 echo implode(' ', $order->getDiscountDetailArray(true));
				 }
				 ?>
				 
				 <?if($order['enuri']!=0){?> + ������ (<?=number_format($order['enuri'])?>��)<?}?> 
				 <?}?>
				 )
				 = 
				<b><?=number_format($order->getRealSettleAmount())?></b>��
			</td>
		</tr>
	</table>
</div>

 <!--��������-->
<div class='info-layer'>
	<div class='layer-title'>��������</div>
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
			<td class='grayfont'>��������</td>
			<td align='center'><?=$r_settlekind[$order['settlekind']]?></td>
			<? if ($order['settlekind']=="a"){ ?>
			<td class='grayfont'>�Աݰ���</td>
			<td><?=$bankData['bank']?>  <?=$bankData['account']?> <?=$bankData['name']?> </td>
			<td class='grayfont'>�Ա���</td>
			<td><?=$order['bankSender']?></td>
			<?}
			else if($order['settlekind']=='v'){?>
				<td>�������</td>
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
 
 <!--�������-->
<div class='info-layer'>
	<div class='layer-title'>�������</div>
	<table width=100% cellpadding=5 cellspacing=0 border=0>
		<colgroup>
			<col width='15%'>
			<col width='240px'>
			<col width='15%'>
			<col>
		</colgroup>
		<tr >
			<td class='grayfont'>�޴»��</td>
			<td><?=$order['nameReceiver']?></td>
			<td class='grayfont'>����ó</td>
			<td><?=$order['phoneReceiver']?> &nbsp;/&nbsp;
				   <?=$order['mobileReceiver']?></td>
		</tr>
		<tr >
			<td class='grayfont'>�����ȣ</td>
			<td><?=$deliverycomp?> (<?=$order['deliverycode']?>)</td>
			<td class='grayfont'>�����(�����)</td>
			<td><?=$order['ddt']?></td>
		</tr>
		<tr >
			<td class='grayfont'>������ּ�</td>
			<td colspan='3'><?php echo $order['zonecode']; ?> <?php if(str_replace("-", "", $order['zipcode'])) echo '('.substr($order['zipcode'],0,3).' - '.substr($order['zipcode'],4).')'; ?><br/>
				<?if($order['road_address']) { ?>���� : <? } ?><?=$order['address']?><?if($order['road_address']) { ?><br/>���θ� : <?=$order['road_address']?><? } ?>
			</td>
		</tr>
		<tr >
			<td class='grayfont'>��û����</td>
			<td colspan='3'>&nbsp;<?=nl2br($order['memo'])?></td>
		</tr>
	</table>
</div>