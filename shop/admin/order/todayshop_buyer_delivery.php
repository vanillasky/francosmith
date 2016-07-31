<?
include "../_header.popup.php";

$ordno = $_GET['ordno'];





// 주문정보
$query = "select b.m_id,a.* from ".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no where ordno='$ordno'";
$data = $db->fetch($query);

// 택배사 정보
$query = "select * from ".GD_LIST_DELIVERY." where useyn='y' order by deliverycomp";
$res = $db->query($query);
while ($row=$db->fetch($res)){
	$_delivery[] = $row;
	$r_delivery[$row[deliveryno]] = $row[deliverycomp];
}

if(!$data[deliveryno] && $_delivery[0][deliveryno]) $data[deliveryno] = $_delivery[0][deliveryno];
$_selected[deliveryno][$data[deliveryno]] = "selected";


?>

<script type="text/javascript" src="../todayshop/todayshop.js"></script>



<div class="title title_top">배송정보<span></div>

	<form name="frmDelivery" method="post" action="./indb.todayshop_buyer_delivery.php">
	<input type=hidden name=ordno value="<?=$data[ordno]?>">




	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($data[deli_title] != null){?>
	<tr>
		<td>배송방법</td>
		<td><?if($data['deli_msg'] != "개별 착불 배송비"){?><?=$data['deli_title']?><?}?> <?=( $data['deli_msg'] )?$data['deli_msg']:""?></td>
	</tr>
	<?}?>
	<tr>
		<td>송장번호</td>
		<td>
		<? if($data['step'] >= 1 && $data['step'] < 4 && !$set['delivery']['basis']): ?>
			<select name="deliveryno">
			<option value="">==택배사==</option>
			<? foreach((array)$_delivery as $v): ?>
				<option value="<?=$v['deliveryno']?>" <?=$_selected['deliveryno'][$v['deliveryno']]?>><?=$v['deliverycomp']?>
			<? endforeach; ?>
			</select>
			<input type='text' name='deliverycode' value="<?=$data['deliverycode']?>" class=line>
		<? else: ?>
			<? if($data['deliverycode']) : ?>
				<?=$r_delivery[$data['deliveryno']]?> <?=$data['deliverycode']?>
				<div class=small1 color=444444>아래 배송상태추적 버튼을 눌러 확인하세요.</div>
			<? endif; ?>
			<input type='hidden' name='deliveryno' value='<?=$data['deliveryno']?>'>
			<input type='hidden' name='deliverycode' value='<?=$data['deliverycode']?>'>
		<? endif; ?>
		</td>
	</tr>
	<? if ($data[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>배송추적</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>배송일(출고일)</td>
		<td><font class=ver8><?=$data[ddt]?></td>
	</tr>
	<? if ($data[confirmdt]){ ?>
	<tr>
		<td>배송완료일</td>
		<td><font class=ver8><?=$data[confirmdt]?>(<?=$data[confirm]?>)</td>
	</tr>
	<? } ?>
	<? if ($data[escrowyn]=="y"){ ?><!-- 에스크로 배송 확인 -->
	<tr>
		<td>에스크로</td>
		<td>
		<? if (!$data[escrowconfirm]){ ?><a href="javascript:escrow_confirm()">[배송확인요청]</a>
		<? } else if ($data[escrowconfirm]==1){ ?>배송요청중
		<? } else if ($data[escrowconfirm]==2){ ?>배송완료
			<?if ($cfg[settlePg] == 'inicis'){?>&nbsp;<a href="javascript:escrow_cancel()">[반품등록]</a><?}?>
		<?}?>
		</td>
	</tr>
	<? } ?>
	</table>


<p align="center">

<input type="image" src="../img/btn_delinum_confirm.gif" border="0" style="border:none;">
<img src="../img/btn_delinum_close.gif" class="hand" onClick="parent.closeLayer();">
</p>


	</form>