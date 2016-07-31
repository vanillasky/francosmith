<?php

$location = '상품진열 > 분류페이지 상품진열';
include dirname(__FILE__).'/../_header.php';

$goodsSort = Core::loader('GoodsSort');

if ($_GET['cate']) {
	$category = array_pop(array_notnull($_GET['cate']));
}

if (!$_GET['limitRows']) $limitRows = $goodsSort->limitSet[0];
else if ($_GET['limitRows'] > $goodsSort->limitSet[count($goodsSort->limitSet)-1]) $limitRows = $goodsSort->limitSet[count($goodsSort->limitSet)-1];
else $limitRows = $_GET['limitRows'];

?>

<link rel="stylesheet" type="text/css" href="./css/adm_goods_sort.css"/>
<script type="text/javascript" src="./js/adm_goods_sort.js"></script>

<div class="title title_top">
	분류페이지 상품진열
	<span>각 분류페이지의 상품진열순서를 정하실 수 있습니다</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=8')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<form id="goods-search-form">
	<table class="tb">
		<colgroup>
			<col class="cellC"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<td>분류선택</td>
			<td><script type="text/javascript">new categoryBox('category[]', 4, '<?php echo $category; ?>', 'multiple');</script></td>
		</tr>
	</table>
	<div class="button_top"><input type=image src="../img/btn_search2.gif"/></div>

	<div id="goods-sort-type-description"></div>
	<script type="text/html" id="goods-sort-type-description-template">
		<div class="sort-type-status">
			<span class="category-name">"#{categoryLocation}"</span>카테고리의 상품진열 타입 :
			<a href="#MSG01" class="sort-type help-link auto">자동진열</a>
			<span class="description auto">(자동진열 상태에서는 진열순서를 변경할 수 없으며, 진열여부만 설정 가능합니다.)</span>
			<a href="#MSG01" class="sort-type help-link manual">수동진열</a>
			<span class="description manual">(진열순서와 진열여부를 변경할 수 있습니다.)</span>
		</div>
		<div class="manual-sort-on-link-goods-position-form">
			카테고리에 상품 새로 연결 시
			<select id="manual-sort-on-link-goods-position">
				<option value="LAST">맨 뒤에 진열</option>
				<option value="FIRST">맨 앞에 진열</option>
			</select>
		</div>
		<div class="change-category-sort-type">
			상품 진열타입 변경하기 :
			<button id="change-category-sort-type-manual" class="auto" type="button">수동진열로 변경하기</button>
			<button id="change-category-sort-type-auto" class="manual" type="button">자동진열로 변경하기</button>
		</div>
		<div class="sort-type-description extext">
			<span class="bold">자동진열</span> : 가장 최근에 카테고리에 등록된 상품순으로(최근 등록된 상품이 맨앞) 진열되어 출력됩니다.<br/>
			<span class="bold">수동진열</span> : 카테고리에 등록한 순서와 상관 없이 운영자가 편집한 순서대로 상품을 진열 합니다. 카테고리에 새로 등록한 상품은 리스트의 가장 마지막에 출력됩니다.
		</div>
	</script>

	<ul id="goods-sort-guide">
		<li>
			1차 분류, 2차 분류, 3차 분류, 4차 분류별로 각각 진열순서를 지정할 수 있습니다.
		</li>
		<li>
			상품 선택<br/>
			- 리스트 형 보기 시 : 선택할 상품의 이미지, 상품명을 제외한 빈 공간을 클릭하면 해당 상품이 선택됩니다.<br/>
			- 갤러리 형 보기 시 : 선택할 상품의 상품명을 제외한 이미지나 빈 공간을 클릭하면 해당 상품이 선택됩니다.<br/>
			- 범위선택 : 범위선택할 첫번째 상품을 선택하고, 마지막 상품을 선택하면, 해당 범위안에 있는 상품이 모두 선택됩니다.
		</li>
		<li>
			선택상품 이동<br/>
			- 키보드 사용 시 : 상품/영역 선택 후, 상하 이동키 ↑ ↓, 또는 좌우 이동키 ← → 로 상품의 위치를 이동합니다.<br/>
			<span style="margin-right: 100px;"></span>Home키를 누르면 현재 페이지 맨 위, End키를 누르면 현재 페이지 맨 아래로 이동합니다.<br/>
			- 마우스 사용 시 : 상품/영역 선택 후, 선택 된 영역을 마우스로 드래그 하여 상품의 위치를 이동합니다.
		</li>
		<li>
			선택 취소<br/>
			- 단일상품 선택 시 : 선택된 상품/영역을 다시 클릭하면 선택 된 영역이 해제됩니다.
			- 다수상품 선택 시 : 상품리스트를 클릭하면 선택 된 영역이 해제됩니다.
		</li>
		<li>
			선택상품 페이지 이동<br/>
			- 선택상품 페이지 이동 기능을 사용하여 이동 시 에는 선택된 상품이 지정한 페이지로 이동하여 저장됩니다.(실제 쇼핑몰 진열페이지에 반영 됨)<br/>
			- 선택상품을 다른페이지로 이동하기 전에 현재페이지에 작업한 내용을 저장해야 합니다.
		</li>
		<li>
			저장한 순서대로 쇼핑몰에 반영이 되지 않을 때는 <a href="./soldout.php" target="_blank" class="adm_goods_sort_link">[품절상품 진열설정]</a> 페이지에서 "분류페이지 품절상품 진열 설정"항목을 확인하여 보시기 바랍니다.<br/>
			(본 페이지의 상품리스트에는 품절상품 진열에대한 설정이 반영되지 않습니다.)<br/>
		</li>
		<li>
			품절여부와 관계없이 진열순서 저장 시 에러가 발생하거나, 오작동 시 에는 <a id="optimize-manual-sort" href="javascript:void(0);" class="adm_goods_sort_link">[진열순서 최적화]</a>를 클릭하여 본 카테고리의 진열순서를 최적화 시켜보시기 바랍니다.
		</li>
		<li>
			<button id="optimize-manual-sort-button" type="button"> 진열순서 최적화 </button>
		</li>
	</ul>

	<table id="list-display-option" class="tb">
		<col class="cellC"><col class="cellL"><col class="cellC"><col class="cellL">
		<tr>
			<td width="120" nowrap>리스트 보기 설정</td>
			<td class="noline">
				<input type="hidden" name="defaultViewType" value="<?php echo $goodsSort->getConfig("viewType"); ?>"/>
				<input type="hidden" name="defaultImageSize" value="<?php echo $goodsSort->getConfig("imageSize"); ?>"/>
				<input type="hidden" name="defaultLimitRows" value="<?php echo $goodsSort->getConfig("limitRows"); ?>"/>
				<span class="view-type-selector">
					<input type="radio" id="view-type-list" name="viewType" value="LIST"/>
					<label for="view-type-list">리스트 형 보기</label>
					<input type="radio" id="view-type-gallery" name="viewType" value="GALLERY"/>
					<label for="view-type-gallery">갤러리 형 보기</label>
				</span>
				<span class="image-size-selector">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					상품이미지 사이즈
					<select name="imageSize">
						<option value="25">25px</option>
						<option value="50">50px</option>
						<option value="100">100px</option>
					</select>
				</span>
				<span class="limit-rows-selector">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					출력수
					<select name="limitRows">
					<?php foreach ($goodsSort->limitSet as $limit) { ?>
					<option value="<?php echo $limit; ?>"><?php echo $limit; ?>개 출력</option>
					<?php } ?>
					</select>
				</span>
				<span class="action">
					&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
					<button id="save-list-display-option" type="button">설정저장</button>
				</span>
			</td>
		</tr>
	</table>

	<div class="move-selection-box"></div>
	<script type="text/html" id="move-selection-box-template">
		선택상품 페이지 이동 :
		<select class="move-selection">
			<option value="">-- 선택 --</option>
			<option value="firstTop">첫 페이지 맨 앞으로 이동 ↑</option>
			<option value="nextTop">다음 페이지 맨 앞으로 이동 ↑</option>
			<option value="nextBottom">다음 페이지 맨 뒤로 이동 ↓</option>
			<option value="prevTop">이전 페이지 맨 앞으로 이동 ↑</option>
			<option value="prevBottom">이전 페이지 맨 뒤로 이동 ↓</option>
			<option value="lastBottom">마지막 페이지 맨 뒤으로 이동 ↓</option>
		</select>
		<span class="extext" style="font-size: 11px;">페이지 이동 선택 시, 바로 저장되어 쇼핑몰에 반영됩니다.</span>
	</script>
