<?

$location = "상품일괄관리 > 빠른 판매가격 수정";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/design_main.$cfg[tplSkin].php";

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

	'indicate' => Clib_Application::request()->get('indicate'),
	'smain' => Clib_Application::request()->get('smain'),
	'sevent' => Clib_Application::request()->get('sevent'),

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
<script type="text/javascript"><!--
function __search(type) {

	var f = document.frmList;

	if (type) {
		f.indicate.value = type;
	}
	else {
		f.indicate.value = '';
	}

	f.submit();

}

function chkFormList(fObj){

	if (chkForm(fObj) === false) return false;
	if (fObj['pmmark'].value == '원' && chkPatten(fObj['pmnum'],'regNum') === false) return false;
	if (fObj['isall'].checked === false && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}

	var msg = '';
	msg += fObj['dtprice'].options[fObj['dtprice'].selectedIndex].text;
	msg += '의 ' + fObj['pmnum'].value + fObj['pmmark'].options[fObj['pmmark'].selectedIndex].text;
	msg += '를 ' + fObj['pmtype'].options[fObj['pmtype'].selectedIndex].text;
	msg += '된 가격으로 ' + fObj['tgprice'].options[fObj['tgprice'].selectedIndex].text;
	msg += '을 ' + fObj['roundunit'].value;
	msg += '원 단위로 ' + fObj['roundtype'].options[fObj['roundtype'].selectedIndex].text;
	msg += '하여 일괄적으로 수정하시겠습니까?';
	msg += "\n\n" + '[주의] 일괄적용 후에는 이전상태로 복원이 안되므로 신중하게 변경하시기 바랍니다.';
	if (!confirm(msg)) return false;

	return true;
}

/*** 이벤트목록 요청 ***/
function getEventList(sobj, selValue)
{
	if (sobj.options[sobj.selectedIndex].getAttribute("call") == null) return;
	function setcallopt(idx, text, value, defaultSelected, selected, call){
		if (idx == 0) for (i = sobj.options.length; i > 0; i--) sobj.remove(i);
		sobj.options[idx] = new Option(text, value, defaultSelected, selected);
		if (call != null) sobj.options[idx].setAttribute('call', call);
	}
	var ajax = new Ajax.Request( "../goods/indb.php",
	{
		method: "post",
		parameters: "mode=getEvent&page=" + sobj.options[sobj.selectedIndex].getAttribute("call") + "&selValue=" + (selValue != null ? selValue : ''),
		onLoading: function (){ setcallopt(0, '== 로 딩 중 ... ==', ''); },
		onComplete: function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var jsonData = eval( '(' + req.responseText + ')' );
				var lists = jsonData.lists;
				var page = jsonData.page;
				var idx = 0;
				if (page.prev != null) setcallopt(idx++, '☞ 처음목록보기', '', false, false, '1');
				if (page.prev != null) setcallopt(idx++, '☞ 이전목록보기', '', false, false, page.prev);
				if (lists.length == 0) setcallopt(idx++, '== 이벤트가 없습니다 ==', '', false, false);
				for (i = 0; i < lists.length; i++){
					if (i == 0 || (selValue != null && selValue == lists[i].sno)) selected = true; else selected = false;
					setcallopt(idx++, '[' + lists[i].sdate + ' ~ ' + lists[i].edate + '] ' + lists[i].subject, lists[i].sno, false, selected);
				}
				if (page.next != null) setcallopt(idx++, '☞ 다음목록보기', '', false, false, page.next);
				sobj.form['seventpage'].value = page.now;
			}
			else {
				setcallopt(0, '☞ 로딩 시작하기', '', false, false, '1');
				setcallopt(1, '[로딩실패] 재로딩하세요.', '', true, true);
			}
		}
	} );
}
--></script>

<h2 class="title">빠른 판매가격 수정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=13');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<!-- 상품출력조건 : start -->
<form class="admin-form" method="get" name="frmList" id="el-admin-goods-search-form">
<input type="hidden" name="sort" value="<?=Clib_Application::request()->get('sort')?>">
<input type="hidden" name="indicate" value="<?=Clib_Application::request()->get('indicate')?>">

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
</table>

<div class="button_top"><input type="image" src="../img/btn_search2.gif" onclick="__search();return false;"></div>

