<?
$location = "상품일괄관리 > 가격/적립금/재고수정 ";
include "../_header.php";
include "../../lib/page.class.php";
@include_once "../../conf/config.purchase.php";

$goodsHelper = Clib_Application::getHelperClass('admin_goods_option');

// 파라미터 설정
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'regdt' => Clib_Application::request()->get('regdt'),
	'price' => Clib_Application::request()->get('price'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'stock' => Clib_Application::request()->get('stock'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),
);

// 상품 목록
$goodsOptionList = $goodsHelper->getGoodsCollection($params);

// 페이징
$pg = $goodsOptionList->getPaging();

// 상품 검색 폼
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<script type="text/javascript">
function __batchInput() {

	var tr, fld, val;
	var form = $('manage-stock-list');

	$$('input[name="chk[]"]:checked').each(function(el){

		tr = el.up('tr');

		$w('consumer price supply reserve stock').each(function(name){
			fld = tr.select('[name^="'+name+'"]')[0];
			val = $F(form['all_' + name]);

			if (val != '') {
				fld.value = val;
			}

		});

	});

	return false;

}
</script>

<h2 class="title">가격/적립금/재고수정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=4');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
<tr>
	<th>분류선택</th>
	<td colspan="3">
	<script type="text/javascript" src="../../lib/js/categoryBox.js"></script>
	<script type="text/javascript">new categoryBox('cate[]',4,'<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>');</script>
	</td>
</tr>
<tr>
	<th>검색어</th>
	<td>
		<?=$searchForm->getTag('skey');?>
		<?=$searchForm->getTag('sword');?>
	</td>
	<th>브랜드</th>
	<td>
		<?=$searchForm->getTag('brandno');?>
	</td>
</tr>
<tr>
	<th>상품가격</th>
	<td colspan="3">
	<input type="text" name="price[]" value="<?=$_GET[price][0]?>" onkeydown="onlynumber()" size="15" class="ar"> 원 -
	<input type="text" name="price[]" value="<?=$_GET[price][1]?>" onkeydown="onlynumber()" size="15" class="ar"> 원
	</td>
</tr>
<tr>
	<th>상품등록일</th>
	<td colspan=3>
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][0]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac"> -
	<input type="text" name="regdt[]" value="<?=$_GET[regdt][1]?>" onclick="calendar(event)" onkeydown="onlynumber()" class="ac">
	<a href="javascript:setDate('regdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	<a href="javascript:setDate('regdt[]')"><img src="../img/sicon_all.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>상품진열여부</th>
	<td>
		<?php
		foreach ($searchForm->getTag('open') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
	<th>품절상품</th>
	<td>
		<?php
		foreach ($searchForm->getTag('soldout') as $label => $tag) {
			echo sprintf('<label>%s%s</label> ',$tag, $label);
		}
		?>
	</td>
</tr>
<tr>
	<th>재고량</th>
	<td colspan="3">
		<?=$searchForm->getTag('stock');?> 개 이하 (입력값이 없을시 전체 레코드를 조회합니다)
	</td>
</tr>
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div class="admin-list-toolbar">
	<div class="list-information">
		검색 <b><?=number_format($pg->recode['total'])?></b>개 / <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</div>

	<div class="list-tool">
	<ul>
		<li><img src="../img/sname_date.gif"><a href="javascript:nsAdminGoodsList.sort('goods.regdt desc')"><img name="sort_goods.regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods.regdt')"><img name="sort_goods.regdt" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_product.gif"><a href="javascript:nsAdminGoodsList.sort('goods.goodsnm desc')"><img name="sort_goods.goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods.goodsnm')"><img name="sort_goods.goodsnm" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_price.gif"><a href="javascript:nsAdminGoodsList.sort('goods_option.price desc')"><img name="sort_goods_option.price_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods_option.price')"><img name="sort_goods_option.price" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_brand.gif"><a href="javascript:nsAdminGoodsList.sort('goods.brandno desc')"><img name="sort_goods.brandno_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods.brandno')"><img name="sort_goods.brandno" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_company.gif"><a href="javascript:nsAdminGoodsList.sort('goods.maker desc')"><img name="sort_goods.maker_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods.maker')"><img name="sort_goods.maker" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li>
		<img src="../img/sname_output.gif" align=absmiddle>
		<select name=page_num onchange="this.form.submit()">
		<?
		$r_pagenum = array(10,20,40,60,100);
		foreach ($r_pagenum as $v){
		?>
		<option value="<?=$v?>" <?=($v == Clib_Application::request()->get('page_num')) ? 'selected' : ''?>><?=$v?>개 출력
		<? } ?>
		</select>
		</li>
	</ul>
	</div>
