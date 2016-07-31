<?

//$hiddenLeft = 1;
$location = "상품일괄관리 > 빠른 아이콘 수정";
include "../_header.php";
include "../../lib/page.class.php";
include "../../conf/config.pay.php";
@include "../../conf/my_icon.php";

// 아이콘 갯수
$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
$cnt_myicon = sizeof($r_myicon);

// 아이콘쓰
$ar_icon = array('good_icon_new.gif','good_icon_recomm.gif','good_icon_special.gif','good_icon_popular.gif','good_icon_event.gif','good_icon_reserve.gif','good_icon_best.gif','good_icon_sale.gif');

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
);

// 아이콘
if (sizeof($_GET[sicon])) {
	if ($_GET[sicon][custom] == 1) {
		unset($_GET[sicon][custom]);
		$_max = sizeof($r_myicon);

		$checked[sicon][custom] = "checked";
	}
	else {
		unset($_GET[sicon][custom]);
		$_max = 8;
	}

	$tmp = array();

	for ($i=0;$i<$_max;$i++) {
		if ($_GET[sicon][$i] > 0) {
			$checked[sicon][$i] = "checked";
			$_bit = pow(2,$i);
			$tmp[] = $_bit;
		}
	}

	if (!empty($tmp)) {
		$params['sicon'] = $tmp;
	}

}

// 상품 목록
$goodsList = $goodsHelper->getGoodsCollection($params);

// 페이징
$pg = $goodsList->getPaging();

// 상품 검색 폼
$searchForm = Clib_Application::form('admin_goods_search')->setData(Clib_Application::request()->gets('get'));


?>
<link rel="stylesheet" type="text/css" href="./css/css.css">
<script type="text/javascript" src="../js/adm_form.js"></script>
<script type="text/javascript" src="./js/goods_list.js"></script>
<script type="text/javascript">
var nsMultiIconSet = function() {
	return {
		ar_icons : <?=gd_json_encode($r_myicon)?>
		,

		// 되돌리기
		restore : function() {

			var self = this;

			// 기본 세트
			for (var i=0;i<8 ;i++ )
			{
				$$('.el-checkbox-icon-' + i).each(function(el) {
					el.checked = (el.readAttribute('o_checked') == 'checked') ? true : false;
				});
			}

			// 추가 아이콘
			var oval,img,div,fld,del,icon,goodsno,_id;
			var cnt_myicon = self.ar_icons.length;

			var btn_x = new Element('img', {src:'../img/btn_x.gif'});

			$$('input[name="chk[]"]').each(function(el){
				goodsno = el.value;

				fld = $$('input[name="customicon['+goodsno+']"]')[0];
				oval = parseInt(fld.getAttribute('o_value'));

				if (parseInt(fld.value) != oval && oval > 0) {

					div = $$('.el-custom-icon-' + goodsno)[0];
					div.update('');
					fld.value = oval;

					for (i=8;i<cnt_myicon;i++) {

						_bit = Math.pow(2,i);

						if (self.ar_icons[i] && (oval & _bit) > 0) {
							// 아이콘 출력

							_id = "el-custom-icon-" + goodsno + "-" + _bit;

							del = Element.clone(btn_x);

							del.writeAttribute('goodsno',goodsno);
							del.writeAttribute('icon',_bit);
							del.writeAttribute('p_id',_id);

							del.observe('click', nsMultiIconSet.del);

							img = new Element('img',{src:'../../data/my_icon/'+self.ar_icons[i]});
							icon = new Element('p', {id:_id,style:'padding:0;margin:5px'});

							icon.insert({ bottom:img });
							icon.insert({ bottom:'&nbsp;' });
							icon.insert({ bottom:del });

							div.insert({bottom:icon});

						}
					}
				}
			});
		}
		,
		// 추가 아이콘 설정
		set : function() {

			var img,div,fld,del,icon,goodsno,_id;
			var val = 0;
			var icons = new Array;

			var custom_icons = $$('input[name="custom_icon[]"]:checked');
			if (!custom_icons.length)
			{
				alert('추가할 아이콘을 선택하세요.');
				return false;
			}

			var chks = $$('input[name="chk[]"]:checked');
			if (!chks.length)
			{
				alert('아이콘을 추가할 상품을 선택하세요.');
				return false;
			}

			var btn_x = new Element('img', {src:'../img/btn_x.gif'});

			chks.each(function(el){

				goodsno = el.value;

				div = $$('.el-custom-icon-' + goodsno)[0];
				div.update('');

				fld = $$('input[name="customicon['+goodsno+']"]')[0];

				val = 0;

				custom_icons.each(function(el) {

					val = parseInt(val) + parseInt(el.value);

					_id = "el-custom-icon-" + goodsno + "-" + el.value;

					del = Element.clone(btn_x);

					del.writeAttribute('goodsno',goodsno);
					del.writeAttribute('icon',el.value);
					del.writeAttribute('p_id',_id);

					del.observe('click', nsMultiIconSet.del);

					img = Element.clone(el.next('img'));

					icon = new Element('p', {id:_id,style:'padding:0;margin:5px'});

					icon.insert({ bottom:img });
					icon.insert({ bottom:'&nbsp;' });
					icon.insert({ bottom:del });
					div.insert({bottom:icon});

				});

				fld.value = val;

				el.checked = false;

			});
		}
		,
		// 추가 아이콘 삭제
		del : function(e) {

			var el = typeof e.type != 'undefined' ? e.srcElement : e;
			fld = $$('input[name="customicon['+el.getAttribute('goodsno')+']"]')[0];
			fld.value = parseInt(fld.value) - parseInt(el.getAttribute('icon'));
			$(el.getAttribute('p_id')).remove();

		}
		,
		// 사용자 아이콘 일괄 삭제
		cs_del : function() {

			var self = this;

			var chks = $$('input[name="chk[]"]:checked');
			if (!chks.length)
			{
				alert('삭제할 상품을 선택하세요.');
				return false;
			}

			if (confirm('선택된 상품의 사용자 아이콘을 삭제하시겠습니까?'))
			{
				// 추가 아이콘
				var div,fld,goodsno;

				chks.each(function(el) {

					goodsno = el.value;

					fld = $$('input[name="customicon['+goodsno+']"]')[0];
					fld.value = 0;

					div = $$('.el-custom-icon-' + goodsno)[0];
					div.update('');

				});

			}

		}
		,
		// 페이지내 모든 상품 아이콘 지정 (기본 세트만)
		multiset : function (id) {
			var idx = 0;
			var bool = true;
			$$('.el-checkbox-icon-' + id).each(function(el){

				if (idx == 0)
				{
					if (el.checked == false) bool = true;
					else bool = false;
				}

				el.checked = bool;

				idx++;
			});
		}

	}
}();