<table class="nude">
<tr>
	<td>
	<!-- 메인상품 -->
	<table class="admin-form-table">
	<tr>
		<th>메인상품</th>
		<td class="vt">
			<select name="smain">
			<?
			$displayAreaKeys = array_keys($cfg_step);
			for ($i=0, $m=sizeof($displayAreaKeys);$i<$m;$i++) {
				$key = $displayAreaKeys[$i];
			?>
			<option value="<?=$key?>" <?=($key == Clib_Application::request()->get('smain')) ? 'selected' : ''?>><?=$cfg_step[$key]['title']?>
			<? } ?>
			</select>

			<a href="javascript:void(0);" onclick="__search('main'); return false;"><img src="../img/buttons/btn_go.gif"></a>

			<p class="help">
			(메인페이지에 진열된 상품들을 출력합니다)
			</p>
		</td>
	</tr>
	</table>
	<!-- 메인상품 -->
	</td>
	<td class="vt">
	<!-- 이벤트 상품 -->
	<table class="admin-form-table">
	<tr>
		<th>이벤트</th>
		<td>
			<select name="sevent" onchange="getEventList(this)">
			<option value="" call="<?=$_GET[seventpage]?>">☞ 로딩 시작하기</option>
			</select>
			<input type="hidden" name="seventpage">

			<a href="javascript:void(0);" onclick="__search('event'); return false;"><img src="../img/buttons/btn_go.gif"></a>

			<p class="help">
			(이벤트상품으로 선정해놓은 상품들을 출력합니다)
			</p>
		</td>
	</tr>
	</table>
	<!-- 이벤트 상품 -->
	</td>
</tr>
</table>

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
<!-- 상품출력조건 : end -->

<form name="fmList" class="admin-form" method="post" action="indb_adm_goods_manage_price.php" target="ifrmHidden" onsubmit="return chkFormList(this)">

<input type="hidden" name="query" value="<?=base64_encode(substr($pg->query,0,stripos($pg->query,"limit")))?>" required msgR="일괄관리 할 상품을 먼저 검색하세요.">


<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:70px;">
	<col >
	<col style="width:80px;">
	<col style="width:80px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:80px;">
	<col style="width:40px;">
	<col style="width:40px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">선택</a></th>
	<th>번호</th>
	<th>상품명</th>
	<th>옵션1</th>
	<th>옵션2</th>
	<th>판매금액</th>
	<th>소비자가</th>
	<th>적립금</th>
	<th>판매재고</th>
	<th>등록일</th>
	<th>상품<br/>진열</th>
	<th>옵션<br/>출력</th>
