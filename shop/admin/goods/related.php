<?
$location = "상품관리 > 관련 상품 노출 설정";
include "../_header.php";

if (is_file("../../conf/config.related.goods.php")) include "../../conf/config.related.goods.php";
else {
	// 기본 설정 값
	$cfg_related['horizontal'] =  5;
	$cfg_related['vertical'] =  1;
	$cfg_related['size'] =  $cfg[img_s];

	$cfg_related['dp_image'] = 1;	// 고정
	$cfg_related['dp_goodsnm'] =  1;
	$cfg_related['dp_price'] = 1;
	$cfg_related['dp_shortdesc'] = $cfg[img_s];

	$cfg_related['use_cart'] = 0;
	$cfg_related['cart_icon'] = 1;

	$cfg_related['exclude_soldout'] =  0;
	$cfg_related['link_type'] = 'self';
}
?>
<script>
function fnSuggestImageSize(col) {
	var _width = Math.floor(640 / col) - 10;
	$('el-suggest-image-size').update(_width);

}
</script>
<div class="title title_top">관련 상품 노출 설정<span>상품 상세화면에서 보여지는 관련상품의 노출형태를 설정 및 관리하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=33')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form name="frmRelatedGoods" method="post" action="./indb.related.php" target="ifrmHidden" enctype="multipart/form-data">
<input type="hidden" name="mode" value="config">

	<table class=tb>
	<col class=cellC width="120"><col class=cellL>

	<tr>
		<td>관련상품 노출형태</td>
		<td>
			<p class="extext">
			가로 <select name="cfg_related[horizontal]" onChange="fnSuggestImageSize(this.value)"><? for ($i=1;$i<=10;$i++) {?><option value="<?=$i?>" <?=($i==$cfg_related['horizontal']) ? 'selected' : ''?>><?=$i?></option><?}?></select>개 X 세로 <select name="cfg_related[vertical]"><? for ($i=1;$i<=5;$i++) {?><option value="<?=$i?>" <?=($i==$cfg_related['vertical']) ? 'selected' : ''?>><?=$i?></option><? } ?></select>개 <br>
			상품 이미지 사이즈 <input type="text" name="cfg_related[size]" value="<?=$cfg_related['size']?>" class="line" style="width:35px;" onKeydown="onlynumber();">픽셀 (권장사이즈 : <span style="letter-spacing:0px;" id="el-suggest-image-size"></span> 픽셀) <br>
			권장 사이즈 보다 큰 사이즈 설정시에 상품이미지가 깨져 보일 수 있습니다. <br>
			</p>
		</td>
	</tr>
	<tr>
		<td>관련상품 노출값</td>
		<td class="noline">
			<label><input type="checkbox" name="cfg_related[dp_image]" value="1" checked disabled>이미지</label>
			<label><input type="checkbox" name="cfg_related[dp_goodsnm]" value="1" <?=$cfg_related['dp_goodsnm'] ? 'checked' : ''?>>상품명</label>
			<label><input type="checkbox" name="cfg_related[dp_price]" value="1" <?=$cfg_related['dp_price'] ? 'checked' : ''?>>가격</label>
			<label><input type="checkbox" name="cfg_related[dp_shortdesc]" value="1" <?=$cfg_related['dp_shortdesc'] ? 'checked' : ''?>>짧은설명</label>
		</td>
	</tr>
	<tr>
		<td>장바구니 담기</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[use_cart]" value="1" <?=$cfg_related['use_cart'] == 1 ? 'checked' : ''?>>사용함</label>
			<label><input type="radio" name="cfg_related[use_cart]" value="0" <?=$cfg_related['use_cart'] != 1 ? 'checked' : ''?>>사용안함</label>

			<fieldset id="related-goods-cart-icon" style="margin-top:10px;"><legend>아이콘</legend>

				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// 기본 제공 아이콘
				for ($i=1;$i<=5;$i++) {
				?>
				<td align="center">
					<img src="../../data/goods/icon/icon_basket<?=$i?>.gif"><br>
					<input type="radio" name="cfg_related[cart_icon]" value="<?=$i?>" <?=$cfg_related['cart_icon'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>

				<td align="center">
					<img src="../../data/goods/icon/custom/basket" onerror="this.src='../img/img_basket.gif';" id="el-user-cart-icon"><br>
					<input type="radio" name="cfg_related[cart_icon]" value="custom" <?=$cfg_related['cart_icon'] == 'custom' ? 'checked' : ''?>>
				</td>
				</table>
				사용자 아이콘 : <input type="file" name="cart_image" value="">
			</fieldset>


		</td>
	</tr>
	<tr>
		<td>품절상품 제외 설정</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[exclude_soldout]" value="0" <?=$cfg_related['exclude_soldout'] != 1 ? 'checked' : ''?>>품절된 상품 노출</label>
			<label><input type="radio" name="cfg_related[exclude_soldout]" value="1" <?=$cfg_related['exclude_soldout'] == 1 ? 'checked' : ''?>>상품 품절시 자동으로 관련상품에서 제외</label>
		</td>
	</tr>
	<tr>
		<td>관련상품 연결</td>
		<td class="noline">
			<label><input type="radio" name="cfg_related[link_type]" value="self"  <?=$cfg_related['link_type'] != 'blank' ? 'checked' : ''?>>현재창에서 관련상품 상세 페이지 연결</label>
			<label><input type="radio" name="cfg_related[link_type]" value="blank" <?=$cfg_related['link_type'] == 'blank' ? 'checked' : ''?>>새창으로 관련상품 상세 페이지 연결</label>
		</td>
	</tr>
	</table>




	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>

</form>



<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관련상품 등록은 상품등록 및 상품리스트에 수정 페이지에서 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관련상품 등록시 노출방식을 ‘자동’으로 선택 하셨을 경우에는 5개의 상품이 랜덤으로 노출되며, 노출형태에 설정된 상품 개수가 적용되지 않습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관련상품 노출내용을 선택할 수 있습니다. 짧은설명의 경우 상품이미지에 마우스 오버시 말풍선으로 보여집니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">장바구니 담기 기능을 설정할 수 있습니다. (사용자 아이콘을 등록하여 기본아이콘을 대체할 수 있습니다.) </td></tr>

<tr><td><img src="../img/icon_list.gif" align="absmiddle">품절상품 제외 설정이 가능합니다. 상품 품절시 자동으로 관련상품에서 제외 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">관련상품 이미지 클릭시 현재페이지에서 연결 또는 새창으로 페이지 연결 설정이 가능합니다.</td></tr>
</table>
</div>
<script>
cssRound('MSG01');
fnSuggestImageSize(<?=$cfg_related['horizontal']?>);
</script>

<? include "../_footer.php"; ?>