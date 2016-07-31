<?

$location = "상품일괄관리 > 빠른 이동/복사/삭제";
include "../_header.php";
include "../../lib/page.class.php";

// 상품분류 연결방식 전환 여부에 따른 처리
if (_CATEGORY_NEW_METHOD_ === false) {
	go('./adm_goods_manage_link.php');
}

$goodsHelper = Clib_Application::getHelperClass('admin_goods');

// 파라미터 설정
$params = array(
	'page' => Clib_Application::request()->get('page', 1),
	'page_num' => Clib_Application::request()->get('page_num', 10),
	'cate' => Clib_Application::request()->get('cate'),
	'skey' => Clib_Application::request()->get('skey'),
	'sword' => Clib_Application::request()->get('sword'),
	'regdt' => Clib_Application::request()->get('regdt'),
	'goods_price' => Clib_Application::request()->get('goods_price'),
	'open' => Clib_Application::request()->get('open'),
	'soldout' => Clib_Application::request()->get('soldout'),
	'brandno' => Clib_Application::request()->get('brandno'),
	'sort' => Clib_Application::request()->get('sort', 'goodsno desc'),

	'unlink' => Clib_Application::request()->get('unlink'),
	'unbrand' => Clib_Application::request()->get('unbrand'),
);

// 상품 목록
$goodsList = $goodsHelper->getGoodsCollection($params);

// 페이징
$pg = $goodsList->getPaging();

// 상품 검색 폼
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));

// 현재 검색된 카테고리
$searchCategory	= array_pop(array_notnull(Clib_Application::request()->get('cate')));
?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<script type="text/javascript"><!--
function chkFormList(mode){
	var fObj = document.forms['fmList'];
	if (inArray(mode, new Array('move','copyGoodses','unlink')) && fObj.category.value == ''){
		if (mode == 'move') alert("분류이동는 분류로 검색했을 경우만 가능합니다.");
		else if (mode == 'copyGoodses') alert("상품복사는 분류로 검색했을 경우만 가능합니다.");
		else if (mode == 'unlink') alert("연결해제는 분류로 검색했을 경우만 가능합니다.");
		document.getElementsByName("cate[]")[0].focus();
		return;
	}
	if (isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return;
	}
	if (mode == 'delGoodses'){
		tobj = document.getElementsByName('chk[]');
		for(i=0; i< tobj.length; i++){
			if (tobj[i].checked === true && tobj[i].getAttribute('notDel') == 'notInpk'){
				alert("인터파크에 등록된 상품은 삭제할 수 없습니다.");
				tobj[i].focus();
				return;
			}
		}
	}

	if (mode == 'link' && document.getElementsByName("sCate[]")[0].value == '')
	{
		alert("선택한 상품에 연결 할 분류를 선택해주세요.");
		document.getElementsByName("sCate[]")[0].focus();
		return;
	}
	else if (mode == 'move' && document.getElementsByName("mCate[]")[0].value == '')
	{
		alert("선택한 상품을 이동 할 분류를 선택해주세요.");
		document.getElementsByName("mCate[]")[0].focus();
		return;
	}
	else if (mode == 'copyGoodses' && document.getElementsByName("ssCate[]")[0].value == '')
	{
		alert("선택한 상품을 복사 할 분류를 선택해주세요.");
		return false;

	}
	else if (mode == 'linkBrand' && fObj.select('select[name="brandno"]')[0].value == '')
	{
		alert("선택한 상품에 연결 할 브랜드를 선택해주세요.");
		fObj.select('select[name="brandno"]')[0].focus();
		return;
	}

	var msg = '';
	if (mode == 'link') msg += '선택한 상품에 해당 분류를 연결하시겠습니까?';
	else if (mode == 'move') msg += '선택한 상품을 해당 분류로 이동하시겠습니까?';
	else if (mode == 'copyGoodses') msg += '선택한 상품을 해당 분류로 복사하시겠습니까?';
	else if (mode == 'unlink') msg += '선택한 상품의 분류를 해제하시겠습니까?';
	else if (mode == 'delGoodses') msg += '선택한 상품을 정말 삭제하시겠습니까?' + "\n\n" + '[주의] 삭제 후에는 복원이 안되므로 신중하게 삭제하시기 바랍니다.';
	else if (mode == 'linkBrand') msg += '선택한 상품에 해당 브랜드를 연결하시겠습니까?';
	else if (mode == 'unlinkBrand') msg += '선택한 상품의 브랜드를 해제하시겠습니까?';
	if (!confirm(msg)) return;

	fObj.mode.value = mode;
	fObj.submit();
}
--></script>