</div>
</form>

<form class="admin-form" id="manage-stock-list" method="post" action="indb_adm_goods_manage_option.php" target="ifrmHidden">
<input type=hidden name=mode value="stock">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:70px;">
	<col>
	<col style="width:80px;">
	<col style="width:80px;">
	<col style="width:60px;">
	<col style="width:60px;">
	<col style="width:60px;">
	<col style="width:60px;">
	<col style="width:55px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">선택</a></th>
	<th>번호</th>
	<th>상품명</th>
	<th>옵션1</th>
	<th>옵션2</th>
	<th>정가</th>
	<th>판매가</th>
	<th>매입가</th>
	<th>적립금</th>
	<th>재고</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsOptionList as $option) {
?>
<tr class="ac">
	<td><input type="checkbox" name="chk[]" value="<?=$option->getId()?>"></td>
	<td><?=$pg->idx--?></td>
	<td class="al vt">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$option->goods->getId()?>" target=_blank><?=goodsimg($option->goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$option->goods->getId()?>"><?=$option->goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$option->goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$option->goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>

	<td><?=$option->getOpt1()?></td>
	<td><?=$option->getOpt2()?></td>
	<td><span class="field-wrapper"><input type="text" name="consumer[<?=$option->getId()?>]" value="<?=$option->getConsumer()?>"></span></td>
	<td><span class="field-wrapper"><input type="text" name="price[<?=$option->getId()?>]" value="<?=$option->getPrice()?>"></span></td>
	<td><span class="field-wrapper"><input type="text" name="supply[<?=$option->getId()?>]" value="<?=$option->getSupply()?>"></span></td>
	<td><span class="field-wrapper"><input type="text" name="reserve[<?=$option->getId()?>]" value="<?=$option->getReserve()?>"></span></td>
	<td><span class="field-wrapper"><input type="text" name="stock[<?=$option->getId()?>]" value="<?=$option->getStock()?>"></span></td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="left-buttons">
	<a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	</div>
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<div style="margin-top:20px;"></div>

<table class="admin-form-table">
<tr>
	<th style="width:160px;">선택한 옵션<br />일괄 수정/적용</th>
	<td>

	<table class="admin-list-table" style="width:800px;">
	<thead>
	<tr>
		<th>정가</th>
		<th>판매가</th>
		<th>매입가</th>
		<th>적립금</th>
		<th>재고</th>
	</tr>
	</thead>
	<tbody>
	<tr class="ac">
		<td><input type="text" style="width:80px;" name="all_consumer" ></td>
		<td><input type="text" style="width:100%;" name="all_price"></td>
		<td><input type="text" style="width:100%;" name="all_supply"></td>
		<td><input type="text" style="width:100%;" name="all_reserve"></td>
		<td><input type="text" style="width:100%;" name="all_stock"></td>
		<td><a href="javascript:void(0);" onclick="__batchInput();return false;"><img src="../img/buttons/btn_modify_seting.gif"></a></td>
	</tr>
	</tbody>
	</table>

	</td>
</tr>
</table>

<div class=button>
<input type=image src="../img/btn_save.gif">
</div>

</form>

<ul class="admin-simple-faq">
	<li>각 상품의 옵션별 가격 및 재고를 수정하시려면 해당 입력박스의 숫자 변경 및 일괄수정/적용 후 [저장]버튼을 눌러 주세요.</li>
	<li>각 상품명을 클릭하면 상품정보를 수정하실 수 있습니다.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'), $('manage-stock-list'));
});
</script>

<? include "../_footer.php"; ?>
