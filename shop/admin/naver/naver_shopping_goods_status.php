<?
include "../_header.popup.php";
include "../../lib/naverPartner.class.php";
?>
<script type="text/javascript" src="../godo.loading.indicator.js"></script>
<script>
nsGodoLoadingIndicator.init({});
nsGodoLoadingIndicator.show();
</script>
<?
flush();
$naver = new naverPartner();
$category = array();
$categoryExposeCount = 0;
$categoryGoodsCount = 0;

// ī�װ��� ��ȸ
$query = "select catnm,category from ".GD_CATEGORY." where length(category)='3' and hidden='0'";
$res = $db->query($query);
while($data = $db->fetch($res,1)){
	$category[] = $data;
}

// ī�װ��� ���� ��ǰ��
for ($i=0; $i<count($category); $i++) {
	$category[$i]['exposeCount'] = $naver->statusCategoryGoodsCount($category[$i]['category'],'Y');
}

// ī�װ��� ��ǰ��
for ($i=0; $i<count($category); $i++) {
	$category[$i]['goodsCount'] = $naver->statusCategoryGoodsCount($category[$i]['category'],'');
}

// ��ü ���� ��ǰ ��
$exposeCount = $naver->statusGoodsCount('Y');

// ��ü ī�װ� ��ǰ ��
$goodsCount = $naver->statusGoodsCount('');
?>
<script>nsGodoLoadingIndicator.hide();</script>
<table>
<tr>
<td class="title title_top">���̹� ���� ��ǰ ����� ��Ȳ<span> ���̹� ���ο� ����Ǵ� ��ǰ���� ī�װ����� Ȯ���� �� �ֽ��ϴ�.</span></td>
<td align="right" style="padding-left:140px;"><a href="javascript:window.location.reload(true);"><img src="../img/btn_naver_shopping_refresh.png"></a></td>
</tr>
</table>

<table style="width:765px;">
<tr>
	<td>
		<div style="overflow-y: hidden; overflow-x: hidden;">
			<table class=tb style="width:782px;">
			<tr>
				<td style="background:#f6f6f6"><b>ī�װ���</b> (1�� ī�װ�)</td>
				<td style="width:130px; background:#f6f6f6"><b>���� ��ǰ��</b></td>
				<td style="width:147px; background:#f6f6f6"><b>ī�װ� ��ǰ��</b></td>
			</tr>
			</table>
		</div>
		<div style="height: 600px; overflow-y: scroll; overflow-x: hidden;">
			<table class=tb style="width:765px;">
				<?for ($i=0; $i<count($category); $i++) {?>
				<tr>
					<td><?=$category[$i]['catnm']?></td>
					<td style="width:130px"><?=number_format($category[$i]['exposeCount'])?></td>
					<td style="width:130px"><?=number_format($category[$i]['goodsCount'])?></td>
				</tr>
				<?
				$exposeAllCount += $category[$i]['exposeCount'];
				$goodsAllCount += $category[$i]['goodsCount'];
				}?>
			</table>
		</div>
		<div style="position:absolute; bottom:0; background:white; height:115px;">
			<div style="width:100%; border-top:1px solid; margin-bottom:10px;"></div>
			<table class=tb style="width:782px;">
			<tr>
				<td>�հ�</td>
				<td style="width:130px;">
				<?if ($exposeCount > 499000) {?>
				<font color="red"><?=number_format($exposeCount)?></font>
				<?}else{?>
				<?=number_format($exposeCount)?>
				<?}?>
				<br>(�ߺ��� ��ǰ�� : <?=number_format($exposeAllCount-$exposeCount)?>)</td>

				<td style="width:147px;">
				<?if ($goodsCount > 499000) {?>
				<font color="red"><?=number_format($goodsCount)?></font>
				<?}else{?>
				<?=number_format($goodsCount)?>
				<?}?>
				<br>(�ߺ��� ��ǰ�� : <?=number_format($goodsAllCount-$goodsCount)?>)</td>
			</tr>
			</table>
			<div class="extext" style="margin-top:10px; margin-bottom:10px;">���� ��ǰ�� �� ī�װ� ��ǰ���� ǰ����ǰ�� ��������ǰ�� ��ȸ�� ��ǰ���Դϴ�.<br>������ ī�װ��� ��ǰ�� 499,000�� �ʰ� �� �ֱ� ��ǰ ������ڼ����� 499,000�� ���Ϸ� ���� ��ǰ�� �����˴ϴ�.</div>
		</div>
	</td>
</tr>
</table>
<? include '../footer.popup.php'; ?>