<h2 class="title">빠른 이동/복사/삭제 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=15');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<!-- 상품출력조건 : start -->
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">

<table class="admin-form-table">
<tr>
	<th>분류선택</th>
	<td colspan="3">
	<script type="text/javascript" src="../../lib/js/categoryBox.js"></script>
	<script type="text/javascript">new categoryBox('cate[]',4,'<?=$searchCategory?>');</script>
	&nbsp;&nbsp;&nbsp;<a href="?unlink=Y"><img src="../img/btn_without_cate.gif" alt="미연결상품보기" align=absmiddle></a>
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
		&nbsp;&nbsp;&nbsp;<a href="?unbrand=Y"><img src="../img/btn_without_brand.gif" alt="미연결상품보기" align=absmiddle></a>
	</td>
</tr>
<tr>
	<th>상품가격</th>
	<td colspan="3">
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][0]?>" onkeydown="onlynumber()" size="15" class="ar"> 원 -
	<input type="text" name="goods_price[]" value="<?=$_GET[goods_price][1]?>" onkeydown="onlynumber()" size="15" class="ar"> 원
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
</table>
<div class=button_top><input type=image src="../img/btn_search2.gif"></div>

<div class="admin-list-toolbar">
	<div class="list-information">
		검색 <b><?=number_format($pg->recode['total'])?></b>개 / <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</div>

	<div class="list-tool">
	<ul>
		<li><img src="../img/sname_date.gif"><a href="javascript:nsAdminGoodsList.sort('regdt desc')"><img name="sort_regdt_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('regdt')"><img name="sort_regdt" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_product.gif"><a href="javascript:nsAdminGoodsList.sort('goodsnm desc')"><img name="sort_goodsnm_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goodsnm')"><img name="sort_goodsnm" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_price.gif"><a href="javascript:nsAdminGoodsList.sort('goods_price desc')"><img name="sort_goods_price_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('goods_price')"><img name="sort_goods_price" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_brand.gif"><a href="javascript:nsAdminGoodsList.sort('brandno desc')"><img name="sort_brandno_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('brandno')"><img name="sort_brandno" src="../img/list_down_off.gif"></a></li>
		<li class="separater"></li>
		<li><img src="../img/sname_company.gif"><a href="javascript:nsAdminGoodsList.sort('maker desc')"><img name="sort_maker_desc" src="../img/list_up_off.gif"></a><a href="javascript:nsAdminGoodsList.sort('maker')"><img name="sort_maker" src="../img/list_down_off.gif"></a></li>
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
<!-- 상품출력조건 : end -->

