<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
include '../../lib/page.class.php';

$goodsHelper = Clib_Application::getHelperClass('admin_goods');

// 파라미터 설정
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'regdt' => Clib_Application::request()->get('regdt'),
	'stock_type' => Clib_Application::request()->get('stock_type'),
	'stock_amount' => Clib_Application::request()->get('stock_amount'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
	'hashtag' => str_replace(" ", "_", trim(Clib_Application::request()->get('hashtag'))),
);

// 상품 목록
$goodsList = $goodsHelper->getGoodsCollection($params);
// 페이징
$pg = $goodsList->getPaging();
// 상품 검색 폼
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>
<link rel="stylesheet" type="text/css" href="../goods/css/css.css">
<style>
html body { margin: 0px; padding: 0px; height: 100%; }
form #open,
form #open_0,
form #open_1,
form #soldout,
form #soldout_0,
form #soldout_1,
form #stock_type_product,
form #stock_type_item { border:0px ;}
.goodsChoice_buttonAddArea { float: left; cursor: pointer; }
.goodsChoice_titleTopAreaLeft { float: left; margin-bottom: 3px; }
.goodsChoice_titleTopAreaRight { float: right; margin-bottom: 3px; }
.goodsChoice_searchButton { text-align: center; margin: 10px 0px 10px 0px;}
table.goodsChoiceListTable {width:100%;border-collapse: collapse;}
table.goodsChoiceListTable th,
table.goodsChoiceListTable td {border:1px solid #ccc; padding:5px;font-weight:normal;color:#303030;}

table.goodsChoiceListTable tbody th {background:#f6f6f6; text-align:left;padding-left:5px; width:80px;}
table.goodsChoiceListTable tbody td {background:#fff; vertical-align:middle;line-height:1.5em;}
table.goodsChoiceListTable tbody td #sword { width: 90px; }

table.goodsChoiceListTable th {font-weight:bold;}

table.goodsChoice-admin-list-table {width:100%;border-collapse: collapse;table-layout:fixed;color:#303030;}
table.goodsChoice-admin-list-table th,
table.goodsChoice-admin-list-table td {border:none;padding:5px;font-weight:normal;}
table.goodsChoice-admin-list-table th {padding:5px 0;}

table.goodsChoice-admin-list-table tbody th {background:#f6f6f6;text-align:left;padding-left:10px;width:130px;}
table.goodsChoice-admin-list-table tbody td {vertical-align:middle;line-height:1.5em;border-bottom:1px solid #ccc;overflow:hidden;}

table.goodsChoice-admin-list-table thead th,
table.goodsChoice-admin-list-table thead td {background: url('../img/table_title_bg.gif') center center repeat-x;background-size:auto 100%;font:11px 돋움;line-height:100%;vertical-align:middle;_height:26px;color:#fff;padding:8px 0;}

table.goodsChoice-admin-list-table thead th {font-weight:bold;}


table.goodsChoice-admin-list-table tbody tr.has-info th,
table.goodsChoice-admin-list-table tbody tr.has-info td {border-bottom:1px solid #dbdbdb;}

table.goodsChoice-admin-list-table .price {overflow:visible; white-space:nowrap;}
</style>
<script type="text/javascript" src="../goods/js/goods_list.js"></script>
<script type="text/javascript" src="../js/adm_form.js"></script>
<link href="<?php echo $cfg['rootDir']; ?>/lib/js/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="<?php echo $cfg['rootDir']; ?>/proc/hashtag/hashtagControl.js?actTime=<?php echo time(); ?>"></script>

<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form" action="./_goodsChoiceList.php">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">
<input type="hidden" name="searchParam" value="<?php echo http_build_query($params); ?>">

<table cellpadding="0" cellspacing="0" width="100%">
<tr>
	<td>
		<table class="goodsChoiceListTable">
		<colgroup>
			<col width="80" style="width: 80px;" />
			<col width="*" />
			<col width="80" style="width: 80px;" />
			<col width="80" style="width: 80px;" />
		</colgroup>
		<tr>
			<th>분류선택</th>
			<td colspan=3><script type="text/javascript">new categoryBox('cate[]',4,'<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>');</script></td>
		</tr>
		<tr>
			<th>검색어</th>
			<td>
				<?=$searchForm->getTag('skey');?>
				<?=$searchForm->getTag('sword');?>
			</td>
			<th>브랜드</th>
			<td><?=$searchForm->getTag('brandno');?></td>
		</tr>
		<tr>
			<th>상품진열여부</th>
			<td colspan="3">
				<?php
				foreach ($searchForm->getTag('open') as $label => $tag) {
					echo sprintf('<label>%s%s</label> ',$tag, $label);
				}
				?>
			</td>
		</tr>
		<tr>
			<th>품절상품</th>
			<td colspan="3">
				<?php
				foreach ($searchForm->getTag('soldout') as $label => $tag) {
					echo sprintf('<label>%s%s</label> ',$tag, $label);
				}
				?>
			</td>
		</tr>
		<tr>
			<th>해시태그</th>
			<td colspan="3">
			<div style="border: 1px #BDBDBD solid; width: 170px; height: 20px;">#<?php echo $searchForm->getTag('hashtag'); ?></div>
			</td>
		</tr>
		<tr id="displayTr1">
			<th>상품재고수량</th>
			<td colspan=3>
				<?php
				foreach ($searchForm->getTag('stock_type') as $label => $tag) {
					echo sprintf('<label>%s%s</label> ',$tag, $label);
				}

				$stock_amount = (array)Clib_Application::request()->get('stock_amount');
				?>
				<div>
					<input type=text name=stock_amount[] value="<?=$stock_amount[0]?>" onkeydown="onlynumber()" size="15" class="ar"> 개 -
					<input type=text name=stock_amount[] value="<?=$stock_amount[1]?>" onkeydown="onlynumber()" size="15" class="ar"> 개
				</div>

				<p class="help">
					<font color="blue">상품재고:</font> 상품내 품목(가격옵션)별 재고 총합의 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 조회대상이 됩니다. <br/>
					<font color="blue">품목재고:</font> 품목(가격옵션) 개별 재고 조건을 말합니다. 주문시 재고차감(재고량연동)인 상품만 조회대상이 됩니다.
				</p>
			</td>
		</tr>
		<tr id="displayTr2">
			<th>상품등록일</th>
			<td colspan=3>
				<?
				$regdt = (array)Clib_Application::request()->get('regdt');
				?>
				<input type="text" name="regdt[]" value="<?=$regdt[0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
				<input type="text" name="regdt[]" value="<?=$regdt[1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
				<br />
				<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
				<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
			</td>
		</tr>
		</table>
		<div class="goodsChoice_buttonAddArea"><img src="../img/btn_addSearchOpen.gif" onclick="javascript:searchAddGoodsChoice();" id="goodsChoicd_addSearch" style="margin-top: 3px;" /></div>
	</td>
</tr>
</table>

<div class="goodsChoice_searchButton"><input type="image" src="../img/btn_search2.gif" /></div>


<div class="goodsChoice_titleTopAreaLeft"><img src="../img/btn_goodSerchAll.gif" id="goodSerchAll" class="hand" /></div>

<div class="goodsChoice_titleTopAreaRight">
<select name=page_num onchange="this.form.submit()">
<?
$r_pagenum = array(10,20,40,60,100);
foreach ($r_pagenum as $v){
?>
<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>개 출력
<? } ?>
</select>
</div>
</form>

<table class="goodsChoice-admin-list-table">
<colgroup>
	<col style="width:5%;">
	<col style="width:5%;">
	<col style="width:10%;">
	<col >
	<col style="width:10%;">
	<col style="width:10%;">
	<col style="width:10%;">
	<col style="width:10%;">
</colgroup>
<thead>
<tr>
	<th><div id="goodsChoiceCheckBoxAll"><a href="javascript:void(0)" onclick="javascript:chkBox(document.getElementsByName('goodsno[]'),'rev');" class="white">선택</a></div></th>
	<th>번호</th>
	<th>이미지</th>
	<th>상품명</th>
	<th>판매가격</th>
	<th>판매재고</th>
	<th>진열여부</th>
	<th>품절여부</th>
</tr>
</thead>
<tbody>
<?
	$listNo = $pg->idx;
	foreach($goodsList as $goods) {
		$soldout = '';
		if ($goods->getSoldout()) $soldout = '품절';

		$open = '진열';
		if($goods->getData('open') == '0') $open = '미진열';

?>
<tr class="ac has-info hand">
	<td><input type="checkbox" name="goodsno[]" value="<?php echo $goods->getId(); ?>"></td>
	<td><?=$listNo--?></td>
	<td class="al"><a href="../../goods/goods_view.php?goodsno=<?php echo $goods->getId(); ?>" target=_blank><?php echo goodsimg($goods[img_s], '40,40', 'style="vertical-align:middle;border:1px solid #e9e9e9;"', 1); ?></a></td>
	<td class="al"><?php echo strcut(strip_tags($goods->getGoodsName()), 50); ?></a></td>
	<td class="price"><?php echo number_format($goods->getPrice()); ?></td>
	<td><?php echo number_format($goods->getStock()); ?></td>
	<td><?php echo $open; ?></td>
	<td><?php echo $soldout; ?></td>
</tr>
<? } ?>
</tbody>
</table>


<div class="admin-list-toolbar">
	<div class="paging"><?=$pg->page['navi']?></div>
</div>
<script type="text/javascript">
function searchAddGoodsChoice()
{
	var displayTr1 = document.getElementById('displayTr1');
	var displayTr2 = document.getElementById('displayTr2');
	var goodsChoicd_addSearch = document.getElementById('goodsChoicd_addSearch');
	var searchAdd = false;

	if('<?php echo $_GET[stock_amount][0]; ?>' || '<?php echo $_GET[stock_amount][1]; ?>' || '<?php echo $_GET[regdt][0]; ?>' || '<?php echo $_GET[regdt][1]; ?>'){
		searchAdd = true;
	}

	if(displayTr1.style.display == 'none' || searchAdd === true){
		displayTr1.style.display = displayTr2.style.display = '';
		goodsChoicd_addSearch.src = '../img/btn_addSearchClose.gif';
	}
	else {
		displayTr1.style.display = displayTr2.style.display = 'none';
		goodsChoicd_addSearch.src = '../img/btn_addSearchOpen.gif';
	}
}

// onload events
Event.observe(document, 'dom:loaded', function(){
	searchAddGoodsChoice();
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
jQuery(document).ready(HashtagInputListController);
</script>
<?php include '../_footer.popup.php'; ?>