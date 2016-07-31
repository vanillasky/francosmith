<?php

$location = "모바일샵관리 > 모바일샵 분류페이지 상품 진열";
include "../_header.php";
include "../../conf/config.mobileShop.php";
include "../../conf/config.mobileShop.category.php";

$goodsDisplay = Core::loader('Mobile2GoodsDisplay');

if(!$cfgMobileDispCategory['disp_goods_count']) $cfgMobileDispCategory['disp_goods_count'] = 10;
$selected['disp_goods_count'][$cfgMobileDispCategory['disp_goods_count']] = 'selected="selected"';

{ // 출력 카운트 정의

	$goods_count = array(
		10,20,30,50,100
	);
	sort ( $goods_count );

}
?>

<style type="text/css">
a.extext:hover{
	color: #000000;
}
</style>

<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="disp_category_set">

<div class="title title_top">모바일샵 분류페이지 상품 진열</div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류페이지<br/>상품출력 수</td>
	<td class="noline">
		<div>
			<select name="disp_goods_count">
			<?php foreach($goods_count as $count){?>
			<option value="<?php echo $count;?>" <?=$selected['disp_goods_count'][$count]?>><?php echo $count;?>개</option>
			<?php }?>
			</select>
<!--			<label for="vtype-main-pc">온라인 쇼핑몰(PC버전)과 동일하게 메인 상품진열 적용</label>-->
			<br/>
			<span class="extext">* 분류페이지로 이동 시, 분류페이지에서 더보기 버튼 클릭 시 불러올 상품 개수를 설정합니다.<br>너무 많은 상품을 한번에 불러올 경우 페이지 로딩 속도가 느려질 수 있습니다.</span>
		</div>
	</td>
</tr>
</table>
<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>

<? include "../_footer.php"; ?>