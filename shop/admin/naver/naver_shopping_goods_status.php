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

// 카테고리명 조회
$query = "select catnm,category from ".GD_CATEGORY." where length(category)='3' and hidden='0'";
$res = $db->query($query);
while($data = $db->fetch($res,1)){
	$category[] = $data;
}

// 카테고리별 노출 상품수
for ($i=0; $i<count($category); $i++) {
	$category[$i]['exposeCount'] = $naver->statusCategoryGoodsCount($category[$i]['category'],'Y');
}

// 카테고리별 상품수
for ($i=0; $i<count($category); $i++) {
	$category[$i]['goodsCount'] = $naver->statusCategoryGoodsCount($category[$i]['category'],'');
}

// 전체 노출 상품 수
$exposeCount = $naver->statusGoodsCount('Y');

// 전체 카테고리 상품 수
$goodsCount = $naver->statusGoodsCount('');
?>
<script>nsGodoLoadingIndicator.hide();</script>
<table>
<tr>
<td class="title title_top">네이버 쇼핑 상품 노출수 현황<span> 네이버 쇼핑에 노출되는 상품수를 카테고리별로 확인할 수 있습니다.</span></td>
<td align="right" style="padding-left:140px;"><a href="javascript:window.location.reload(true);"><img src="../img/btn_naver_shopping_refresh.png"></a></td>
</tr>
</table>

<table style="width:765px;">
<tr>
	<td>
		<div style="overflow-y: hidden; overflow-x: hidden;">
			<table class=tb style="width:782px;">
			<tr>
				<td style="background:#f6f6f6"><b>카테고리명</b> (1차 카테고리)</td>
				<td style="width:130px; background:#f6f6f6"><b>노출 상품수</b></td>
				<td style="width:147px; background:#f6f6f6"><b>카테고리 상품수</b></td>
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
				<td>합계</td>
				<td style="width:130px;">
				<?if ($exposeCount > 499000) {?>
				<font color="red"><?=number_format($exposeCount)?></font>
				<?}else{?>
				<?=number_format($exposeCount)?>
				<?}?>
				<br>(중복된 상품수 : <?=number_format($exposeAllCount-$exposeCount)?>)</td>

				<td style="width:147px;">
				<?if ($goodsCount > 499000) {?>
				<font color="red"><?=number_format($goodsCount)?></font>
				<?}else{?>
				<?=number_format($goodsCount)?>
				<?}?>
				<br>(중복된 상품수 : <?=number_format($goodsAllCount-$goodsCount)?>)</td>
			</tr>
			</table>
			<div class="extext" style="margin-top:10px; margin-bottom:10px;">노출 상품수 및 카테고리 상품수는 품절상품과 미진열상품을 제회한 상품수입니다.<br>선택한 카테고리의 상품이 499,000개 초과 시 최근 상품 등록일자순으로 499,000개 이하로 노출 상품이 조정됩니다.</div>
		</div>
	</td>
</tr>
</table>
<? include '../footer.popup.php'; ?>