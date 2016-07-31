<?
$location = "상품관리 > 품절상품 진열 설정";
include "../_header.php";

if (is_file("../../conf/config.soldout.php")) include "../../conf/config.soldout.php";
else {
	// 기본 설정 값
	$cfg_soldout['exclude_main'] =  0;
	$cfg_soldout['back_main'] = 0;
	$cfg_soldout['exclude_category'] =  0;
	$cfg_soldout['back_category'] = 0;
	$cfg_soldout['exclude_search'] =  0;
	$cfg_soldout['back_search'] = 0;

	$cfg_soldout['display'] = 'icon';
	$cfg_soldout['mobile_display'] = 'none';
	$cfg_soldout['price'] = 'price';
	$cfg_soldout['display_overlay'] = 1;
	$cfg_soldout['mobile_display_overlay'] = 1;
	$cfg_soldout['display_icon'] = 1;
}
?>
<style>
p.soldout {margin:3px 0 0 0;}
</style>

<div class="title title_top">품절상품 진열설정<span> 진열 페이지별로 품절상품 노출 및 진열을 설정하실 수 있습니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<form name="frmRelatedGoods" method="post" action="./indb.soldout.php" target="ifrmHidden" enctype="multipart/form-data">
<input type="hidden" name="mode" value="config">

	<div style="padding:10px 0px 5px 0px;font-weight:bold;">메인페이지 품절상품 진열설정</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 상품 노출</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_main]" value="0" <?=$cfg_soldout['exclude_main'] != '1' ? 'checked' : ''?>>품절상품 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_main]" value="1" <?=$cfg_soldout['exclude_main'] == '1' ? 'checked' : ''?>>품절상품 보여주지 않기</label>
		</td>
	</tr>
	<tr>
		<td>품절상품 진열상태</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_main]" value="0" <?=$cfg_soldout['back_main'] != '1' ? 'checked' : ''?>>정렬 순서대로 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_main]" value="1" <?=$cfg_soldout['back_main'] == '1' ? 'checked' : ''?>>리스트 끝으로 보내기</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">분류페이지 품절상품 진열 설정</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 상품 노출</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_category]" value="0" <?=$cfg_soldout['exclude_category'] != '1' ? 'checked' : ''?>>품절상품 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_category]" value="1" <?=$cfg_soldout['exclude_category'] == '1' ? 'checked' : ''?>>품절상품 보여주지 않기</label>
		</td>
	</tr>
	<tr>
		<td>품절상품 진열상태</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_category]" value="0" <?=$cfg_soldout['back_category'] != '1' ? 'checked' : ''?>>정렬 순서대로 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_category]" value="1" <?=$cfg_soldout['back_category'] == '1' ? 'checked' : ''?>>리스트 끝으로 보내기</label>
		</td>
	</tr>
	</table>



	<div style="padding:10px 0px 5px 0px;font-weight:bold;">검색페이지 품절상품 진열 설정</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 상품 노출</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_search]" value="0" <?=$cfg_soldout['exclude_search'] != '1' ? 'checked' : ''?>>품절상품 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_search]" value="1" <?=$cfg_soldout['exclude_search'] == '1' ? 'checked' : ''?>>품절상품 보여주지 않기</label>
		</td>
	</tr>
	<tr>
		<td>품절상품 진열상태</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_search]" value="0" <?=$cfg_soldout['back_search'] != '1' ? 'checked' : ''?>>정렬 순서대로 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_search]" value="1" <?=$cfg_soldout['back_search'] == '1' ? 'checked' : ''?>>리스트 끝으로 보내기</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">브랜드페이지 품절상품 진열 설정</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 상품 노출</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_brand]" value="0" <?=$cfg_soldout['exclude_brand'] != '1' ? 'checked' : ''?>>품절상품 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_brand]" value="1" <?=$cfg_soldout['exclude_brand'] == '1' ? 'checked' : ''?>>품절상품 보여주지 않기</label>
		</td>
	</tr>
	<tr>
		<td>품절상품 진열상태</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_brand]" value="0" <?=$cfg_soldout['back_brand'] != '1' ? 'checked' : ''?>>정렬 순서대로 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_brand]" value="1" <?=$cfg_soldout['back_brand'] == '1' ? 'checked' : ''?>>리스트 끝으로 보내기</label>
		</td>
	</tr>
	</table>


	<div style="padding:10px 0px 5px 0px;font-weight:bold;">이벤트페이지 품절상품 진열 설정</div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 상품 노출</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_event]" value="0" <?=$cfg_soldout['exclude_event'] != '1' ? 'checked' : ''?>>품절상품 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[exclude_event]" value="1" <?=$cfg_soldout['exclude_event'] == '1' ? 'checked' : ''?>>품절상품 보여주지 않기</label>
		</td>
	</tr>
	<tr>
		<td>품절상품 진열상태</td>
		<td>
			<label class="noline"><input type="radio" name="cfg_soldout[back_event]" value="0" <?=$cfg_soldout['back_event'] != '1' ? 'checked' : ''?>>정렬 순서대로 보여주기</label>
			<label class="noline"><input type="radio" name="cfg_soldout[back_event]" value="1" <?=$cfg_soldout['back_event'] == '1' ? 'checked' : ''?>>리스트 끝으로 보내기</label>
		</td>
	</tr>
	</table>

	<div class="title title_top">PC 품절상품 표시 설정<span>PC버전 쇼핑몰 진열페이지에 품절표시를 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 표시 설정</td>
		<td>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="overlay" <?=$cfg_soldout['display'] == 'overlay' ? 'checked' : ''?>>상품 이미지 오버레이</label></legend>
				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// 기본 제공 아이콘
				for ($i=1;$i<=5;$i++) {
				?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/icon_soldout<?=$i?>) no-repeat center center;border:1px solid #CCCCCC;"></div>
					<input type="radio" name="cfg_soldout[display_overlay]" value="<?=$i?>" <?=$cfg_soldout['display_overlay'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/custom/soldout_overlay) no-repeat center center;border:1px solid #CCCCCC;" id="el-user-soldout-overlay"></div>
					<input type="radio" name="cfg_soldout[display_overlay]" value="custom" <?=$cfg_soldout['display_overlay'] == 'custom' ? 'checked' : ''?>>
				</td>
				</table>

				<div style="padding-top:3px">사용자 이미지 : <input type="file" name="soldout_overlay" value=""> <span class="extext">(권장 사이즈 : <?=$cfg[img_m]?>px)</span></div>

				<p class="soldout extext">
				사용자 이미지 업로드시 반드시 배경이 투명처리된 png, gif 파일로 업로드 하셔야 합니다.
				</p>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="icon" <?=$cfg_soldout['display'] == 'icon' ? 'checked' : ''?>>품절 아이콘 표시</label></legend>
				<div style="padding-top:3px"><label class="noline"><input type="radio" name="cfg_soldout[display_icon]" value="1" <?=$cfg_soldout['display_icon'] != 'custom' ? 'checked' : ''?>>기본 아이콘</label> : <img src="../../data/skin/<?=$cfg[tplSkin]?>/img/icon/good_icon_soldout.gif" align="absmiddle"></div>
				<div style="padding-top:3px"><label class="noline"><input type="radio" name="cfg_soldout[display_icon]" value="custom" <?=$cfg_soldout['display_icon'] == 'custom' ? 'checked' : ''?>>대체 아이콘</label> : <img src="../../data/goods/icon/custom/soldout_icon" onerror="this.src='../img/img_basket.gif';" id="el-user-soldout-icon"> <input type="file" name="soldout_icon" value=""></div>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[display]" value="none" <?=$cfg_soldout['display'] == 'none' ? 'checked' : ''?>>표시 안함</label></legend>
				<p class="soldout extext">
				아이콘, 오버레이 이미지 모두 표시하지 않습니다.
				</p>
			</fieldset>

		</td>
	</tr>
	</table>

	<div class="title title_top">모바일샵 품절상품 표시 설정 <span>모바일샵 진열페이지에 품절표시를 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>품절 표시 설정</td>
		<td>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[mobile_display]" value="overlay" <?=$cfg_soldout['mobile_display'] === 'overlay' ? 'checked' : ''?>>상품 이미지 오버레이</label></legend>
				<table border="0" style="" cellpadding="5">
				<tr>
				<?
				// 기본 제공 아이콘
				for ($i=1;$i<=1;$i++) {
				?>
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/mobile_icon_soldout<?=$i?>) no-repeat center center; background-size: cover; border:1px solid #CCCCCC;"></div>
					<input type="radio" name="cfg_soldout[mobile_display_overlay]" value="<?=$i?>" <?=$cfg_soldout['mobile_display_overlay'] == $i ? 'checked' : ''?>>
				</td>
				<? } ?>
				
				<td align="center" class="noline">
					<div style="width:130px;height:130px;background:url(../../data/goods/icon/mobile_custom_soldout) no-repeat center center;border:1px solid #CCCCCC;" id="mobile-el-user-soldout-overlay"></div>
					<input type="radio" name="cfg_soldout[mobile_display_overlay]" value="custom" <?=$cfg_soldout['mobile_display_overlay'] === 'custom' ? 'checked' : ''?>>
				</td>
				</table>

				<div style="padding-top:3px">사용자 이미지 : <input type="file" name="mobile_custom_soldout" value=""> <span class="extext">(권장 사이즈 : <?=$cfg[img_m]?>px)</span></div>

				<p class="soldout extext">
				사용자 이미지 업로드시 반드시 배경이 투명처리된 png, gif 파일로 업로드 하셔야 합니다.
				</p>
			</fieldset>

			<input type="radio" name="cfg_soldout[mobile_display]" value="none" <?=$cfg_soldout['mobile_display'] === 'none' ? 'checked' : ''?>>표시 안함

		</td>
	</tr>
	</table>

	<div class="title title_top">품절상품 노출값 설정<span>모든 진열페이지에 품절상품 노출값을 설정합니다.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=37')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
	<table class=tb style="margin-bottom:20px;">
	<col class=cellC width="120"><col class=cellL>
	<tr>
		<td>상품명</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[goodsnm]" value="1" <?=$cfg_soldout[goodsnm] != '0' ? 'checked' : '' ?>>노출</label>
			<label><input type="radio" name="cfg_soldout[goodsnm]" value="0" <?=$cfg_soldout[goodsnm] == '0' ? 'checked' : '' ?>>노출안함</label>
		</td>
	</tr>
	<tr>
		<td>상품가격
		<p style="margin:5px 0 0 0;" class="extext">
		상품 상세 페이지에도 함께 적용됩니다.
		</p>
		</td>
		<td>
			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="price" <?=$cfg_soldout['price'] == 'price' ? 'checked' : ''?>>가격 표시</label></legend>
				<span class="extext">상품의 가격을 표시함.</span>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="string" <?=$cfg_soldout['price'] == 'string' ? 'checked' : ''?>>가격 대체 문구</label></legend>
				<input type="text" name="cfg_soldout[price_string]" value="<?=$cfg_soldout[price_string]?>" class="line"> <span class="extext">품절시 상품 가격을 대체할 텍스트 입력</span>
			</fieldset>

			<fieldset class="soldout"><legend><label class="noline"><input type="radio" name="cfg_soldout[price]" value="image" <?=$cfg_soldout['price'] == 'image' ? 'checked' : ''?>>이미지 출력</label></legend>
				<img src="../../data/goods/icon/custom/soldout_price" onerror="this.src='../img/img_basket.gif';" id="el-user-soldout-price"><br>
				<input type="file" name="soldout_price" value=""> <span class="extext">품절시 상품 가격을 대체할 이미지 업로드</span>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td>짧은설명</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[shortdesc]" value="1" <?=$cfg_soldout[shortdesc] != '0' ? 'checked' : '' ?>>노출</label>
			<label><input type="radio" name="cfg_soldout[shortdesc]" value="0" <?=$cfg_soldout[shortdesc] == '0' ? 'checked' : '' ?>>노출안함</label>
		</td>
	</tr>
	<tr>
		<td>쿠폰할인</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[coupon]" value="1" <?=$cfg_soldout[coupon] != '0' ? 'checked' : '' ?>>노출</label>
			<label><input type="radio" name="cfg_soldout[coupon]" value="0" <?=$cfg_soldout[coupon] == '0' ? 'checked' : '' ?>>노출안함</label>
		</td>
	</tr>
	<tr>
		<td>상품아이콘</td>
		<td class="noline">
			<label><input type="radio" name="cfg_soldout[icon]" value="1" <?=$cfg_soldout[icon] != '0' ? 'checked' : '' ?>>노출</label>
			<label><input type="radio" name="cfg_soldout[icon]" value="0" <?=$cfg_soldout[icon] == '0' ? 'checked' : '' ?>>노출안함</label>
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
<tr><td>- 품절표시 설정</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">상품 이미지 오버레이 : 상품이미지(썸네일) 가운데에 오버레이 이미지가 덮혀 표시 됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(※ 사용자 이미지 업로드시 반드시 배경이 투명처리된 png,gif 파일로 업로드 하셔야 합니다.)</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">품절 아이콘 표시: [찾아보기]로 품절 아이콘 대신 다른 이미지로 아이콘 대체가 가능합니다.</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;대체 아이콘이 없을 경우 "품절" 아이콘으로 표시됩니다.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>- 상품가격 노출설정</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">가격 대체문구를 사용하여 가격 표시를 설정하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">이미지 출력을 선택하여 이미지로 품절상품 가격을 출력 하실 수 있습니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">(※상품 상세페이지에도 함께 적용됩니다.)</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>