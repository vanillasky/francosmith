<?php
$location = "상품관리 > 해시태그 관련 설정";
include '../_header.php';

$hashtag = Core::loader('hashtag');
$hashtagConfig = $hashtag->getConfig();

if(!$hashtagConfig['hashtag_main_use']) $hashtagConfig['hashtag_main_use'] = 'y';
if(!$hashtagConfig['hashtag_main_display_count']) $hashtagConfig['hashtag_main_display_count'] = '10';
if(!$hashtagConfig['hashtag_main_order_by']) $hashtagConfig['hashtag_main_order_by'] = 'goodsCount';
if(!$hashtagConfig['hashtag_goodsView_use']) $hashtagConfig['hashtag_goodsView_use'] = 'y';
if(!$hashtagConfig['hashtag_goodsView_order_by']) $hashtagConfig['hashtag_goodsView_order_by'] = 'goodsCount';
if(!$hashtagConfig['hashtag_goodsView_user_write']) $hashtagConfig['hashtag_goodsView_user_write'] = 'y';
if(!$hashtagConfig['hashtag_goodsList_use']) $hashtagConfig['hashtag_goodsList_use'] = 'y';
if(!$hashtagConfig['hashtag_goodsList_display_count']) $hashtagConfig['hashtag_goodsList_display_count'] = '2';
if(!$hashtagConfig['hashtag_goodsList_order_by']) $hashtagConfig['hashtag_goodsList_order_by'] = 'goodsCount';