</form>

<form id="goods-sort-form" method="post" action="indb.php">
	<input type="hidden" name="tplSkin" value="<?php echo $cfg['tplSkin']; ?>">
	<input type="hidden" name="mode" value="sortGoods">
	<div id="goods-content" name="#goods-content">
		<table class="head" width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr><td class="rnd" colspan="9"></td></tr>
			<tr class="rndbg">
				<th class="no">번호</th>
				<th class="image">상품이미지</th>
				<th class="name">상품명</th>
				<th class="option">옵션:옵션값</th>
				<th class="soldout">품절여부</th>
				<th class="open">진열여부</th>
				<th class="sell-stock">판매재고</th>
				<th class="real-stock">실재고</th>
				<th class="price">판매금액</th>
			</tr>
			<tr><td class="rnd" colspan="9"></td></tr>
		</table>
		<ol class="body" start="1"></ol>
		<div class="foot"></div>
		<script type="text/html" id="result-row-template">
			<li class="result">#{message}</li>
		</script>
		<script type="text/html" id="soldout-image-template">
			<img src="../../data/skin/#{tplSkin}/img/icon/good_icon_soldout.gif"/>
		</script>
		<script type="text/html" id="goods-row-option-template">
			<div class="option-item">
				<span class="option-item-name">#{optionName}</span> :
				<span class="option-item-values">#{optionValues}</span>
			</div>
		</script>
		<script type="text/html" id="goods-row-template">
			<li id="data-#{sno}" class="data" data-sno="#{sno}" data-goodsno="#{goodsno}" data-sort="#{sort}" data-origin-sort="#{sort}" data-origin-open="#{open}" data-index="#{index}">
				<div class="field no">#{_no}</div>
				<div class="field image">
					<a href="../../goods/goods_view.php?goodsno=#{goodsno}" target="_blank">#{imageTag}</a>
				</div>
				<div class="field name">
					<a href="javascript:void(0);" onclick="popup('popup.register.php?mode=modify&goodsno=#{goodsno}', 825, 600);">#{goodsnm}</a>
				</div>
				<div class="field option">#{option}</div>
				<div class="field soldout">#{soldoutImage}</div>
				<div class="field open">
					<select name="open">
						<option value="1"#{open1Selected}>YES</option>
						<option value="0"#{open2Selected}>NO</option>
					</select>
				</div>
				<div class="field sell-stock">#{sellstock}</div>
				<div class="field real-stock">#{realstock}</div>
				<div class="field price">#{price}원</div>
			</li>
		</script>
		<script type="text/html" id="page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">[#{pageNum}]</a>
		</script>
		<script type="text/html" id="prev-page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">이전</a>
		</script>
		<script type="text/html" id="next-page-anchor-template">
			<a class="page normal" data-page="#{pageNum}" href="#goods-content">다음</a>
		</script>
		<script type="text/html" id="active-page-anchor-template">
			<span class="page activate" data-page="#{pageNum}">#{pageNum}</span>
		</script>
	</div>

	<div class="move-selection-box"></div>
	
	<div class="button">
		<input type="image" src="../img/btn_save.gif"/>
		<img id="cancel-modified" src="../img/btn_cancel.gif"/>
	</div>
</form>

<div id="MSG01" name="#MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex" style="font-family: Dotum; font-size: 11px;">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">분류별페이지마다 구매자에게 어필하는 상품을 효과적으로 순서를 정해 진열하세요.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">구매자들은 보통 특정분류에서 상품을 조회하고 구매의욕을 갖게 되는데 이때 상품의 진열은 중요합니다.<td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">자동진열 관리 : 상품의 등록순서대로 노출됩니다.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">수동진열 관리 : 수동으로 진열순서를 변경하실 수 있으며 1차, 2차, 3차, 4차 분류별로 진열순서를 설정하실 수 있습니다.<td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">※ [저장] 버튼을 클릭하여 작업내용을 저장해 주세요.<td></tr>
</table>
</div>
<script type="text/javascript">cssRound("MSG01");</script>

<? include "../_footer.php"; ?>