function fnToggleCustomIconSearchForm(c) {

	if (c.checked == true)
		$('el-customicon-search-form').setStyle({display:'block'});
	else
		$('el-customicon-search-form').setStyle({display:'none'});

}

</script>

<h2 class="title">빠른 아이콘 수정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=34');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

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
<tr>
	<th>아이콘</th>
	<td colspan="3" class="noline">
	<?
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $icon = "../../data/my_icon/".$r_myicon[$i];
		else $icon = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];

	?>
	<input type="checkbox" name="sicon[<?=$i?>]" value="<?=(pow(2,$i))?>" <?=$checked[sicon][$i]?>>
	<img src="<?=$icon?>">
	<? } ?>
	<input type="checkbox" name="sicon[custom]" value="1" onclick="fnToggleCustomIconSearchForm(this)" <?=$checked[sicon][custom]?>><font class=extext>사용자 아이콘</font>

	<div id="el-customicon-search-form" style="display:<?=$checked[sicon][custom] ? 'block' : 'none'?>;padding:0;">
		<ul style="margin:0;padding:0;">
			<? for ($i=8;$i<$cnt_myicon;$i++) { ?><? if($r_myicon[$i]) { ?>
			<li class="noline" style="float:left;padding:0 3px 0 0;"><input type="checkbox" name="sicon[<?=$i?>]" value="<?=(pow(2,$i))?>" <?=$checked[sicon][$i]?>><img src="../../data/my_icon/<?=$r_myicon[$i]?>"></li>
			<? } } ?>
		</ul>
		<div style="clear:both;"></div>
	</div>

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

<form method="post" class="admin-form" action="./indb_adm_goods_manage_icon.php" target="ifrmHidden">

<table class="admin-list-table">
<colgroup>
	<col style="width:35px;">
	<col style="width:100px;">
	<col >
	<col style="width:55px;">
	<col style="width:55px;">
	<col style="width:80px;">
	<col style="width:55px;">
	<? for($i=0;$i<8;$i++){ ?>
	<col style="width:40px;">
	<? } ?>
	<col style="width:80px;">