<form name="fmList" class="admin-form" method="post" onsubmit="return false" target="ifrmHidden" action="./indb_adm_goods_manage_move.php">
<input type=hidden name=mode>
<input type=hidden name=category value="<?=array_pop(array_notnull(Clib_Application::request()->get('cate')))?>">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:70px;">
	<col style="width:80px;">
	<col style="width:55px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">선택</a></th>
	<th>시스템상품코드</th>
	<th>상품명</th>
	<th>판매금액</th>
	<th>적립금</th>
	<th>판매재고</th>
	<th>브랜드</th>
	<th>등록일</th>
	<th>진열</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsList as $goods) {
?>
<tr class="ac">
	<td><input type="checkbox" name="chk[]" value="<?=$goods['goodsno']?>" ></td>
	<td><?=$goods->getReadableId()?> <br />(<?=$goods['goodscd']?>)</td>
	<td class="al">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$goods->getId()?>" target=_blank><?=goodsimg($goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>"><?=$goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>
	<td class="price"><?=number_format($goods->getPrice())?></td>
	<td><?=number_format($goods->getReserve())?></td>
	<td><?=number_format($goods->getStock())?></td>
	<td><?=$goods->brand->getBrandName()?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
</tr>
<tr class="info el-admin-goods-list-extra-info">
	<td colspan="9">
	<div class="admin-goods-list-category-wrap">
		<ul class="admin-goods-list-category">
		<? foreach($goods->getCategory() as $linkedCategory) { ?>
			<li><?=currPosition($linkedCategory['category'] , 1);?></li>
		<? } ?>
		</ul>
	</div>
	<div class="clear"></div>
	</td>
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

<table class="admin-form-table" style="margin:30px 0;">
<tr>
	<th>현재 검색 분류</th>
	<td>
	<div style="margin:5px 0; color:#627dce; font-weight:bold;">
<?php
	if (empty($searchCategory) === false) {
		echo currPosition($searchCategory,1);
	}
	else {
		echo "분류를 선택해서 검색해 주세요.";
	}
?>
	</div>
	</td>
</tr>
<tr>
	<th>분류연결</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품을 <script type="text/javascript">new categoryBox('sCate[]',4,'','','fmList');</script> 으로
	<a href="javascript:chkFormList('link')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="연결"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isToday" value="Y" <?=$checked[isToday]['Y']?>>해당 상품의 등록일을 현재 등록시간으로 변경합니다.
	</div>
	</td>
</tr>
<tr>
	<th>분류이동</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품을
	<script type="text/javascript">new categoryBox('mCate[]',4,'','','fmList');</script> 으로
	<a href="javascript:chkFormList('move')"><img src="../img/btn_cate_move.gif" align="absmiddle" alt="이동"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">※ 분류이동 시 해당 상품에 연결된 분류는 모두 해제되며, 새롭게 이동되는 분류만 연결됩니다.</font>
	</div>
	</td>
</tr>
<tr height=35>
	<th>분류해제</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품의 모든 분류를
	<a href="javascript:chkFormList('unlink')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="해제"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">※ 해당 상품에 연결된 분류가 모두 해제되므로 상품분류페이지에서 해당 상품이 조회되지 않습니다.</font>
	</div>
	</td>
</tr>
<tr>
	<th>브랜드연결</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품을 <?=$searchForm->getTag('brandno');?> 으로
	<a href="javascript:chkFormList('linkBrand')"><img src="../img/btn_cate_connect.gif" align="absmiddle" alt="연결"></a>
	<a href="javascript:chkFormList('unlinkBrand')"><img src="../img/btn_cate_unconnect.gif" align="absmiddle" alt="해제"></a>
	</div>
	</td>
</tr>
<tr height=35>
	<th>상품삭제</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품을 <a href="javascript:chkFormList('delGoodses')"><img src="../img/btn_cate_del.gif" align="absmiddle" alt="삭제"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">※ 신중하게 진행하세요. 버튼클릭시 선택한 상품들이 삭제됩니다. 삭제되면 복구되지 않습니다.</font>
	</div>
	</td>
</tr>
</table>

<table class="admin-form-table" style="margin:30px 0;">
<tr>
	<th>복사</th>
	<td>
	<div style="margin:5px 0">
	선택한 상품을 <script type="text/javascript">new categoryBox('ssCate[]',4,'','','fmList');</script> 으로
	<a href="javascript:chkFormList('copyGoodses')"><img src="../img/btn_cate_copy.gif" align="absmiddle" alt="복사"></a>
	</div>
	<div style="margin:5px 0" class="noline">
	<font class="extext">※ 복사의 경우에는 상품의 등록일이 무조건 현재시간으로 변경됩니다</font>
	</div>
	</td>
</tr>
</table>
</form>

<ul class="admin-simple-faq">
	<li>분류연결 : 상품에 분류(카테고리)를 연결하는 기능입니다.(다중분류기능지원)</li>
	<li>분류이동 : 현재 연결된 분류에서 다른 분류로 이동하는 기능입니다.</li>
	<li>분류해제 : 연결된 분류를 해제하는 기능입니다.</li>
	<li>상품복사 : 다른 분류로 똑같은 상품을 하나 더 복사(생성)하는 기능입니다.</li>
	<li>상품삭제 : 상품을 삭제하는 기능으로 삭제 후에는 복원이 안되므로 신중하게 삭제하시기 바랍니다.</li>
	<li>[주의] 상품복사 경우 상품문의/상품후기는 복사되지 않습니다.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
</script>

<? include "../_footer.php"; ?>