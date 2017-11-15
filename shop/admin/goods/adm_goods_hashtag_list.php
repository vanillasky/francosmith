<?php
$location = "상품관리 > 해시태그 관리";
include '../_header.php';

$hashtag = Core::loader('hashtag');
$hashtagConfig = $hashtag->getConfig();

if(!$hashtagConfig['hashtag_snsUse']) $hashtagConfig['hashtag_snsUse'] = 'y';
$checked['hashtag_snsUse'][$hashtagConfig['hashtag_snsUse']] = "checked='checked'";
?>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>
<style>
.hashtagList-boxBorder { border: 1px solid #e8e8e8; }
.hashtagList-layout { width: 1000px; }
.hashtagList-layout .hashtagList-marginTop5 { margin-top: 5px; }
.hashtagList-layout .hashtagList-common-layout { width: 100%; height: 200px;}
.hashtagList-layout .hashtagList-common-layout .hashtagListConfig-save-button { width: 100%; margin-top: 10px; text-align: center; }
.hashtagList-layout .hashtagList-common-layout .hashtag-skinPatchInfo { border: 1px solid #cccccc; height: 30px; line-height:30px; margin-bottom: 10px; width: 99%; padding: 3px; color: red; font-weight: bold;}
.hashtagList-layout .hashtagList-list-layout { width: 100%; margin-top: 40px; height: 600px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout { float: left; width: 300px; height: 550px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagAdd {  width: 100%; margin-top: 10px; padding: 5px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagSearch {  width: 100%; margin-top: 10px; padding: 5px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-leftLayout .hashtagList-hashtagListBox {  width: 100%; height: 350px; padding: 5px; overflow-y: auto; border-top: none; background-color: #cccccc;}
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout { float: left; width: 600px; height: 700px; margin-left: 30px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout { }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout .right-second-title { font-weight: bold; font-family: Dotum; font-size: 14px; margin-bottom: 10px; }
.hashtagList-layout .hashtagList-list-layout .hashtagList-rightLayout .right-second-layout .right-second-title span { font: 11px dotum; padding-left: 10px; color: #6d6d6d; }
.hashtagInputText { border: 1px #BDBDBD solid; width: 170px; float: left; height: 18px; }
.hashtagInputText input { border: none; height: 16px; width: 150px; }
.hashtagInputTextButton { margin-left: 3px; }
</style>

<input type="hidden" name="cfgRootDir" id="cfgRootDir" value="<?php echo $cfg['rootDir']; ?>" />
<div class="hashtagList-layout">
	<!-- 상단 레이아웃 -->
	<div class="hashtagList-common-layout">
		<form name="hashtagListConfigForm" id="hashtagListConfigForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
		<input type="hidden" name="mode" id="mode" value="" />

		<div class="title title_top">해시태그 상품리스트 공통 설정 <span>해시태그 상품리스트 페이지의 기능을 설정합니다.</span></div>
		
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
				<td>SNS 공유기능<br />사용설정</td>
				<td>
					<div>
						<input type="radio" name="hashtag_snsUse" value="y" <?php echo $checked['hashtag_snsUse']['y']; ?> /> 사용
						&nbsp;
						<input type="radio" name="hashtag_snsUse" value="n" <?php echo $checked['hashtag_snsUse']['n']; ?> /> 사용안함
					</div>
					<div class="extext hashtagList-marginTop5">프로모션 > SNS 공유하기 설정관리에 출력으로 설정된 SNS 공유 기능을 사용합니다.</div>
					<div class="extext">쇼핑몰명, 해시태그명, 해당 상품리스트 URL을 공유할 수 있습니다.</div>
				</td>
			</tr>
			</tbody>
		</table>

		<div class="hashtagListConfig-save-button"><img src="../img/btn_save.gif" border="0" style="cursor: pointer;" id="hashtagListConfig-save-btn" /></div>
		</form>
	</div>
	<!-- 상단 레이아웃 -->

	<!-- 하단 레이아웃 -->
	<div class="hashtagList-list-layout">
		<div class="title title_top">
			해시태그 관리
			<span>해시태그 등록 및 삭제, 해시태그 상품리스트 페이지를 관리할 수 있습니다.</span>
			<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=51')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
		</div>
		<!-- 좌측 레이아웃 -->
		<div class="hashtagList-leftLayout">
			<!-- 해시태그 추가 -->
			<div class="hashtagList-hashtagAdd hashtagList-boxBorder">
				<div>
					<div>
					  <div class="hashtagInputText">#<input type="text" name="hashtag" id="hashtag" class="hashtagInputListSearch" maxlength="20" /></div>
					  <img src="../img/btn_add3.png" border="0" class="hand hashtagInputTextButton" id="hashtagAddBtn" alt="추가" align="absmiddle" />
					</div>
				</div>
				<div class="hashtagList-marginTop5">
					<div>ENTER 키로도 해시태그 추가가 가능합니다.(최대 20자)</div>
					<div class="extext">'윁','붍' 등의 부적절한 문자는 등록 할 수 없습니다.</div>
				</div>
			</div>
			<!-- 해시태그 추가 -->

			<!-- 해시태그 검색 -->
			<div class="hashtagList-hashtagSearch hashtagList-boxBorder">
				<div>
					<div>
					  <div class="hashtagInputText">#<input type="text" name="hashtagSearch" id="hashtagSearch" class="hashtagInputListSearch" maxlength="20" /></div>
					   <img src="../img/btn_search3.png" border="0" class="hand hashtagInputTextButton" id="hashtagSearchBtn" alt="검색" align="absmiddle" />
					</div>
				</div>
			</div>
			<!-- 해시태그 검색 -->

			<form name="hashtagListForm" id="hashtagListForm" action="./adm_goods_hashtag_indb.php" method="post" target="ifrmHidden">
			<input type="hidden" name="mode" id="mode" value="" />
			<div id="hashtagListBox" class="hashtagList-hashtagListBox hashtagList-boxBorder"></div>
			</form>
		</div>
		<!-- 좌측 레이아웃 -->

		<!-- 우측 레이아웃 -->
		<div class="hashtagList-rightLayout">
			<div class="right-second-layout">
				<div class="right-second-title">해시태그 정보 <span>해시태그 별로 정보를 확인하실 수 있습니다.</span></div>
				<input type="hidden" name="hashtagWidget_name" id="hashtagWidget_name" value="" />
				<table class="tb">
					<colgroup>
						<col class="cellC" />
						<col class="cellL" />
					</colgroup>
					<tbody>
					<tr>
						<td>등록된 상품</td>
						<td><span id="hashtagRegistGoodsCount">0</span>개</td>
					</tr>
					<tr>
						<td>상품리스트 URL</td>
						<td>
							<div id="hashtagWidgetUrl">해시태그를 선택하여 주세요.</div>
							<div class="extext hashtagList-marginTop5">URL을 복사하여 배너, 팝업 등에 링크를 걸어 홍보해보세요.</div>
						</td>
					</tr>
					<tr>
						<td>상품리스트<br />코드 생성</td>
						<td>
							<div class="hashtagList-marginTop5 extext">해당 해시태그 상품리스트를 이벤트 페이지 등에 삽입할 수 있도록 소스코드를 생성할 수 있습니다.</div>

							<table class="tb">
							<colgroup>
								<col class="cellC" />
								<col class="cellL" />
							</colgroup>
							<tbody>
							<tr>
								<td>레이아웃</td>
								<td>
									<input type="text" name="hashtagWidget_width" id="hashtagWidget_width" size="2" value="4" maxlength="2" />
									*
									<input type="text" name="hashtagWidget_height" id="hashtagWidget_height" size="2" value="2" maxlength="2" />
								</td>
							</tr>
							<tr>
								<td>상품 리스트<br />영역 사이즈</td>
								<td>
									<input type="text" name="hashtagWidget_iframeWidth" id="hashtagWidget_iframeWidth" size="4" value="1000" maxlength="4" />px
									&nbsp;<span class="extext">가로 사이즈를 설정해 주세요.</span>
								</td>
							</tr>
							<tr>
								<td>상품 이미지<br />사이즈</td>
								<td>
									<input type="text" name="hashtagWidget_imageWidth" id="hashtagWidget_imageWidth" size="4" value="150" maxlength="4" />px
									&nbsp;<span class="extext">가로 사이즈를 설정해 주세요.</span>
								</td>
							</tr>
							</table>

							<div class="hashtagList-marginTop5"><img src="../img/createCode.png" border="0" class="hand" align="absmiddle" id="hashtagCreateCodeBtn" /></div>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- 우측 레이아웃 -->
	</div>
	<!-- 하단 레이아웃 -->
</div>

<script type="text/javascript">
jQuery(document).ready(HashtagListController);
</script>
<?php include '../_footer.php'; ?>