</tr>
</thead>
<tbody>
<?
foreach ($goodsOptionList as $option) {
?>
<tr class="ac">
	<td><input type="checkbox" name="chk[]" value="<?=$option->getId()?>"></td>
	<td><?=$pg->idx--?></td>
	<td class="al">
		<div>
			<a href="../../goods/goods_view.php?goodsno=<?=$option->goods->getId()?>" target=_blank><?=goodsimg($option->goods[img_s],40,'style="vertical-align:middle;border:1px solid #e9e9e9;"',1)?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$option->goods->getId()?>"><?=$option->goods->getGoodsName()?></a>
			<a href="adm_goods_form.php?mode=modify&goodsno=<?=$option->goods->getId()?>" onclick="nsAdminGoodsList.edit('<?=$option->goods->getId()?>');return false;"><img src="../img/icon_popup.gif"></a>
		</div>
	</td>
	<td><?=$option->getOpt1()?></td>
	<td><?=$option->getOpt2()?></td>
	<td class="price"><?=number_format($option->getPrice())?></td>
	<td class="price"><?=number_format($option->getConsumer())?></td>
	<td><?=number_format($option->getReserve())?></td>
	<td><?=number_format($option->getStock())?></td>
	<td><?=Core::helper('date')->format($option->goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$option->goods[open]?>.gif"></td>
	<td><img src="../img/icn_<?=$option[go_is_display]?>.gif"></td>
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

<table class="admin-form-table">
<tr>
	<th>가격조건설정</th>
	<td>
	<div style="margin:5px 0">
		<select name="dtprice">
		<option value="price" <?=$selected[dtprice][price]?>>판매가</option>
		<option value="consumer" <?=$selected[dtprice][consumer]?>>소비자가</option>
		<option value="supply" <?=$selected[dtprice][supply]?>>매입가</option>
		</select> 에서
		<input type="text" name="pmnum" value="<?=$_GET[pmnum]?>" required label="계산수치" option="/^[0-9]+(\.[0-9]){0,1}$/" size="4" class="ar" style="width:55px">
		<select name="pmmark">
		<option value="원" <?=$selected[pmmark]['원']?>>원</option>
		<option value="%" <?=$selected[pmmark]['%']?>>%</option>
		</select> 을
		<select name="pmtype">
		<option value="down" <?=$selected[pmtype][down]?>>할인</option>
		<option value="up" <?=$selected[pmtype][up]?>>할증</option>
		</select>	된 가격으로
		<select name="tgprice">
		<option value="price" <?=$selected[tgprice][price]?>>판매가</option>
		<option value="consumer" <?=$selected[tgprice][consumer]?>>소비자가</option>
		</select> 를 일괄적으로 수정합니다.
		</div>
		<div style="margin:5px 0">
		수정되는 가격의 단위는
		<select name="roundunit">
		<option value="1" <?=$selected[roundunit][1]?>>1</option>
		<option value="10" <?=$selected[roundunit][10]?>>10</option>
		<option value="100" <?=$selected[roundunit][100]?>>100</option>
		<option value="1000" <?=$selected[roundunit][1000]?>>1000</option>
		</select> 원 단위로
		<select name="roundtype">
		<option value="down" <?=$selected[roundtype][down]?>>내림</option>
		<option value="halfup" <?=$selected[roundtype][halfup]?>>반올림</option>
		<option value="up" <?=$selected[roundtype][up]?>>올림</option>
		</select> 하여 수정합니다.

		<p class="help">
			가격 단위는 아래와 같이 연산됩니다.
		</p>
		<dl class="help" style="margin-left:10px;">
			<dt>1원 단위</dt>
			<dd>소수점 자리 절삭</dd>

			<dt>10원 단위</dt>
			<dd>1원 자리 절삭</dd>

			<dt>100원 단위</dt>
			<dd>10원 자리 절삭</dd>

			<dt>1000원 단위</dt>
			<dd>100원 자리 절삭</dd>
		</dl>

	</div>
	<div style="margin:5px 0" class="noline">
	<input type="checkbox" name="isall" value="Y" <?=$checked[isall]['Y']?>>검색된 상품 전체<?=($pg->recode[total]?"({$pg->recode[total]}개)":"")?>를 수정합니다. <span class=help>(상품수가 많은 경우 비권장합니다. 가능하면 한 페이지씩 선택하여 수정하세요)</span>
	</div>
	</td>
</tr>
</table>

<div class=button_top><input type=image src="../img/btn_modify.gif"></div>

</form>

<ul class="admin-simple-faq">
	<li>일괄수정 할 상품을 검색 후 상품가격을 일괄처리 조건에 맞춰 적용합니다.</li>
	<li>[주의1] 일괄수정 이후에는 <b>이전상태로 복원이 안되므로 신중하게 변경하시기 바랍니다.</b></li>
	<li>[주의2] 서버 부하등 안정적인 서비스를 위해서 수정할 상품이 많은 경우에는 검색된 상품 전체수정은 피하시기 바랍니다.</li>
	<li>할인/할증율(%) : 소수점 첫째자리까지 입력하실 수 있습니다. [예] 5.5% 할인(가능), 5.5% 할증(가능), 5.55% 할인(불가능)</li>
	<li><b>[가격수정 예제]</b></li>
	<li>판매가의 5.5% 할인된 가격으로 판매가를 일괄적으로 수정하고, 가격 단위는 100원 단위로 내림하여 수정한다면,</li>
	<li>판매가 10,000원인 상품의 계산식은 다음과 같습니다.</li>
	<li>⇒ 10,000 × (1 - (5.5 / 100)) = 9,450원이며,</li>
	<li>⇒ 100원 단위 내림하면 9,400원 으로 최종 가격수정이 됩니다.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
	getEventList(frmList.sevent, '<?=$_GET[sevent]?>');
});
</script>

<? include "../_footer.php"; ?>