$checked['hashtag_main_use'][$hashtagConfig['hashtag_main_use']] = "checked='checked'";
$checked['hashtag_main_order_by'][$hashtagConfig['hashtag_main_order_by']] = "checked='checked'";
$checked['hashtag_goodsView_use'][$hashtagConfig['hashtag_goodsView_use']] = "checked='checked'";
$checked['hashtag_goodsView_order_by'][$hashtagConfig['hashtag_goodsView_order_by']] = "checked='checked'";
$checked['hashtag_goodsView_user_write'][$hashtagConfig['hashtag_goodsView_user_write']] = "checked='checked'";
$checked['hashtag_goodsList_use'][$hashtagConfig['hashtag_goodsList_use']] = "checked='checked'";
$checked['hashtag_goodsList_order_by'][$hashtagConfig['hashtag_goodsList_order_by']] = "checked='checked'";
$selected['hashtag_main_display_count'][$hashtagConfig['hashtag_main_display_count']] = "selected='selected'";
$selected['hashtag_goodsList_display_count'][$hashtagConfig['hashtag_goodsList_display_count']] = "selected='selected'";
?>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<script type="text/javascript" src="../godo_ui.js?ts=<?php echo date('Ym'); ?>"></script>
<style>
div.tooltip {width:720px;padding:0;margin:0;}
.hashtagConfig-layout { width: 1000px; }
.hashtagConfig-layout .hashtagConfig-marginTop5 { margin-top: 5px; }
.hashtagConfig-layout .hashtag-skinPatchInfo { border: 1px solid #cccccc; height: 30px; line-height:30px; margin-bottom: 10px; width: 99%; padding: 3px; color: red; font-weight: bold;}
.hashtagConfig-layout .hashtagConfig-save-button { width: 100%; margin-top: 10px; text-align: center; }
.hashtagConfig-layout .hashtagConfig-buttonLayout { margin-top: 30px; text-align: center; }

.hashtagConfig-layout .hashtagConfig-default-layout {}
.hashtagConfig-layout .hashtagConfig-main-layout,
.hashtagConfig-layout .hashtagConfig-goodsView-layout,
.hashtagConfig-layout .hashtagConfig-goodsList-layout,
.hashtagConfig-layout .hashtagConfig-replaceCode-layout { margin-top: 50px; }
.hashtagConfig-layout .hashtagConfig-fontLink { font-weight: bold; color: #627dce; cursor: pointer; }
</style>

<div class="hashtagConfig-layout">
	<!-- 기본 해시태그 설정 -->
	<div class="hashtagConfig-default-layout">
		<div class="title title_top">
			기본 해시태그 설정
			<span>상품에 등록된 특정 항목을 일괄적으로 해시태그로 등록할 수 있습니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>
		
		<div class="hashtag-skinPatchInfo">
			※2016년 10월 06일 이전 제작 스킨을 사용하시는 경우 반드시 스킨패치를 적용해야 기능 사용이 가능합니다.
			<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2634" class="extext" style="font-weight:bold" target="_blank"> [패치 바로가기]</a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>기본 해시태그 설정</td>
				<td>
					<div>
						<input type="checkbox" name="brand" value="y" /> 브랜드명
						&nbsp;
						<input type="checkbox" name="keyword" value="y" /> 유사검색어
						&nbsp;
						<input type="checkbox" name="category" value="y" /> 상품카테고리 (등록된 최하위 카테고리로 추가됨)
					</div>
					<div class="extext hashtagConfig-marginTop5">체크 후 [체크된 항목을 해시태그로 추가하기]버튼 클릭 시 위의 항목으로 현재 등록된 상품에 해시태그가 추가됩니다.</div>
				</td>
			</tr>
			</tbody>
		</table>

		<div class="hashtagConfig-save-button"><img src="../img/btn_hashtag_migration.gif" id="hashtagMigragionBtn" class="hand" /></div>
	</div>
	<!-- 기본 해시태그 설정 -->

	<form name="hashtagConfigForm" id="hashtagConfigForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
	<input type="hidden" name="mode" id="mode" value="" />

	<!-- 메인 페이지 해시태그 노출 설정 -->
	<div class="hashtagConfig-main-layout">
		<div class="title title_top">
			메인 페이지 해시태그 노출 설정
			<span>메인 페이지에 쇼핑몰에 등록된 해시태그 노출 설정을 할 수 있습니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>노출 설정</td>
				<td>
					<input type="radio" name="hashtag_main_use" value="y" <?php echo $checked['hashtag_main_use']['y']; ?> /> 노출함
					&nbsp;
					<input type="radio" name="hashtag_main_use" value="n" <?php echo $checked['hashtag_main_use']['n']; ?> /> 노출안함
					&nbsp;
					<span class="extext">메인 페이지에 쇼핑몰에 등록된 해시태그 리스트를 노출합니다.</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image1.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>노출 개수 설정</td>
				<td>
					<select name="hashtag_main_display_count">
						<?php for($i=3; $i<=30; $i++){ ?>
						<option value="<?php echo $i; ?>" <?php echo $selected['hashtag_main_display_count'][$i]; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					<span class="extext">노출될 해시태그의 개수를 설정합니다.</span>
				</td>
			</tr>
			<tr>
				<td>노출 기준설정</td>
				<td>
					<div>
						<input type="radio" name="hashtag_main_order_by" value="goodsCount" <?php echo $checked['hashtag_main_order_by']['goodsCount']; ?> /> 상품등록수순
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="newRegister" <?php echo $checked['hashtag_main_order_by']['newRegister']; ?> /> 최근등록순
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="name" <?php echo $checked['hashtag_main_order_by']['name']; ?> /> ㄱㄴㄷ순
						&nbsp;
						<input type="radio" name="hashtag_main_order_by" value="user" <?php echo $checked['hashtag_main_order_by']['user']; ?> /> 사용자설정
						&nbsp;&nbsp;
						<span class="hashtagConfig-fontLink hashtagDisplayPopupBtn">[설정하기▶]</span>
					</div>
					<div class="extext hashtagConfig-marginTop5">메인 페이지에 해시태그 노출 시 노출 기준을 설정합니다. 사용자설정은 [설정하기▶] 에서 설정된 순서를 따릅니다.</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- 메인 페이지 해시태그 노출 설정 -->

	<!-- 상품 상세 페이지 해시태그 노출 설정 -->
	<div class="hashtagConfig-goodsView-layout">
		<div class="title title_top">
			상품 상세 페이지 해시태그 노출 설정
			<span>상품 상세 페이지에서 상품에 등록된 해시태그 노출 설정을 할 수 있습니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>노출 설정</td>
				<td>
					<input type="radio" name="hashtag_goodsView_use" value="y" <?php echo $checked['hashtag_goodsView_use']['y']; ?> /> 노출함
					&nbsp;
					<input type="radio" name="hashtag_goodsView_use" value="n" <?php echo $checked['hashtag_goodsView_use']['n']; ?> /> 노출안함
					&nbsp;
					<span class="extext">상품 상세 페이지에 상품에 등록된 해시태그 리스트를 노출합니다.(최대 10개)</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image2.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>노출 기준설정</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsView_order_by" value="goodsCount" <?php echo $checked['hashtag_goodsView_order_by']['goodsCount']; ?> /> 상품등록수순
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="newRegister" <?php echo $checked['hashtag_goodsView_order_by']['newRegister']; ?> /> 최근등록순
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="name" <?php echo $checked['hashtag_goodsView_order_by']['name']; ?> /> ㄱㄴㄷ순
						&nbsp;
						<input type="radio" name="hashtag_goodsView_order_by" value="user" <?php echo $checked['hashtag_goodsView_order_by']['user']; ?> /> 사용자설정
					</div>
					<div class="extext hashtagConfig-marginTop5">상품 상세 페이지에 해시태그 노출 시 노출 기준을 설정합니다. 사용자설정은 <a href="./adm_goods_form.php" target="_blank" class="hashtagConfig-fontLink">[상품 > 상품등록]</a> 페이지에서 상품별로 설정된 순서를 따릅니다.</div>
				</td>
			</tr>
			<tr>
				<td>고객 해시태그<br />입력 설정</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsView_user_write" value="y" <?php echo $checked['hashtag_goodsView_user_write']['y']; ?> /> 사용
						&nbsp;
						<input type="radio" name="hashtag_goodsView_user_write" value="n" <?php echo $checked['hashtag_goodsView_user_write']['n']; ?> /> 사용안함
					</div>
					<div class="extext hashtagConfig-marginTop5">
						상품 상세 페이지에서 고객이 해당 상품에 어울리는 해시태그를 직접 추가할 수 있는 기능의 사용여부를 선택합니다.
						고객으로부터 해시태그를 수집하면, 상품에 등록된 해시태그 데이터를 늘릴 수 있으므로 보다 유용하게 해시태그 기능을 사용하실 수 있습니다.
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- 상품 상세 페이지 해시태그 노출 설정 -->

	<!-- 상품 리스트 해시태그 노출 설정 -->
	<div class="hashtagConfig-goodsList-layout">
		<div class="title title_top">
			상품 리스트 해시태그 노출 설정
			<span>상품 리스트에 상품에 등록된 해시태그 노출 설정을 할 수 있습니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>노출 설정</td>
				<td>
					<input type="radio" name="hashtag_goodsList_use" value="y" <?php echo $checked['hashtag_goodsList_use']['y']; ?> /> 노출함
					&nbsp;
					<input type="radio" name="hashtag_goodsList_use" value="n" <?php echo $checked['hashtag_goodsList_use']['n']; ?> /> 노출안함
					&nbsp;
					<span class="extext">메인, 분류, 검색, 이벤트 페이지 상품리스트에 해시태그 노출여부를 설정합니다.</span>
					&nbsp;<img src="../img/icons/icon_qmark.gif" style="vertical-align:middle;cursor:pointer;" class="godo-tooltip" tooltip="<img src=&quot;../img/hashtag_info_image3.png&quot; border=0 />">
				</td>
			</tr>
			<tr>
				<td>노출 개수 설정</td>
				<td>
					<select name="hashtag_goodsList_display_count">
						<?php for($i=1; $i<=10; $i++){ ?>
						<option value="<?php echo $i; ?>" <?php echo $selected['hashtag_goodsList_display_count'][$i]; ?>><?php echo $i; ?></option>
						<?php } ?>
					</select>
					<span class="extext">상품리스트에 노출될 해시태그의 개수를 설정합니다.</span>
				</td>
			</tr>
			<tr>
				<td>노출 기준설정</td>
				<td>
					<div>
						<input type="radio" name="hashtag_goodsList_order_by" value="goodsCount" <?php echo $checked['hashtag_goodsList_order_by']['goodsCount']; ?> /> 상품등록수순
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="newRegister" <?php echo $checked['hashtag_goodsList_order_by']['newRegister']; ?> /> 최근등록순
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="name" <?php echo $checked['hashtag_goodsList_order_by']['name']; ?> /> ㄱㄴㄷ순
						&nbsp;
						<input type="radio" name="hashtag_goodsList_order_by" value="user" <?php echo $checked['hashtag_goodsList_order_by']['user']; ?> /> 사용자설정
					</div>
					<div class="extext hashtagConfig-marginTop5">상품 리스트에 해시태그 노출 시 노출 기준을 설정합니다. 사용자설정은 <a href="./adm_goods_form.php" target="_blank" class="hashtagConfig-fontLink">[상품 > 상품등록]</a> 페이지에서 상품별로 설정된 순서를 따릅니다</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- 상품 리스트 해시태그 노출 설정 -->

	<!-- 해시태그 치환코드 설정 -->
	<div class="hashtagConfig-replaceCode-layout">
		<div class="title title_top">
			해시태그 치환코드 설정
			<span>쇼핑몰 해시태그를 치환코드 형태로 제공합니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=52')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>

		<table class="tb">
			<colgroup>
				<col class="cellC" />
				<col class="cellL" />
			</colgroup>
			<tbody>
			<tr>
				<td>해시태그 리스트<br />치환코드</td>
				<td>
					<div>{p.hashtagCode->displayHashtag(옵션,노출개수)}</div>
					<div class="extext hashtagConfig-marginTop5">원하는 페이지에 해시태그 리스트가 노출되도록 치환코드를 삽입하실 수 있습니다.</div>
					<div class="extext">옵션: 1 상품등록수순, 2 최근등록순, 3 ㄱㄴㄷ순, 4. 사용자설정&nbsp;&nbsp;<span class="hashtagConfig-fontLink hashtagDisplayPopupBtn">[설정하기▶]</span></div>
					<div class="extext">노출개수: 노출을 원하는 치환코드의 개수를 넣어주세요. (최대: 50개)</div>
					<div class="extext">예) 상품등록수순으로 10개의 해시태그를 노출하고자 하는 경우 : {치환코드(1,10)}</div>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	<!-- 해시태그 치환코드 설정 -->

	<div class="button hashtagConfig-buttonLayout">
		<img src="../img/btn_save.gif" border="0" style="cursor: pointer;" id="hashtagConfig_submitImg" />
		<a href="javascript:history.back(-1);"><img src="../img/btn_cancel.gif" border="0" /></a>
	</div>

	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(HashtagConfigController);
</script>
<?php include '../_footer.php'; ?>