<?
include "../_header.popup.php";

$ordno = $_GET['ordno'];





// �ֹ�����
$query = "select b.m_id,a.* from ".GD_ORDER." a left join ".GD_MEMBER." b on a.m_no=b.m_no where ordno='$ordno'";
$data = $db->fetch($query);

// �ù�� ����
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



<div class="title title_top">�������<span></div>

	<form name="frmDelivery" method="post" action="./indb.todayshop_buyer_delivery.php">
	<input type=hidden name=ordno value="<?=$data[ordno]?>">




	<table class=tb>
	<col class=cellC><col class=cellL>
	<?if($data[deli_title] != null){?>
	<tr>
		<td>��۹��</td>
		<td><?if($data['deli_msg'] != "���� ���� ��ۺ�"){?><?=$data['deli_title']?><?}?> <?=( $data['deli_msg'] )?$data['deli_msg']:""?></td>
	</tr>
	<?}?>
	<tr>
		<td>�����ȣ</td>
		<td>
		<? if($data['step'] >= 1 && $data['step'] < 4 && !$set['delivery']['basis']): ?>
			<select name="deliveryno">
			<option value="">==�ù��==</option>
			<? foreach((array)$_delivery as $v): ?>
				<option value="<?=$v['deliveryno']?>" <?=$_selected['deliveryno'][$v['deliveryno']]?>><?=$v['deliverycomp']?>
			<? endforeach; ?>
			</select>
			<input type='text' name='deliverycode' value="<?=$data['deliverycode']?>" class=line>
		<? else: ?>
			<? if($data['deliverycode']) : ?>
				<?=$r_delivery[$data['deliveryno']]?> <?=$data['deliverycode']?>
				<div class=small1 color=444444>�Ʒ� ��ۻ������� ��ư�� ���� Ȯ���ϼ���.</div>
			<? endif; ?>
			<input type='hidden' name='deliveryno' value='<?=$data['deliveryno']?>'>
			<input type='hidden' name='deliverycode' value='<?=$data['deliverycode']?>'>
		<? endif; ?>
		</td>
	</tr>
	<? if ($data[deliverycode] || $cntDv ){ ?>
	<tr>
		<td>�������</td>
		<td><a href="javascript:popup('popup.delivery.php?ordno=<?=$ordno?>',800,500)"><img src="../img/btn_delifind.gif" border=0></a></td>
	</tr>
	<? } ?>
	<tr>
		<td>�����(�����)</td>
		<td><font class=ver8><?=$data[ddt]?></td>
	</tr>
	<? if ($data[confirmdt]){ ?>
	<tr>
		<td>��ۿϷ���</td>
		<td><font class=ver8><?=$data[confirmdt]?>(<?=$data[confirm]?>)</td>
	</tr>
	<? } ?>
	<? if ($data[escrowyn]=="y"){ ?><!-- ����ũ�� ��� Ȯ�� -->
	<tr>
		<td>����ũ��</td>
		<td>
		<? if (!$data[escrowconfirm]){ ?><a href="javascript:escrow_confirm()">[���Ȯ�ο�û]</a>
		<? } else if ($data[escrowconfirm]==1){ ?>��ۿ�û��
		<? } else if ($data[escrowconfirm]==2){ ?>��ۿϷ�
			<?if ($cfg[settlePg] == 'inicis'){?>&nbsp;<a href="javascript:escrow_cancel()">[��ǰ���]</a><?}?>
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