</colgroup>
<thead>
<tr>
	<th><a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')" class="white">선택</a></th>
	<th>시스템상품코드</th>
	<th>상품명</th>
	<th>판매금액</th>
	<th>판매재고</th>
	<th>등록일</th>
	<th>진열여부</th>
	<?
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $img = "../../data/my_icon/".$r_myicon[$i];
		else $img = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];
	?>
	<th><a href="javascript:void(0);" onclick="nsMultiIconSet.multiset(<?=$i?>)"><img src="<?=$img?>"></a></th>
	<? } ?>
	<th>사용자아이콘</th>
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
	<td><?=number_format($goods->getStock())?></td>
	<td><?=Core::helper('date')->format($goods['regdt'],'Y-m-d')?></td>
	<td><img src="../img/icn_<?=$goods[open]?>.gif"></td>
	<?
	$o_icon = 0;
	for($i=0;$i<8;$i++){
		if($r_myicon[$i]) $icon = "../../data/my_icon/".$r_myicon[$i];
		else $icon = "../../data/skin/".$cfg[tplSkin]."/img/icon/".$ar_icon[$i];
//
		$icon_use = ($goods[icon] & pow(2,$i)) > 0 ? true : false;
		$o_icon += $icon_use ? pow(2,$i) : 0;
	?>
	<td><!--img src="<?=$icon?>"--><input type="checkbox" name="icon[<?=$goods[goodsno]?>][<?=$i?>]" class="el-checkbox-icon-<?=$i?>" value="<?=(pow(2,$i))?>" o_checked="<?=$icon_use ? 'checked' : ''?>" <?=$icon_use ? 'checked' : ''?>></td>
	<? } ?>
	<td>
	<input type="hidden" name="customicon[<?=$goods[goodsno]?>]" value="<?=($goods[icon] - $o_icon)?>" o_value="<?=($goods[icon] - $o_icon)?>">
	<div class="el-custom-icon-<?=$goods[goodsno]?>">
	<? for ($i=8;$i<$cnt_myicon;$i++) {
		$_bit = pow(2,$i);
		if($r_myicon[$i] && ($goods[icon] & $_bit) > 0) { ?>
	<p style="padding:0;margin:5px" id="el-custom-icon-<?=$goods[goodsno]?>-<?=$_bit?>">
	<img src="../../data/my_icon/<?=$r_myicon[$i]?>"> <img src="../img/btn_x.gif" p_id="el-custom-icon-<?=$goods[goodsno]?>-<?=$_bit?>" goodsno="<?=$goods[goodsno]?>" icon="<?=$_bit?>" onclick="nsMultiIconSet.del(this);">
	</p>
	<?
		}
	}
	?>
	</div>
	</td>
</tr>
<? } ?>
</tbody>
</table>

<div class="admin-list-toolbar">
	<div class="left-buttons">
		<a href="javascript:void(0)" onclick="chkBox(document.getElementsByName('chk[]'),'rev')"><img src="../img/btn_allchoice.gif"></a>
	</div>
	<div class="right-buttons">
		<a href="javascript:void(0);" onclick="nsMultiIconSet.cs_del();"><img src="../img/admin_btn_user_delet.gif"></a>
	</div>
	<div class="paging"><?=$pg->page['navi']?></div>
</div>

<fieldset style="padding:10px;"><legend> 사용자 아이콘 </legend>
	<ul style="margin:0;padding:0;">
		<? for ($i=8;$i<$cnt_myicon;$i++) { ?><? if($r_myicon[$i]) { ?>
		<li class="noline" style="float:left;padding:3px;"><input type="checkbox" name="custom_icon[]" value="<?=(pow(2,$i))?>"><img src="../../data/my_icon/<?=$r_myicon[$i]?>"></li>
		<? } } ?>
	</ul>

	<div style="clear:both;"></div>

	<div style="text-align:center;border-top:1px solid #DCD8D6;padding-top:10px;">

		<div style="display:inline;padding:5px;"><a href="javascript:void(0);" onclick="nsMultiIconSet.set();"><img src="../img/admin_btn_user_icon.gif" align=absmiddle></a></div>
		<div style="display:inline;padding:5px;"><a href="javascript:popup('popup.myicon.php',510,550)"><img src="../img/admin_btn_user_icon01.gif" align=absmiddle></a></font>
	</div>
	</div>

</fieldset>

<div class=button_top>
<a href="javascript:void(0);" onclick="nsMultiIconSet.restore();"><img src="../img/admin_btn_refresh.gif"></a>
<input type=image src="../img/admin_btn_re01.gif">
</div>
</form>

<ul class="admin-simple-faq">
	<li>진열페이지에 노출되는 아이콘 개수는 최대 7개입니다. </li>
	<li>등록 가능한 아이콘 개수는 최대 30개 입니다.</li>
	<li>8개의 아이콘을 제외한 추가 아이콘은 사용자 아이콘 목록에서 선택, 적용하세요. </li>
	<li>추가 적용된 사용자 아이콘은 list의 사용자 아이콘에 표시됩니다.</li>
	<li>[원래대로] 클릭후, 반드시 [수정] 버튼 클릭하여 설정을 완료 하여 주세요.</li>
</ul>

<script type="text/javascript">
// onload events
Event.observe(document, 'dom:loaded', function(){
	nsAdminGoodsList.sortInit('<?=Clib_Application::request()->get('sort')?>');
	nsAdminForm.init($('el-admin-goods-search-form'));
});
</script>

<? include "../_footer.php"; ?>
